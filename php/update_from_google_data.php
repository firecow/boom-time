<?
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

use App\Config;
use App\DAL\SQL;
use App\GoogleSheetsApi;

require 'vendor/autoload.php';
require 'error_cli.php';

$time = microtime(true);

$config = new Config();
$sql = new SQL($config->getPDODataSourceName(), $config->getPDOUsername(), $config->getPDOPassword());

$time = microtime(true);

echo "\n";
echo "--- Starting ---\n";

// Fetch each char from armory
$statement = $sql->raw("SELECT charName FROM characters");
$sheetData = file_get_contents("https://docs.google.com/spreadsheets/d/e/2PACX-1vQlsQf4opfay-TIVQzNTwXp3tSjIiVJYZ-2cWABtcoqdLIxy0259o9Sp0Kjglg-zaprpabJJLM0GX0A/pub?gid=2023636606&single=true&output=csv");

foreach ($statement as $key => $row) {
    GoogleSheetsApi::UpdateFromSheet($sql, $sheetData, $row['charName']);
}

echo "--- Elapsed: " . (microtime(true) - $time) . " ---\n";



