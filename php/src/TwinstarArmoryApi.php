<?php

namespace App;

use App\DAL\SQL;
use App\HTTP\XMLHTTPRequest;
use SimpleXMLElement;

class TwinstarArmoryApi {

    public static function InitCharactersFromBoomTime(SQL $sql): void {
        $httpRequest = new XMLHTTPRequest("http://armory.twinstar.cz/guild-info.xml?r=KronosIII&gn=Boom+Time");
        $response = $httpRequest->send();
        $responseText = $response->getResponseText();
        $xml = simplexml_load_string($responseText);

        foreach ($xml->guildInfo->guild->members->children() as $child) {
            $name = "{$child["name"]}";
            $classId = "{$child["classId"]}";
            $raceId = "{$child["raceId"]}";
            $genderId = "{$child["genderId"]}";
            $level = "{$child["level"]}";
            $rank = "{$child["rank"]}";

            $query = "INSERT INTO characters (charName, level, rank, classId, raceId, genderId) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE level=VALUES(level), rank=VALUES(rank), classId=VALUES(classId), raceId=VALUES(raceId), genderId=VALUES(genderId)";
            $sql->execute($query, [$name, $level, $rank, $classId, $raceId, $genderId]);
        }
    }

    public static function InitCharacterByXML(SimpleXMLElement $xml, SQL $sql) {

        $charName = $xml->{"characterInfo"}->{"character"}["name"];
        $level = $xml->{"characterInfo"}->{"character"}["level"];
        $genderId = $xml->{"characterInfo"}->{"character"}["genderId"];
        $raceId = $xml->{"characterInfo"}->{"character"}["raceId"];
        $classId = $xml->{"characterInfo"}->{"character"}["classId"];
        $lastModified = date("Y-m-d H:i:s", strtotime($xml->{"characterInfo"}->{"character"}["lastModified"]));
        $rankHighest = $xml->{"characterInfo"}->{"character"}->{"honorRanking"}["rankHighest"];

        $query = "INSERT INTO characters (charName, level, classId, raceId, genderId, lastModified, highestPvpRank) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE level=VALUES(level), classId=VALUES(classId), raceId=VALUES(raceId), genderId=VALUES(genderId), lastModified=GREATEST(VALUES(lastModified), lastModified), highestPvpRank=GREATEST(VALUES(highestPvpRank), highestPvpRank)";
        $sql->execute($query, [$charName, $level, $classId, $raceId, $genderId, $lastModified, $rankHighest]);

        foreach ($xml->{"characterInfo"}->{"characterTab"}->{"items"}->children() as $child) {
            $icon = $child["icon"];
            $itemName = $child["name"];
            $slot = $child["slot"];
            $type = $child["inventoryType"];
            $itemId = $child["id"];
            $enchant = $child["permanentenchant"] == '0' ? null : $child["permanentenchant"];

            IconHandler::initIcon($icon);
            $query = "INSERT INTO items (charName, itemName, itemId, slot, type, enchant, icon, rarity, level, lastSeen, firstSeen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE slot=VALUES(slot), type=VALUES(type), enchant=COALESCE(VALUES(enchant), enchant), itemId=VALUES(itemId), icon=VALUES(icon), rarity=VALUES(rarity), level=VALUES(level)";
            $sql->execute($query, [$charName, $itemName, $itemId, $slot, $type, $enchant, $icon, $child["rarity"], $child["level"], $lastModified, $lastModified]);


            $query = "UPDATE items SET lastSeen=GREATEST(lastSeen, ?), firstSeen=LEAST(firstSeen, ?) WHERE charName=? AND itemName=?";
            $sql->execute($query, [$lastModified, $lastModified, $charName, $itemName]);
        }
//            $item['enchant'] = $child['permanentEnchantSpellName'] ? "{$child['permanentEnchantSpellName']}" : null;
//            if ($child['permanentEnchantItemId'] != null) {
//                $item['enchant'] = isset($enchantIds["{$child['permanentEnchantItemId']}"]) ? $enchantIds["{$child['permanentEnchantItemId']}"] : "Unknown: {$child['permanentEnchantItemId']}";
//            }
//        }
    }

}