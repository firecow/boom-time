<?
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

use App\CLI;

// Include class loader.
require 'vendor/autoload.php';
require 'error_cli.php';

// Iterate routes folders, and find all `*Test*.php` files.
$it = new RecursiveDirectoryIterator("src/");
$it = new RecursiveIteratorIterator($it);
$it = new RegexIterator($it, "/.*Test.*\.failed/i");
foreach ($it as $splFile) {
    $fileName = $splFile->getPathname();
    unlink($fileName);
}

// Failed test counter.
$failed = 0;

// Drop and setup mysql.
$output = shell_exec("sh /shell/mysqldrop.sh");
$output = shell_exec("sh /shell/mysqlsetup.sh");

$testClasses = array();
if (isset($argv[1])) {
    $testClasses[] = $argv[1];
} else {
    // Iterate routes folders, and find all `*Test*.php` files.
    $it = new RecursiveDirectoryIterator("src/");
    $it = new RecursiveIteratorIterator($it);
    $it = new RegexIterator($it, "/.*Test.*\.php/i");
    foreach ($it as $splFile) {
        $fileName = $splFile->getPathname();

        // File is in testing folder, don't include in tests.
        if (preg_match("/testing/i", $fileName)) {
            continue;
        }

        // Get className from file, remember microsoft windows.
        $className = str_replace(".php", "", $fileName);
        $className = str_replace("php/", "", $className);
        $className = str_replace("php\\", "", $className);
        $className = str_replace("/", "\\", $className);
        $className = str_replace("src\\", "App\\", $className);

        $testClasses[] = $className;
    }
}

// Row headers
echo str_pad("Status", 11) . str_pad("Class Name", 91) . str_pad("Time", 10) . "\n";

// Test rows content
foreach ($testClasses as $className) {
    // Create new instance of class name.
    $test = new ReflectionClass($className);
    $test = $test->newInstance();

    try {
        $startTime = microtime(true);
        $test->ExecuteTest();
        $dt = number_format(microtime(true) - $startTime, 2);
        echo str_pad(CLI::green("Success"), 20) . str_pad(CLI::blue($className), 100) . str_pad(sprintf('%6.2fs', $dt), 10) . "\n";
    } catch (AssertionError $ex) {
        $message = $ex->getMessage();
        echo str_pad(CLI::red("Failed"), 20) . CLI::blue($className) . " $message\n";
        $failed++;
    }
}

// Reset mysql
shell_exec("sh /shell/mysqlimport.sh");

// Reset mongo
shell_exec("sh /shell/mongoimport.sh");

exit($failed > 0 ? 1 : 0);
