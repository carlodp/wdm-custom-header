<?php
/**
 * Minimal WordPress configuration for testing
 */

// Database settings (not used in this test environment)
define('DB_NAME', 'wordpress');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// WordPress directory paths
define('ABSPATH', __DIR__ . '/');
define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
define('WPINC', 'wp-includes');

// Security keys (demo values)
define('AUTH_KEY',         'demo-key');
define('SECURE_AUTH_KEY',  'demo-key');
define('LOGGED_IN_KEY',    'demo-key');
define('NONCE_KEY',        'demo-key');
define('AUTH_SALT',        'demo-salt');
define('SECURE_AUTH_SALT', 'demo-salt');
define('LOGGED_IN_SALT',   'demo-salt');
define('NONCE_SALT',       'demo-salt');

// WordPress table prefix
$table_prefix = 'wp_';

// Debug mode
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Admin settings
define('WP_ADMIN', true);

// Load WordPress
require_once ABSPATH . 'wp-settings.php';