<?
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

use App\Config;
use App\DAL\SQL;
use App\HTTP\XMLHTTPRequest;
use App\TwinstarArmoryApi;

require 'vendor/autoload.php';
require 'error_cli.php';

$time = microtime(true);

$config = new Config();
$sql = new SQL($config->getPDODataSourceName(), $config->getPDOUsername(), $config->getPDOPassword());

$time = microtime(true);

echo "\n";
echo "--- Starting ---\n";

// Update guild
TwinstarArmoryApi::InitCharactersFromBoomTime($sql);

// Fetch each char from armory
$statement = $sql->raw("SELECT charName FROM characters");
$total = $statement->rowCount();
foreach ($statement as $key => $row) {
    $charName = $row['charName'];
    $url = "http://armory.twinstar.cz/character-sheet.xml?r=KronosIII&cn=$charName";
    $httpRequest = new XMLHTTPRequest($url);
    $response = $httpRequest->send();

    if ($response->getStatus() != "HTTP/1.1 200 OK") {
        echo "$charName armory failed {$response->getStatus()}";
        continue;
    }
    $responseText = $response->getResponseText();

    if (preg_match_all("/nosearchresult/m", $responseText)) {
        echo "'$charName' banned or doesn't exist\n";
        continue;
    }

    try {
        TwinstarArmoryApi::InitCharacterByXML(simplexml_load_string($responseText), $sql);
    } catch (Throwable $ex) {
        echo "$ex\n";
        echo "$url\n";
    }
}

// Iterate xml files to support legacy data.
//$it = new RecursiveDirectoryIterator("../data/armory/");
//$it = new RecursiveIteratorIterator($it);
//$it = new RegexIterator($it, "/\.xml/i");
//foreach ($it as $splFile) {
//    $fileName = $splFile->getPathname();
//    try {
//        TwinstarArmoryApi::InitCharacterByXML(simplexml_load_string(file_get_contents($fileName)), $sql);
//    } catch (Throwable $ex) {
//        echo "$ex\n";
//        echo "$fileName\n";
//    }
//}

echo "--- Elapsed: " . (microtime(true) - $time) . " ---\n";



