<?php
/**
 * SMARTRESTA — Kitchen / Counter Portal Entrypoint
 * Route: /kitchen/ or /kitchen/index.php
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Router.php';

Router::authorizePortal('kitchen');

$initialSection = 'kds';
require_once __DIR__ . '/../index.php';
