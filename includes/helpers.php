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
