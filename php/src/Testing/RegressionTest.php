<?
declare(strict_types=1);

namespace App\Testing;

use App\Diff;
use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\File;
use AssertionError;
use function shell_exec;

abstract class RegressionTest
{

    /**
     * @throws FileErrorException
     * @throws FileNotFoundException
     */
    public final function executeTest() {
        $class = get_class($this);
        $expectedFile = str_replace(".php", ".expected",  "src\\$class.php");
        $expectedFile = str_replace("\\", "/",  $expectedFile);
        $expectedFile = str_replace("/App/", "/",  $expectedFile);

        // Reset mysql
        shell_exec("sh /shell/mysqlimport.sh");
        $initialSqlDump = shell_exec("sh /shell/mysqlexport.sh");

        // Reset mongo
        shell_exec("sh /shell/mongoimport.sh");
        $initialMongo = shell_exec("sh /shell/mongoexport.sh");

        // Run the regression test.
        $output = $this->doTest();

        // Dump sql, and check for diffs.
        $sqlDump = shell_exec("sh /shell/mysqlexport.sh");
        $compareResult = Diff::compare($initialSqlDump, $sqlDump, false);
        $filtered = array_filter($compareResult, function ($line) {
            return $line[1] > 0;
        });
        if (count($filtered) > 0) {
            $output .= "\n\n";
            foreach ($filtered as $key => $line) {
                $value = $line[0];
                $operator = $line[1] === 1 ? "-" : "+";
                $output .= "$operator $value\n";
            }
        }

        // Dump mongo, and check for diffs.
        $mongoExport = shell_exec("sh /shell/mongoexport.sh");
        $compareResult = Diff::compare($initialMongo, $mongoExport, false);
        $filtered = array_filter($compareResult, function ($line) {
            return $line[1] > 0;
        });
        if (count($filtered) > 0) {
            $output .= "\n\n";
            foreach ($filtered as $key => $line) {
                $value = $line[0];
                $operator = $line[1] === 1 ? "-" : "+";
                $output .= "$operator $value\n";
            }
        }

        // Check output
        $expected = File::loadFileTextContent($expectedFile);
        if (strcasecmp($expected, $output) !== 0) {
            $currentOutputFilePath = str_replace(".expected", ".failed", $expectedFile);
            file_put_contents($currentOutputFilePath, $output);
            throw new AssertionError("$currentOutputFilePath");
        }
    }

    protected abstract function doTest(): string;

}