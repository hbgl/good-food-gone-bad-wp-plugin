<?php

/**
 * @param array $breadcrumps
 * @param WP_Post $post
 * @return array
 */
function gfgb_wc_get_hierarchical_breadcrumbs($breadcrumps, $post) {
	// Find the index of the post breadcrump and trim off from there.
	$permalink = get_permalink($post);
	$post_index = 0;
    foreach ($breadcrumps as $crump) {
		if ($crump[1] === $permalink) {
			break;
		}
		$post_index++;
	}
	if ($post_index >= count($breadcrumps)) {
		return $breadcrumps;
	}
	$tail_breadcrumbs = array_splice($breadcrumps, $post_index);

	// Append parent crumbs.
	if ($post->post_parent) {
		$parent = $post;
		$parents = [];
		do {
			$parent = get_post($parent->post_parent);
			$parents[] = $parent;
		} while ($parent->post_parent);
		
		array_reverse($parents); 
		foreach ($parents as $parent) {
			$breadcrumps[] = [wp_strip_all_tags(get_the_title($parent->ID)),get_permalink($parent->ID)];
		}
	}

	// Append previously spliced off tail.
	foreach ($tail_breadcrumbs as $breadcrump) {
		$breadcrumps[] = $breadcrump;
	}
	
	return $breadcrumps;
}