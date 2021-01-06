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
if (!defined( 'WPINC' ) ) {
	die;
}

define('GFGB_VERSION', '1.0.0');
define('GFGB_PLUGIN_DIR', __DIR__);

require_once GFGB_PLUGIN_DIR . '/includes/constants.php';
require_once GFGB_PLUGIN_DIR . '/includes/helpers.php';
require_once GFGB_PLUGIN_DIR . '/shortcode/food-categories/controller.php';

add_action('plugins_loaded', function () {
	load_plugin_textdomain(
		'gfgb',
		false,
		dirname(plugin_basename(__FILE__)) . '/languages/'
	);
});

add_action('init', function () {
	register_post_type(
		GFGB_POST_TYPE_FOOD,
		array(
			'labels' => array(
				'name' => __('Food', 'gfgb'),
				'singular_name' => __('Food', 'gfgb'),
			),
			'public' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_in_rest' => true,
			'supports' => array( 'title', 'custom-fields', 'editor', 'trackbacks', 'excerpt' ),
			'has_archive' => true,
			'delete_with_user' => false,
			'menu_icon' => 'dashicons-carrot',
			'rewrite' => array(
				'slug' => GFGB_SLUG_FOOD,
				'with_front' => false,
			),
		)
	);

	register_taxonomy(GFGB_TAXONOMY_FOOD_CATEGORY, array( GFGB_POST_TYPE_FOOD ), array(
		'labels' => array(
			'name' => __('Food Categories', 'gfgb'),
			'singular_name' => __('Food Category', 'gfgb'),
			'all_items' => __('All Food Categories', 'gfgb'),
			'edit_item' => __('Edit Food Category', 'gfgb'),
			'view_item' => __('View Food Category', 'gfgb'),
			'update_item' => __('Update Food Category', 'gfgb'),
			'add_new_item' => __('Add New Food Category', 'gfgb'),
			'new_item_name' => __('New Food Category Name', 'gfgb'),
			'parent_item' => __('Parent Food Category', 'gfgb'),
		),
		'hierarchical' => true,
		'public' => true,
		'show_in_rest' => true,
		'rewrite' => array(
			'slug' => GFGB_SLUG_FOOD_CATEGORY,
			'query_var' => GFGB_QUERY_VAR_FOOD_CATEGORY,
		),
	));
});

add_action('carbon_fields_register_fields', function () {
    Container::make('term_meta', __('Term Options'))
        ->where('term_taxonomy', '=', GFGB_TAXONOMY_FOOD_CATEGORY)
        ->add_fields([
			Field::make('text', 'priority', __('Priority'))
				->set_attributes([
					'type' => 'number',
					'step' => '1',
					'min' => '0',
				])
				->set_default_value('10')
				->set_required(true),
			Field::make('image', 'image', __('Image'))
				->set_required(true),
			Field::make('association', 'page', __('Page'))
				->set_types([
					['type' => 'post', 'post_type' => 'page'],
				])
				->set_max(1),
		]);
	
	Container::make('post_meta', __('Post Options'))
		->where('post_type', 'IN', ['page', GFGB_POST_TYPE_FOOD])
        ->add_fields([
			Field::make('textarea', 'quiz_json', __('Quiz'))
				->set_rows(10),
		]);
});

add_action('init', function () {
	add_shortcode('gfgb_food_categories', function ($atts, $content, $shortcode_tag) {
		return gfgb_shortcode_food_categories($atts, $content, $shortcode_tag);
	});	
});

add_filter('pre_get_terms', function ($query) {
	/** @var WP_Term_Query $query */
	$taxonomies = $query->query_vars['taxonomy'] ?? [];
	$isOnlyFoodCategory = count($taxonomies) === 1 && isset($taxonomies[0]) && $taxonomies[0] === GFGB_TAXONOMY_FOOD_CATEGORY;
	if (!$isOnlyFoodCategory) {
		return $query;
	}

	$query_vars = &$query->query_vars;
	if (($query_vars['orderby'] ?? null) === 'priority') {
		$query_vars['orderby'] = 'priority';
		$query_vars['meta_query'] = [
			'priority' => [
				'key' => 'priority',
				'compare' => 'EXISTS',
				'type' => 'SIGNED',
			],
		];
		$query->meta_query->parse_query_vars( $query_vars );
	}

    return $query;
}, 10, 2 );

add_filter('manage_edit-' . GFGB_TAXONOMY_FOOD_CATEGORY . '_columns', function ($columns) {
	$columns['priority'] = __('Priority');
	return $columns;
});

add_filter('manage_edit-' . GFGB_TAXONOMY_FOOD_CATEGORY . '_sortable_columns', function ($columns) {
	$columns['priority'] = 'priority';
	return $columns;
});

add_filter('manage_' . GFGB_TAXONOMY_FOOD_CATEGORY . '_custom_column', function ($string, $column_name, $term_id) {
	if ($column_name === 'priority') {
		$priority = carbon_get_term_meta($term_id, 'priority');
		return $priority;
	}
}, 10, 3);


add_action('after_setup_theme', function () {
    require_once GFGB_PLUGIN_DIR . '/carbon-fields/vendor/autoload.php';
    \Carbon_Fields\Carbon_Fields::boot();
});