<?php

namespace GfgbBuild;

use Symfony\Component\Filesystem\Filesystem;

require_once __DIR__ . '/../vendor/autoload.php';

class ComposerScripts
{
    private static $init = false;
    private static $base_dir;
    private static $release_dir;
    private static $release_bundle_file;

    private static function init() {
        if (self::$init) {
            return;
        }
        self::$base_dir = realpath(__DIR__ . '/..');
        self::$release_dir = self::$base_dir . '/release';
        self::$release_bundle_file = self::$release_dir . '/good-food-gone-bad-wp-plugin.zip';
    }

    public static function build()
    {
        self::init();
        self::clean();
        self::build_i18n();

        $files = [
            'carbon-fields',
            'includes',
            'languages',
            'shortcode',
            'gfgb.php',
            'index.php',
            'LICENSE.txt',
            'README.md',
            'uninstall.php',
        ];

        mkdir(self::$release_dir);
        $package_zip = new \PhpZip\ZipFile();
        try{
            foreach ($files as $file) {
                $path = self::$base_dir . '/' . $file;
                if (is_dir($path)) {
                    $package_zip->addDirRecursive($path, '/' . $file);
                } else {
                    $package_zip->addFile($path);
                }
            }
            $package_zip->saveAsFile(self::$release_bundle_file);
        }
        finally{
            $package_zip->close();
        }

        echo "Build successful\n";
    }

    public static function clean()
    {
        self::init();
        $fs = new Filesystem();
        $fs->remove(self::$release_dir);
        echo "Cleaned\n";
    }

    public static function build_i18n()
    {
        self::init();
        $language_dir = self::$base_dir . '/languages';  
        $po_files = glob($language_dir . '/*.po');
        
        foreach ($po_files as $po_file) {
            $mo_file = $language_dir . '/' . pathinfo($po_file, PATHINFO_FILENAME) . '.mo';
            exec("msgfmt -o $mo_file $po_file", $output, $ret_val);
            foreach ($output as $line) {
                echo $line . PHP_EOL;
            }
            echo "Generated $mo_file" . PHP_EOL;
        }
    }
}