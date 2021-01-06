<?php

function gfgb_shortcode_food_categories($atts, $content, $shortcode_tag)
{
    $atts = is_array($atts) ? $atts : [];

    $template = locate_template('shortcode/gfgb-food-categories.php', false, false);
    if ($template === '') {
        $template = __DIR__ . '/template.php';
    }

    $parent_id = absint($atts['parent'] ?? 0);
    $query = new WP_Term_Query([
        'taxonomy' => GFGB_TAXONOMY_FOOD_CATEGORY,
        'orderby' => 'priority',
        'order' => 'ASC',
        'hide_empty' => false,
        'fields' => 'all',
        'meta_query' => [
            'priority' => [
                'key' => 'priority',
                'compare' => 'EXISTS',
                'type' => 'SIGNED',
            ],
        ],
    ]);
    $terms = $query->get_terms();

    $groups = [];
    foreach ($terms as $term) {
        $groups[$term->parent][] = $term;
    }

    $parent_term = null;
    if ($parent_id > 0) {
        foreach ($terms as $term) {
            if ($term->term_id === $parent_id) {
                $parent_term = $term;
                break;
            }
        }
    }

    $map_term = function ($term) use ($groups) {
        $image_id = absint(carbon_get_term_meta($term->term_id, 'image'));
        $page_id = absint(carbon_get_term_meta($term->term_id, 'page')[0]['id'] ?? 0);
        $has_children = !empty($groups[$term->term_id] ?? []);

        $type = GFGB_FOOD_CATEGORY_DISPLAY_TYPE_PAGE;
        if (!$has_children && $term->count === 1) {
            $type = GFGB_FOOD_CATEGORY_DISPLAY_TYPE_FOOD_PAGE;
        }
        return [
            'term' => $term,
            'type' => $type,
            'image_id' => $image_id,
            'food_post' => null,
            'page_id' => $page_id,
        ];
    };

    
    $entries = array_map($map_term, $groups[$parent_id] ?? []);

    $food_page_term_ids = [];
    foreach ($entries as $entry) {
        if ($entry['type'] === GFGB_FOOD_CATEGORY_DISPLAY_TYPE_FOOD_PAGE) {
            $food_page_term_ids[] = $entry['term']->term_id;
        }
    }

    if (!empty($food_page_term_ids)) {
        $food_page_post_query = new WP_Query([
            'post_type' => GFGB_POST_TYPE_FOOD,
            'tax_query' => [
                'taxonomy' => GFGB_TAXONOMY_FOOD_CATEGORY,
                'terms' => $food_page_term_ids,
                'include_children' => false,
            ],
            'posts_per_page' => -1,
        ]);
        $food_page_posts = gfgb_pluck($food_page_post_query->get_posts(), null, 'ID');
        $food_page_post_ids = gfgb_pluck($food_page_posts, 'ID');

        $food_page_term_query = new WP_Term_Query([
            'taxonomy' => GFGB_TAXONOMY_FOOD_CATEGORY,
            'object_ids' => $food_page_post_ids,
            'hide_empty' => false,
            'fields' => 'all_with_object_id',
        ]);
        
        $food_page_terms = gfgb_pluck($food_page_term_query->get_terms(), null, 'term_id');
        foreach ($entries as &$entry) {
            if ($entry['type'] === GFGB_FOOD_CATEGORY_DISPLAY_TYPE_FOOD_PAGE) {
                $term = $food_page_terms[$entry['term']->term_id] ?? null;
                $entry['food_post'] = $food_page_posts[$term->object_id] ?? null;
            }
        }
    }

    $atts['parent_entry'] = $parent_term === null ? null : $map_term($parent_term);
    $atts['entries'] = $entries;

    return gfgb_render($template, $atts);
}