<?php

namespace WiseWidgetManager\Admin;

if (! defined('ABSPATH')) exit;
class Wisema_Admin_Assets
{
  public function init()
  {
    add_action('admin_enqueue_scripts', [$this, 'wisewima_dequeue_assets'], 9999);
    add_action('admin_enqueue_scripts', [$this, 'wisewima_enqueue_assets']);
  }

  private function is_wisewima_Page()
  {

    $screen = get_current_screen();
    return $screen && $screen->id === 'tools_page_wise-widget-dashboard';
  }

  public function wisewima_dequeue_assets($hook)
  {
    if (!$this->is_wisewima_Page()) return;

    $remove = [
      'svg-painter',
    ];

    foreach ($remove as $handle) {
      wp_dequeue_script($handle);
      wp_deregister_script($handle);
    }

    remove_all_actions('admin_notices');
    remove_all_actions('all_admin_notices');
  }

  public function wisewima_enqueue_assets($hook)
  {
    if (!$this->is_wisewima_Page()) return;

    wp_enqueue_script(
      'wisewima-script',
      WISEWIMA_URL . 'dist/assets/admin.js',
      array('wp-hooks', 'wp-element', 'wp-api-fetch'),
      WISEWIMA_VERSION,
      true
    );

    wp_enqueue_style(
      'wisewima-style',
      WISEWIMA_URL . 'dist/assets/index.css',
      [],
      WISEWIMA_VERSION
    );

    wp_localize_script('wisewima-script', 'wisewima', [
      'apiUrl' => site_url() . '/index.php?rest_route=',
      'nonce'  => wp_create_nonce('wp_rest'),
      'imgUrl' => WISEWIMA_URL . 'assets/'
    ]);
  }
}
