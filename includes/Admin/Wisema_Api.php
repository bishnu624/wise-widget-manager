<?php

/**
 * Wise Widget Manager — REST API (NO ELEMENTOR)
 * Supports: Classic, Gutenberg (block widgets), WooCommerce
 *
 * Usage: Widget_Manager::init();
 */

namespace WiseWidgetManager\Admin;

use \WP_REST_Request;
use \WP_Error;

if (! defined('ABSPATH')) {
  exit;
}

class Wisema_Api
{

  /**
   * REST API namespace.
   */
  const NAMESPACE = 'wisewima/v1';

  /**
   * Nonce action used for all REST requests.
   */
  const NONCE_ACTION = 'wp_rest';

  /**
   * WordPress option key that stores disabled widget IDs.
   */
  const OPTION_DISABLED = 'wisewima_disabled_widgets';


  public static function init(): void
  {
    $instance = new self();

    add_action('rest_api_init',    [$instance, 'wisewima_register_routes']);
    add_filter('sidebars_widgets', [$instance, 'wisewima_filter_disabled_widgets']);
  }


  // Route registration
  // ----------------------------------------------------------------

  public function wisewima_register_routes(): void
  {

    $auth = [$this, 'wisewima_check_permission'];

    register_rest_route(self::NAMESPACE, '/widgets', [
      'methods'             => 'GET',
      'permission_callback' => $auth,
      'callback'            => [$this, 'wisewima_get_widgets'],
    ]);


    register_rest_route(self::NAMESPACE, '/toggle', [
      'methods'             => 'POST',
      'permission_callback' => $auth,
      'callback'            => [$this, 'wisewima_toggle_widget'],
    ]);

    register_rest_route(self::NAMESPACE, '/remove', [
      'methods'             => 'POST',
      'permission_callback' => $auth,
      'callback'            => [$this, 'wisewima_remove_widget'],
    ]);

    register_rest_route(self::NAMESPACE, '/sidebar-insights', [
      'methods'  => 'GET',
      'permission_callback' => $auth,
      'callback' => [$this, 'wisewima_get_sidebar_insights'],
    ]);
  }

  // ----------------------------------------------------------------
  // Permission / nonce check (shared by all routes)
  // ----------------------------------------------------------------


  public function wisewima_check_permission(WP_REST_Request $request)
  {
    if (! current_user_can('edit_theme_options')) {
      return new WP_Error(
        'rest_forbidden',
        __('You do not have permission to manage widgets.', 'wise-widget-manager'),
        ['status' => 403]
      );
    }

    $nonce = $request->get_header('X-WP-Nonce')
      ?? $request->get_param('_wpnonce');

    if (! $nonce || ! wp_verify_nonce($nonce, 'wp_rest')) {
      return new WP_Error(
        'rest_nonce_invalid',
        __('Nonce verification failed.', 'wise-widget-manager'),
        ['status' => 403]
      );
    }

    return true;
  }

  // ----------------------------------------------------------------
  // GET /widgets
  // ----------------------------------------------------------------

  public function wisewima_get_widgets(): array
  {

    global $wp_registered_widgets;

    $registered_sidebars = $GLOBALS['wp_registered_sidebars'] ?? [];
    $sidebars_widgets    = get_option('sidebars_widgets', []);
    $disabled            = $this->wisewima_get_disabled_widgets();
    $result              = [];

    foreach ($registered_sidebars as $sidebar_id => $sidebar) {

      $widget_ids = $sidebars_widgets[$sidebar_id] ?? [];
      $widgets    = [];

      foreach ($widget_ids as $widget_id) {
        if (! isset($wp_registered_widgets[$widget_id])) {
          continue;
        }
        $type = $this->wisewima_get_widget_type($widget_id);
        $name = $wp_registered_widgets[$widget_id]['name'];

        // Replace generic "Block" title.
        if ($type === 'block') {
          $name = $this->wisewima_get_block_title($widget_id);
        }
        $widgets[] = [
          'id'      => $widget_id,
          'name'    => $name,
          'type'    => $this->wisewima_get_widget_type($widget_id),
          'enabled' => ! in_array($widget_id, $disabled, true),
        ];
      }

      $result[] = [
        'area'      => $sidebar_id,
        'area_name' => $sidebar['name'] ?? $sidebar_id,
        'source'    => 'wordpress',
        'widgets'   => $widgets,
      ];
    }

    return $result;
  }

