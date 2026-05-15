<?php

/**
 * Plugin Name:       Wise Widget Manager
 * Description:       Centralized WordPress widget manager for classic themes with drag & drop ordering, live preview, and full sidebar control.
 * Version:           1.0.0
 * Author:            bisnusnr
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wise-widget-manager
 * Requires at least: 6.0
 * Requires PHP:      7.4
 *
 * @package           wisewidgetmanager
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
