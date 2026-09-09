<?php
/**
 * SMARTRESTA — Manager Portal Entrypoint
 * Route: /manager/ or /manager/index.php
 */

require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Router.php';

Router::authorizePortal('manager');

$initialSection = 'admin';
require_once __DIR__ . '/../index.php';
