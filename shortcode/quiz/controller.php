<?php

function gfgb_shortcode_quiz($atts, $content, $shortcode_tag)
{
    $atts = is_array($atts) ? $atts : [];

    $template = locate_template('shortcode/gfgb-quiz.php', false, false);
    if ($template === '') {
        $template = __DIR__ . '/template.php';
    }
    
    $atts['quiz_json'] = carbon_get_the_post_meta('quiz_json');

    return gfgb_render($template, $atts);
}