<?php
/**
 * SMARTRESTA Core Dining Session Engine
 * Handles atomic session creation, concurrency locks, table transfers, and session closures.
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/AuditLogger.php';
require_once __DIR__ . '/../config/constants.php';

class DiningSessionEngine {
    
    /**
     * Open a new active dining session on a table.
     * Uses atomic transactions and row locking (FOR UPDATE) to prevent concurrency duplicate sessions.
     */
    public static function openSession($tableId, $guestCount, $openedByUserId, $customerId = null, $notes = null) {
        $pdo = Database::getConnection();
        if (!$pdo) {
            throw new Exception("Database connection unavailable.");
        }

        Database::beginTransaction();

        try {
            // Lock table row to check current operational state atomically
            $table = DB::fetch("
                SELECT id, table_number, capacity, status 
                FROM restaurant_tables 
                WHERE id = ? FOR UPDATE
            ", [$tableId]);

            if (!$table) {
                throw new Exception("Restaurant Table #{$tableId} not found.");
            }

            // Verify Table Operational State
            if ($table['status'] === TABLE_STATUS_OCCUPIED) {
                throw new Exception("Table {$table['table_number']} is currently OCCUPIED with an active session.");
            }

            if ($table['status'] === TABLE_STATUS_OUT_OF_SERVICE) {
                throw new Exception("Table {$table['table_number']} is currently OUT OF SERVICE.");
            }

            // Verify Concurrency: Check if an OPEN session already exists on table
            $existingSession = DB::fetch("
                SELECT id FROM dining_sessions 
                WHERE table_id = ? AND status = 'OPEN' 
                FOR UPDATE
            ", [$tableId]);

            if ($existingSession) {
                throw new Exception("Concurrency Error: Table {$table['table_number']} already has an active dining session.");
            }

            // Validate Guest Count
            $guestCount = (int)$guestCount;
            if ($guestCount <= 0) {
                throw new Exception("Guest count must be greater than zero.");
            }

            // Create Active Dining Session
            $sessionId = DB::insert("
                INSERT INTO dining_sessions (table_id, customer_id, opened_by_user_id, guest_count, status, opened_at, notes)
                VALUES (?, ?, ?, ?, 'OPEN', NOW(), ?)
            ", [
                $tableId,
                $customerId ?: null,
                $openedByUserId,
                $guestCount,
                $notes ?: null
            ]);

            // Update Table State to OCCUPIED
            DB::execute("
                UPDATE restaurant_tables 
                SET status = ? 
                WHERE id = ?
            ", [TABLE_STATUS_OCCUPIED, $tableId]);

            // Audit Event Log
            AuditLogger::log('DINING_SESSION_CREATED', 'DiningSessions', $sessionId, null, [
                'table_number' => $table['table_number'],
                'guest_count' => $guestCount,
                'opened_by' => $openedByUserId
            ], $openedByUserId);

            Database::commit();

            return [
                'session_id' => $sessionId,
                'table_id' => $tableId,
                'table_number' => $table['table_number'],
                'guest_count' => $guestCount,
                'status' => 'OPEN',
                'opened_at' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    /**
     * Transfer an active dining session from source_table to destination_table.
     * Uses double row locking on both source and destination tables.
     */
    public static function transferSession($sessionId, $destinationTableId, $userId) {
        $pdo = Database::getConnection();
        if (!$pdo) {
            throw new Exception("Database connection unavailable.");
        }

        Database::beginTransaction();

        try {
            // Lock Active Dining Session
            $session = DB::fetch("
                SELECT id, table_id, status 
                FROM dining_sessions 
                WHERE id = ? AND status = 'OPEN' 
                FOR UPDATE
            ", [$sessionId]);

            if (!$session) {
                throw new Exception("Active dining session #{$sessionId} not found.");
            }

            $sourceTableId = $session['table_id'];
            if ((int)$sourceTableId === (int)$destinationTableId) {
                throw new Exception("Source and destination tables must be different.");
            }

            // Lock Source and Destination Tables
            $sourceTable = DB::fetch("SELECT id, table_number FROM restaurant_tables WHERE id = ? FOR UPDATE", [$sourceTableId]);
            $destTable = DB::fetch("SELECT id, table_number, status FROM restaurant_tables WHERE id = ? FOR UPDATE", [$destinationTableId]);

            if (!$destTable) {
                throw new Exception("Destination table #{$destinationTableId} not found.");
            }

            if ($destTable['status'] === TABLE_STATUS_OCCUPIED) {
                throw new Exception("Destination Table {$destTable['table_number']} is currently OCCUPIED.");
            }

            if ($destTable['status'] === TABLE_STATUS_OUT_OF_SERVICE) {
                throw new Exception("Destination Table {$destTable['table_number']} is OUT OF SERVICE.");
            }

            // Update Session Table ID Reference
            DB::execute("UPDATE dining_sessions SET table_id = ? WHERE id = ?", [$destinationTableId, $sessionId]);

            // Update Orders table_id reference for orders in this session
            DB::execute("UPDATE orders SET table_id = ? WHERE dining_session_id = ?", [$destinationTableId, $sessionId]);

            // Set Source Table AVAILABLE & Destination Table OCCUPIED
            DB::execute("UPDATE restaurant_tables SET status = ? WHERE id = ?", [TABLE_STATUS_AVAILABLE, $sourceTableId]);
            DB::execute("UPDATE restaurant_tables SET status = ? WHERE id = ?", [TABLE_STATUS_OCCUPIED, $destinationTableId]);

            // Operational Audit Log
            AuditLogger::log('DINING_SESSION_TRANSFERRED', 'DiningSessions', $sessionId, [
                'from_table' => $sourceTable['table_number']
            ], [
                'to_table' => $destTable['table_number']
            ], $userId);

            Database::commit();

            return [
                'session_id' => $sessionId,
                'from_table' => $sourceTable['table_number'],
                'to_table' => $destTable['table_number']
            ];
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }

    /**
     * Close an active dining session.
     * Sets session CLOSED and resets table state to AVAILABLE.
     */
    public static function closeSession($sessionId, $userId) {
        $pdo = Database::getConnection();
        if (!$pdo) {
            throw new Exception("Database connection unavailable.");
        }

        Database::beginTransaction();

        try {
            $session = DB::fetch("
                SELECT id, table_id, status 
                FROM dining_sessions 
                WHERE id = ? FOR UPDATE
            ", [$sessionId]);

            if (!$session) {
                throw new Exception("Dining session #{$sessionId} not found.");
            }

            if ($session['status'] === 'CLOSED') {
                throw new Exception("Dining session #{$sessionId} is already closed.");
            }

            $tableId = $session['table_id'];

            // Update Session Status
            DB::execute("
                UPDATE dining_sessions 
                SET status = 'CLOSED', closed_at = NOW() 
                WHERE id = ?
            ", [$sessionId]);

            // Set Table AVAILABLE
            DB::execute("
                UPDATE restaurant_tables 
                SET status = ? 
                WHERE id = ?
            ", [TABLE_STATUS_AVAILABLE, $tableId]);

            // Operational Audit Log
            AuditLogger::log('DINING_SESSION_CLOSED', 'DiningSessions', $sessionId, null, null, $userId);

            Database::commit();

            return [
                'session_id' => $sessionId,
                'table_id' => $tableId,
                'status' => 'CLOSED',
                'closed_at' => date('Y-m-d H:i:s')
            ];
        } catch (Exception $e) {
            Database::rollBack();
            throw $e;
        }
    }
}
