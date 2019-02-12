<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

use App\Config;
use App\DAL\SQL;

require 'vendor/autoload.php';
require 'error_cli.php';

$config = new Config();
$sql = new SQL($config->getPDODataSourceName(), $config->getPDOUsername(), $config->getPDOPassword());

// TODO: Class specific items. "Classes: Priest, Mage, Warlock"
// TODO: Fishing, Mining, BOP/BOE
// TODO: Buttons to filter items for specific patches.
// TODO: Beastslayer
// TODO: Quest/Drop/PVP

libxml_use_internal_errors(true);

$types = $sql->raw("SELECT * FROM types WHERE enabled = 1")->fetchAll();
$armorTypes = $sql->raw("SELECT * FROM armor_types")->fetchAll();
$items = $sql->raw("SELECT * FROM items WHERE type NOT IN (4, 19) GROUP BY itemId ORDER BY itemId")->fetchAll();
$itemsFromRaids = $sql->raw("SELECT * FROM items_location GROUP BY itemId ORDER BY itemId")->fetchAll();

$items = array_merge($items, $itemsFromRaids);

//$items = [["itemId" => 21563, "itemName" => "Don Rodrigo's Band"]];

foreach ($items as $item) {
    $itemId = $item['itemId'];
    $itemName = $item['itemName'];

    $url = "http://classicdb.ch/?item=$itemId";

    try {
        $contents = file_get_contents($url) . "\n";
    } catch (Exception $ex) {
        try {
            sleep(10);
            $contents = file_get_contents($url) . "\n";
        } catch (Exception $ex) {
            echo "HTTP Request exception $itemName ($itemId)\n";
            continue;
        }
    }
    $document = new DOMDocument();
    $document->loadHTML($contents);
    $element = $document->getElementById("tooltip$itemId-generic");

    $itemStat = [
        "stamina" => [
            "regex" => "([\d|\.]*) Stamina",
        ],
        "strength" => [
            "regex" => "([\d|\.]*) Strength",
        ],
        "agility" => [
            "regex" => "([\d|\.]*) Agility",
        ],
        "spirit" => [
            "regex" => "([\d|\.]*) Spirit",
        ],
        "intellect" => [
            "regex" => "([\d|\.]*) Intellect",
        ],
        "armor" => [
            "regex" => "([\d|\.]*) Armor",
        ],
        "attackPower" => [
            "regex" => "([\d|\.]*) Attack Power\.",
        ],
        "rangedAttackPower" => [
            "regex" => "\+([\d|\.]*) ranged Attack Power\.",
        ],
        "minDmg" => [
            "regex" => "([\d|\.]*) - [\d|\.]*"
        ],
        "maxDmg" => [
            "regex" => "[\d|\.]* - ([\d|\.]*)"
        ],
        "speed" => [
            "regex" => "Speed ([\d|\.]*)"
        ],
        "dps" => [
            "regex" => "([\d|\.]*) damage per second" ,
        ],
        "physicalCrit" => [
            "regex" => "Improves your chance to get a critical strike by ([\d|\.]*)%" ,
        ],
        "physicalHit" => [
            "regex" => "Improves your chance to hit by ([\d|\.]*)%"
        ],
        "spellCrit" => [
            "regex" => "Improves your chance to get a critical strike with spells by ([\d|\.]*)%"
        ],
        "spellCritHoly" => [
            "regex" => "Increases the critical effect chance of your Holy spells by ([\d|\.]*)%"
        ],
        "spellHit" => [
            "regex" => "Improves your chance to hit with spells by ([\d|\.]*)%"
        ],
        "spellDmg" => [
            "regex" => "Increases damage and healing done by magical spells and effects by up to ([\d|\.]*)\."
        ],
        "spellDmgShadow" => [
            "regex" => "Increases damage done by Shadow spells and effects by up to ([\d|\.]*)\."
        ],
        "spellDmgFire" => [
            "regex" => "Increases damage done by Fire spells and effects by up to ([\d|\.]*)\."
        ],
        "spellDmgFrost" => [
            "regex" => "Increases damage done by Frost spells and effects by up to ([\d|\.]*)\."
        ],
        "spellDmgArcane" => [
            "regex" => "Increases damage done by Arcane spells and effects by up to ([\d|\.]*)\."
        ],
        "spellDmgNature" => [
            "regex" => "Increases damage done by Nature spells and effects by up to ([\d|\.]*)\."
        ],
        "healing" => [
            "regex" => "Increases healing done by spells and effects by up to ([\d|\.]*)\."
        ],
        "mana5" => [
            "regex" => "Restores ([\d|\.]*) mana per 5 sec\."
        ],
        "defense" => [
            "regex" => "Increased Defense \+([\d|\.]*)\."
        ],
        "parry" => [
            "regex" => "Increases your chance to parry an attack by ([\d|\.]*)%"
        ],
        "blockValue" => [
            "regex" => "Increases the block value of your shield by ([\d|\.]*)\.",
        ],
        "blockPct" => [
            "regex" => "Increases your chance to block attacks with a shield by ([\d|\.]*)%",
        ],
        "dodge" => [
            "regex" => "Increases your chance to dodge an attack by ([\d|\.]*)%"
        ],
        "frostRes" => [
            "regex" => "([\d|\.]*) Frost Resistance"
        ],
        "fireRes" => [
            "regex" => "([\d|\.]*) Fire Resistance"
        ],
        "shadowRes" => [
            "regex" => "([\d|\.]*) Shadow Resistance"
        ],
        "natureRes" => [
            "regex" => "([\d|\.]*) Nature Resistance"
        ]

    ];

    if ($element == null) {
        echo "No dom element found $itemName ($itemId)\n";
        continue;
    }


    // Remove Set:
    $strippedContents = $element->textContent;
    $strippedContents = preg_replace("/Set: [\s\S]*/", "", $strippedContents);

    // Remove Use:
    $strippedContents = preg_replace("/Use: [\s\S]*/", "", $strippedContents);

    // Remove Equip
    $strippedContents = preg_replace("/Equip: [\s\S]*/", "", $strippedContents);

    // Remove item set
    $strippedContents = preg_replace("/\(\d\/\d\)[\s\S]*/", "", $strippedContents);

    // Readd equip effects
    if (preg_match_all("/Equip: .*?\./", $element->textContent, $matches)) {
        foreach ($matches[0] as $match) {
            $strippedContents .= $match;
        }
    }

    $itemSlot = null;
    foreach ($types as $type) {
        $typeName = $type['typeName'];

        if (preg_match("/.*$typeName.*/", $strippedContents)) {
            $itemSlot = $typeName;
        }
    }

    if ($itemSlot == null) {
        echo "Skipping $itemName ($itemId) no matched typeslotname\n";
        continue;
    }

    // Match stats regex.
    foreach($itemStat as $key => $value) {
        $regEx = $itemStat[$key]['regex'];
        $itemStat[$key] = 0;
        if (preg_match_all("/$regEx/m", $strippedContents, $matches)) {
            foreach ($matches[1] as $match) {
                $floatValue = floatval($match);
                $itemStat[$key] += $floatValue;
            }
        }
    }

    $itemStat['slot'] = $itemSlot;

    foreach ($armorTypes as $armorType) {
        $armorTypeName = $armorType['armorTypeName'];
        if (preg_match("/.*$armorTypeName.*/", $strippedContents)) {
            $itemStat['type'] = $armorTypeName;
        }
    }

    $itemStat['itemId'] = $itemId;
    $itemStat['itemName'] = $itemName;
    $itemStat['uniqueItem'] = preg_match('/Unique/', $strippedContents) ? 1 : 0;

    // Parse item level.
    if (preg_match('/Level: (\d*)/', $contents, $matches)) {
        $itemStat['itemLevel'] = $matches[1];
    }

    // Parse required level.
    if (preg_match('/Requires Level (\d*)/', $contents, $matches)) {
        $itemStat['requiresLevel'] = $matches[1];
    }

    // Do something with the classes
    if (preg_match("/Classes: (.*?)Requires/", $strippedContents, $matches)) {
        foreach(explode(",", $matches[1]) as $className) {
            try {
                $query = "INSERT INTO item_stats_classes (itemId, className) VALUES (?, ?) ON DUPLICATE KEY UPDATE itemId=VALUES(itemId), className=VALUES(className)";
                $sql->execute($query, [$itemId, trim($className)]);
            } catch (Throwable $ex) {
                echo "$itemId $itemName";
                echo "$ex";
            }

        }
    }

    $keys = implode(",", array_keys($itemStat));
    $keysColon = implode(",:", array_keys($itemStat));
    $updateKeys = [];
    foreach (array_keys($itemStat) as $key) {
        $updateKeys[] = "$key=VALUES($key)";
    }
    $updateKeys = implode(",", $updateKeys);
    $query = "INSERT INTO item_stats ($keys) VALUES (:$keysColon) ON DUPLICATE KEY UPDATE $updateKeys";
    $sql->execute($query, $itemStat);

    sleep(1);
}

echo "All Done\n";