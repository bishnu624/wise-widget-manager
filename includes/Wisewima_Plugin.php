<?php

namespace WiseWidgetManager;

use WiseWidgetManager\Admin\Wisewima_Admin;
use WiseWidgetManager\Admin\Wisewima_Assets;
use WiseWidgetManager\Admin\Wisewima_Api;



if (! defined('ABSPATH')) exit;
class Wisewima_Plugin
{

  public function init()
  {

    self::wisewima_loadadmin();
  }

  private static function wisewima_loadadmin()
  {
    $modules = [
      Wisewima_Admin::class,
      Wisewima_Assets::class,
      Wisewima_Api::class,

    ];

    foreach ($modules as $module) {
      (new $module())->init();
    }
  }
}
