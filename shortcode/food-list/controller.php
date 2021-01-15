<?php

function gfgb_shortcode_food_list($atts, $content, $shortcode_tag)
{
    $atts = is_array($atts) ? $atts : [];

    $template = locate_template('shortcode/gfgb-food-list.php', false, false);
    if ($template === '') {
        $template = __DIR__ . '/template.php';
    }

    $atts['parent_entry'] = null;
	$atts['entries'] = [];

    $parent_id = null;
    $parent = null;
    $children = [];

    $map_food = function ($food) {
        return [
            'food' => $food,
            'image_id' => absint(carbon_get_post_meta($food->ID, 'image')),
        ];
    };

    if (isset($atts['parent'])) {
        // Parent ID explicitly set.
        $parent_id = absint($atts['parent']);
        if ($parent_id !== 0) {
            $parent = get_post($parent_id);
            if ($parent->post_type !== GFGB_POST_TYPE_FOOD) {
                $parent_id = null;
                $parent = null;
            }
        }
    } elseif (is_singular(GFGB_POST_TYPE_FOOD)) {
        // Current post is a food.
        $parent = get_post();
        $parent_id = $parent === null ? null : $parent->ID;
    } elseif (is_post_type_archive(GFGB_POST_TYPE_FOOD)) {
        global $wp_query;
        /** @var \WP_Query $wp_query */
        $children = $wp_query->get_posts();
    } else {
        // Fall back to root food.
        $parent_id = 0;
    }
    
    if ($parent_id !== null) {
        $children_query = new WP_Query([
            'post_type' => GFGB_POST_TYPE_FOOD,
            'post_parent' => $parent_id,
            'nopaging' => true,
        ]);
        $children = $children_query->get_posts();
    }

    $atts['parent'] = $parent === null ? null : $map_food($parent);
    $atts['children'] = array_map($map_food, $children);

	return gfgb_render($template, $atts);
}