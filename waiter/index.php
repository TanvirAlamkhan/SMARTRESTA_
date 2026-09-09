<?php
/**
 * SMARTRESTA — Waiter Portal Entrypoint
 * Route: /waiter/ or /waiter/index.php
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Router.php';

Router::authorizePortal('waiter');

$initialSection = 'pos';
require_once __DIR__ . '/../index.php';
