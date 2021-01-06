<?php

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

/**
 * @param string $file
 * @return string Path to file from plugin root.
 */
function gfgb_path($file)
{
    return plugin_dir_path(__DIR__).$file;
}

/**
 * @param string $file
 * @return string Path to file from plugin includes directory.
 */
function gfgb_includes_path($file)
{
    return gfgb_path('includes/' . $file);
}

/**
 * @param string $file
 * @return string Path to file from plugin public directory.
 */
function gfgb_public_path($file)
{
    return gfgb_path('public/' . $file);
}

/**
 * @param string $file
 * @return string Path to file from plugin admin directory.
 */
function gfgb_admin_path($file)
{
    return gfgb_path('admin/' . $file);
}

function gfgb_include($template, $args = [])
{
    $__x8ucMNBDogBenonQ4U2w662KPS5f29x7zMSOlqGTWMxdy8q9MBOIUziAZWMF = $template;
    $__O03CZbBRHTpD6RXl9K2cdhXCuUxalmtewi39ImyxD1VRCmrnozrCbfZ7WvKD = $args;

    (function () use ($__x8ucMNBDogBenonQ4U2w662KPS5f29x7zMSOlqGTWMxdy8q9MBOIUziAZWMF, $__O03CZbBRHTpD6RXl9K2cdhXCuUxalmtewi39ImyxD1VRCmrnozrCbfZ7WvKD) {
        extract($__O03CZbBRHTpD6RXl9K2cdhXCuUxalmtewi39ImyxD1VRCmrnozrCbfZ7WvKD);
        include $__x8ucMNBDogBenonQ4U2w662KPS5f29x7zMSOlqGTWMxdy8q9MBOIUziAZWMF;
    })();
}

function gfgb_render($template_names, $args = [])
{
    $rendered = '';
    ob_start();
    try {
        gfgb_include($template_names, $args);
    } finally {
        $rendered = ob_get_contents(); 
        ob_end_clean();
    }
    return $rendered;
}

function gfgb_pluck($list, $field, $index_key = null)
{
    $plucked = [];

    if (!$index_key) {
        foreach ($list as $key => $value ) {
            if ($field === null) {
                $plucked[$key] = $value;
            } else {
                if (is_object($value)) {
                    $plucked[$key] = $value->$field;
                } else {
                    $plucked[$key] = $value[$field];
                }
            }
        }
        return $plucked;
    }
 
    foreach ($list as $key => $value) {
        if ( is_object($value) ) {
            $plucked_value = $field === null ? $value : $value->$field;
            if (isset($value->$index_key)) {
                $plucked[ $value->$index_key ] = $plucked_value;
            } else {
                $plucked[] = $plucked_value;
            }
        } else {
            $plucked_value = $field === null ? $value : $value[$field];
            if (isset($value[ $index_key])) {
                $plucked[$value[$index_key]] = $plucked_value;
            } else {
                $plucked[] = $plucked_value;
            }
        }
    }
 
    return $plucked;
}