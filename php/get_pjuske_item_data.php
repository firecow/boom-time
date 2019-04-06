<?
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

use App\Config;
use App\DAL\SQL;

require 'vendor/autoload.php';
require 'error_cli.php';

$time = microtime(true);

$config = new Config();
$sql = new SQL($config->getPDODataSourceName(), $config->getPDOUsername(), $config->getPDOPassword());

$rows = $sql->fetchAll("SELECT * FROM items WHERE charName = ?", ['Sthlm']);

foreach ($rows as &$row) {
    $row = [
        "itemId" => $row['itemId'],
        "timestamp" => strtotime($row['firstSeen']),
        "location" => $row['firstSeen']
    ];
}

echo json_encode($rows, JSON_PRETTY_PRINT) . "\n";