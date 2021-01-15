<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/hbgl
 * @since             1.0.0
 * @package           Gfgb
 *
 * @wordpress-plugin
 * Plugin Name:       WpGoodFoodGoneBad
 * Plugin URI:        https://github.com/hbgl/good-food-gone-bad-wp-plugin
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            hbgl
 * Author URI:        https://github.com/hbgl
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       gfgb
 * Domain Path:       /languages
 */

use Carbon_Fields\Container\Container;
use Carbon_Fields\Field\Field;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

define('GFGB_VERSION', '1.0.0');
define('GFGB_PLUGIN_DIR', __DIR__);

require_once GFGB_PLUGIN_DIR . '/includes/constants.php';
require_once GFGB_PLUGIN_DIR . '/includes/helpers.php';
require_once GFGB_PLUGIN_DIR . '/shortcode/food-list/controller.php';
require_once GFGB_PLUGIN_DIR . '/shortcode/quiz/controller.php';
require_once GFGB_PLUGIN_DIR . '/includes/wc-breadcrumbs-extension.php';

/**
 * Load textdomain.
 */
add_action('plugins_loaded', function () {
    load_plugin_textdomain(
        'gfgb',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
});

/**
 * Register food post type.
 */
add_action('init', function () {
    register_post_type(
        GFGB_POST_TYPE_FOOD,
        array(
            'labels' => array(
                'name' => __('Food', 'gfgb'),
                'singular_name' => __('Food', 'gfgb'),
            ),
            'public' => true,
            'hierarchical' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'show_in_rest' => true,
            'supports' => array( 'title', 'custom-fields', 'editor', 'trackbacks', 'excerpt', 'page-attributes' ),
            'has_archive' => true,
            'delete_with_user' => false,
            'menu_icon' => 'dashicons-carrot',
            'rewrite' => array(
                'slug' => GFGB_SLUG_FOOD,
                'with_front' => false,
            ),
        )
    );
});

/**
 * Register custom fields.
 */
add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', __('Post Options'))
        ->where('post_type', 'IN', [GFGB_POST_TYPE_FOOD])
        ->add_fields([
            Field::make('image', 'image', __('Image'))
                ->set_required(true),
            Field::make('textarea', 'quiz_json', __('Quiz'))
                ->set_rows(10),
        ]);
});

/**
 * Register shortcodes.
 */
add_action('init', function () {
    add_shortcode('gfgb_food_list', function ($atts, $content, $shortcode_tag) {
        return gfgb_shortcode_food_list($atts, $content, $shortcode_tag);
    });

    add_shortcode('gfgb_quiz', function ($atts, $content, $shortcode_tag) {
        return gfgb_shortcode_quiz($atts, $content, $shortcode_tag);
    });
});

/**
 * Allow pages and food posts to have non-published parent posts.
 */
foreach ([
    'page_attributes_dropdown_pages_args',
    'quick_edit_dropdown_pages_args',
] as $filter) {
    add_filter($filter, function ($args) {
        // Dropdown must be for parent post.
        if ($args['name'] !== 'post_parent') {
            return;
        }
        // Must be a specified post type.
        $allowed_post_types = ['page', GFGB_POST_TYPE_FOOD];
        if (!in_array($args['post_type'], $allowed_post_types, true)) {
            return;
        }
        $values = ['publish', 'pending', 'draft', 'private'];
        $args['post_status'] = array_unique(array_merge($args['post_status'] ?? [], $values));
        return $args;
    }, 11, 1);
}

/**
 * Get hierarchical breadcrumbs for custom post type food.
 */
// add_action('woocommerce_get_breadcrumb', function ($breadcrumps) {
//     global $post;
//     if ($post->post_type === GFGB_POST_TYPE_FOOD) {
//         return gfgb_wc_get_hierarchical_breadcrumbs($breadcrumps, $post);
//     }
// }, 11, 1);

/**
 * Apply default food query vars.
 */
add_filter('pre_get_posts', function ($query) {
    /** @var \WP_Query $query */
    $is_applicable = !$query->is_admin
        && $query->is_archive
        && $query->query_vars['post_type'] === 'food';
    if (!$is_applicable) {
        return $query;
    }
    // By default only get food at the root level.
    if (!is_int($query->query_vars['post_parent'])) {
        $query->query_vars['post_parent'] = 0;
    }
    // By default get all food posts.
    if (!isset($query->query_vars['posts_per_page'])) {
        $query->query_vars['posts_per_page'] = -1;
    };
    return $query;
});

/**
 * Boot carbon fields.
 */
add_action('after_setup_theme', function () {
    require_once GFGB_PLUGIN_DIR . '/carbon-fields/vendor/autoload.php';
    \Carbon_Fields\Carbon_Fields::boot();
});
