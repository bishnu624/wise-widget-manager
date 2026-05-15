<?php

namespace WiseWidgetManager;

use WiseWidgetManager\Admin\Wisema_Admin_Menu;
use WiseWidgetManager\Admin\Wisema_Admin_Assets;
use WiseWidgetManager\Admin\Wisema_Api;



if (! defined('ABSPATH')) exit;
class Plugin
{

  public function init()
  {

    self::WiseWidgetloadAdmin();
  }

  private static function WiseWidgetloadAdmin()
  {
    $modules = [
      Wisema_Admin_Menu::class,
      Wisema_Admin_Assets::class,
      Wisema_Api::class,

    ];

    foreach ($modules as $module) {
      (new $module())->init();
    }
  }
}
