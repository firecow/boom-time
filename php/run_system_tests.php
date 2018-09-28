<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

use App\File;
use App\HTTP\HTTPRequest;

// Include class loader.
require 'vendor/autoload.php';
require 'error_cli.php';

$it = new RecursiveDirectoryIterator("tests/");
$it = new RecursiveIteratorIterator($it);
$it = new RegexIterator($it, "/\.http$/i");

foreach ($it as $splFile) {
    $filePath = $splFile->getPathname();
    $response = HTTPRequest::sendFromHttpFile($filePath);
    $response->getResponseText();
    $expected = File::loadFileTextContent(str_replace(".http", ".expected", $filePath));

    if ($expected === $response->getResponseText()) {
        echo "Success";
    } else {
        file_put_contents(str_replace(".http", ".expected", $filePath), $response);
        echo "Failure";
    }
}