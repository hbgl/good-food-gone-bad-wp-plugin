<?php

/**
 * Compile po files in the language directory to mo files.
 */

$baseDir = realpath(__DIR__ . '/..');
$languageDir = $baseDir . '/languages';

$poFiles = glob($languageDir . '/*.po');

foreach ($poFiles as $poFile) {
    $moFile = realpath($languageDir . '/' . pathinfo($poFile, PATHINFO_FILENAME) . '.mo');
    exec("msgfmt -o $moFile $poFile", $output, $retVal);
    foreach ($output as $line) {
        echo $line . PHP_EOL;
    }
    echo "Generated $moFile" . PHP_EOL;
}
