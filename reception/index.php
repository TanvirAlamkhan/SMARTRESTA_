<?php
/**
 * SMARTRESTA — Reception / Cashier Portal Entrypoint
 * Route: /reception/ or /reception/index.php
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Router.php';

Router::authorizePortal('reception');

$initialSection = 'reception';
require_once __DIR__ . '/../index.php';
