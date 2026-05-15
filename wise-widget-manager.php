<?php

/**
 * Plugin Name:       Wise Widget Manager
 * Plugin URI:        https://github.com/bishnu624/wise-widget-manager
 * Description:       Centralized WordPress widget manager for classic themes with enable/disable controls and sidebar management.
 * Version:           1.0.1
 * Author:            Bishnu Sunar
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wise-widget-manager
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      7.4
 *
 * @package           WiseWidgetManager
 */

if (!defined('ABSPATH')) exit;

define('WISEWIMA_URL', plugin_dir_url(__FILE__));
define('WISEWIMA_PATH', plugin_dir_path(__FILE__));

// Load Composer autoload
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/helpers.php';

use WiseWidgetManager\Plugin;

function wisewima_boot()
{
  $plugin = new Plugin();
  $plugin->init();
}

wisewima_boot();