  private function wisewima_get_block_title(string $widget_id): string
  {

    preg_match('/-(\d+)$/', $widget_id, $matches);

    $number = isset($matches[1])
      ? (int) $matches[1]
      : null;

    if (!$number) {
      return __('Block', 'wise-widget-manager');
    }

    $settings = get_option('widget_block');

    $content = $settings[$number]['content'] ?? '';

    if (!$content) {
      return __('Block', 'wise-widget-manager');
    }

    $blocks = parse_blocks($content);

    $primary = $blocks[0]['blockName'] ?? '';

    if (!$primary) {
      return __('Block', 'wise-widget-manager');
    }

    $primary = explode('/', $primary)[1] ?? $primary;

    return ucwords(
      str_replace(
        ['-', '_'],
        ' ',
        $primary
      )
    );
  }


  // POST /toggle


  public function wisewima_toggle_widget(WP_REST_Request $request): array
  {

    $id     = sanitize_text_field($request->get_param('id'));
    $enable = filter_var($request->get_param('enabled'), FILTER_VALIDATE_BOOLEAN);

    $disabled = $this->wisewima_get_disabled_widgets();

    if ($enable) {
      $disabled = wisewima_array_filter_not($disabled, $id);
    } else {
      if (! in_array($id, $disabled, true)) {
        $disabled[] = $id;
      }
    }

    update_option(self::OPTION_DISABLED, $disabled);

    return ['success' => true, 'enabled' => $enable];
  }

  // ----------------------------------------------------------------
  // POST /remove
  // ----------------------------------------------------------------

  public function wisewima_remove_widget(WP_REST_Request $request): array
  {

    $id       = sanitize_text_field($request->get_param('id'));
    $sidebars = get_option('sidebars_widgets', []);

    foreach ($sidebars as $sb => $widgets) {
      $sidebars[$sb] = wisewima_array_filter_not((array) $widgets, $id);
    }

    wp_set_sidebars_widgets($sidebars);

    return ['success' => true];
  }



  // Frontend filter — hide disabled widgets on public pages
  // ----------------------------------------------------------------

  public function wisewima_filter_disabled_widgets(array $sidebars): array
  {

    if (is_admin() || defined('REST_REQUEST') && REST_REQUEST) {
      return $sidebars;
    }

    $disabled = $this->wisewima_get_disabled_widgets();
    if (empty($disabled)) {
      return $sidebars;
    }

    foreach ($sidebars as $sidebar_id => $widgets) {
      $sidebars[$sidebar_id] = wisewima_array_filter_not_in(
        (array) $widgets,
        $disabled
      );
    }

    return $sidebars;
  }


  /**
   * Detect the type of a registered widget: 'classic', 'block', or 'woocommerce'.
   */
  private function wisewima_get_widget_type(string $widget_id): string
  {

    global $wp_registered_widgets;

    $widget   = $wp_registered_widgets[$widget_id] ?? null;
    if (! $widget) return 'unknown';

    $callback = $widget['callback'] ?? null;
    $class    = '';

    if (is_array($callback) && is_object($callback[0])) {
      $class = get_class($callback[0]);
    }

    if (wisewima_str_starts_with($class, 'WC_Widget') || wisewima_str_contains($class, 'WooCommerce')) {
      return 'woocommerce';
    }

    if ($class === 'WP_Widget_Block') {
      return 'block';
    }

    if (wisewima_str_starts_with($class, 'WP_Widget') || wisewima_str_contains($class, 'WP_Widget_') === 0) {
      return 'classic';
    }

    return 'classic';
  }

  /**
   * Return the list of disabled widget IDs from the database.
   */
  private function wisewima_get_disabled_widgets(): array
  {
    return get_option(self::OPTION_DISABLED, []);
  }

  public function wisewima_get_sidebar_insights()
  {

    global $wp_registered_sidebars;

    $sidebars_widgets = get_option('sidebars_widgets', []);

    $data = [];

    foreach ($wp_registered_sidebars as $sidebar_id => $sidebar) {

      $widgets = isset($sidebars_widgets[$sidebar_id])
        ? $sidebars_widgets[$sidebar_id]
        : [];

      $count = count($widgets);

      $data[] = [
        'id'           => $sidebar_id,
        'name'         => $sidebar['name'],
        'widget_count' => $count
      ];
    }

    return rest_ensure_response($data);
  }
}
