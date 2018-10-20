<?
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

use App\Config;
use App\DAL\SQL;
use App\File;

require 'vendor/autoload.php';
require 'error_cli.php';

$time = microtime(true);

$config = new Config();
$sql = new SQL($config->getPDODataSourceName(), $config->getPDOUsername(), $config->getPDOPassword());

$text = File::loadFileTextContent("../data/instances.json");
file_put_contents("../data/encoded.json", $text);

$arr = json_decode($text, true);
echo json_last_error_msg() . "\n";

$regExps = [
    "/BWL.*/g" => "BWL",
    "/MC.*/g" => "MC",
    "/Onyxi.*/g" => "Onyxia"
];
foreach ($arr as $items) {
    foreach($items as $item) {
        $itemId = $item[0];
        $itemName = $item[2];
        $query = "REPLACE INTO items_location (itemId, location, itemName) VALUES (?, 'MC', ?)";
        $sql->execute($query, [$itemId, $itemName]);
    }
}

echo "--- Elapsed: " . (microtime(true) - $time) . " ---\n";



