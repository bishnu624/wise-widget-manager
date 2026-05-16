<?php

namespace WiseWidgetManager\Admin;

if (! defined('ABSPATH')) exit;

class Wisewima_Admin
{

  private $plugin_page_setup = array();
  private $plugin_page;
  public function init()
  {
    add_action('admin_menu', [$this, 'wisewima_register_menu']);
  }

  public function wisewima_register_menu()
  {
    $this->plugin_page_setup = apply_filters(
      'wisewima/plugin_page_setup',
      array(
        'parent_slug' => 'tools.php',
        'page_title' => esc_html__('WIse Widget Manager', 'wise-widget-manager'),
        'menu_title' => esc_html__('Wise Widget Manager', 'wise-widget-manager'),
        'capability' => 'manage_options',
        'menu_slug' => 'wise-widget-dashboard',
      )
    );

    $this->plugin_page = add_submenu_page(
      $this->plugin_page_setup['parent_slug'],
      $this->plugin_page_setup['page_title'],
      $this->plugin_page_setup['menu_title'],
      $this->plugin_page_setup['capability'],
      $this->plugin_page_setup['menu_slug'],
      apply_filters(
        'wisewima/plugin_page_display_callback_function',
        array($this, 'wisewima_render_menu')
      )
    );
  }

  public function wisewima_render_menu()
  {
    echo '<div id="wisema-widget-manager"></div>';
  }
}
