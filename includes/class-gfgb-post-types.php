<?php

/**
 * Register all post types for the plugin
 *
 * @link       https://github.com/hbgl
 * @since      1.0.0
 *
 * @package    Gfgb
 * @subpackage Gfgb/includes
 */
class Gfgb_PostTypes
{

    /**
     * Register custom post types.
     *
     * @since    1.0.0
     */
    public function register_custom_post_types()
    {
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

        register_taxonomy(GFGB_SLUG_FOOD_CATEGORY, array( GFGB_POST_TYPE_FOOD ), array(
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
            'public' => true,
            'show_in_rest' => true,
            'rewrite' => array(
                'slug' => GFGB_SLUG_FOOD_CATEGORY,
                'query_var' => GFGB_QUERY_VAR_FOOD_CATEGORY,
            ),
        ));
    }
}
