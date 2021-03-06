<?
declare(strict_types=1);

namespace App\Routes;

use App\Context;
use App\PHTML;
use App\Responses\HtmlTextRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use Throwable;

class Armory extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws Throwable
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $sql = $ctx->createSQL();

        $selectedNames = isset($_GET["names"]) ? $_GET["names"] : "";
        $selectedNames = explode(",", $selectedNames);

        $lastSeenDays = isset($_GET["lastSeenDays"]) ? (int)$_GET["lastSeenDays"] : 15;
        $daysOld = isset($_GET["daysOld"]) ? (int)$_GET["daysOld"] : 3000;

        $groupNames = [];
        $types = $sql->raw("SELECT * FROM type_groups WHERE enabled = true")->fetchAll();
        foreach ($types as $type) {
            $groupNames[$type["group"]] = $type["groupName"];
        }

        $specs = $sql->raw("SELECT * FROM specs")->fetchAll();
        $specs[] = ["specId" => "nospec", "specName" => "No-Spec", "icon" => "inv_misc_questionmark.jpg"];
        $selectedSpecs = isset($_GET["specs"]) ? $_GET["specs"] : [];
        foreach ($specs as &$spec) {
            $spec['checkedAttr'] = in_array($spec['specId'], $selectedSpecs) ? "checked" : "";
        }
        $selectedSpecs = implode("','", $selectedSpecs);
        $implodedNames = implode("','", $selectedNames);

        $searchChars = $sql->raw("
          SELECT charName
          FROM characters
          JOIN classes ON classes.classId = characters.classId  
          ORDER BY 
            characters.charName ASC
        ")->fetchAll();

        $chars = $sql->raw("
          SELECT charName, classColor, signAttendance, attendance, raidsAttended, raidsPossible, officerNote, note, rankName
          FROM characters
          JOIN classes ON classes.classId = characters.classId  
          WHERE (COALESCE(specId, 'nospec') IN ('$selectedSpecs')
            AND rank < 8 
            AND characters.rank IS NOT NULL 
            AND characters.level >= 58) OR charName IN ('$implodedNames')
          ORDER BY 
            characters.attendance DESC,
            characters.rank, 
            characters.highestPvpRank DESC, 
            characters.charName ASC
        ")->fetchAll();
        $charNames = array_map(function($char) { return $char['charName']; }, $chars);
        $implodedCharNames = implode("','", $charNames);

        $locations = [
            ["name" => "AQ40", "icon" => "achievement_boss_cthun.jpg"],
            ["name" => "AQ20", "icon" => "achievement_boss_ossiriantheunscarred.jpg"],
            ["name" => "BWL", "icon" => "achievement_dungeon_blackwingdescent_raid_nefarian.jpg"],
            ["name" => "ZG", "icon" => "achievement_boss_hakkar.jpg"],
            ["name" => "MC", "icon" => "achievement_boss_ragnaros.jpg"],
            ["name" => "Onyxia", "icon" => "achievement_boss_onyxia.jpg"],
            ["name" => "Non-raid", "icon" => "inv_misc_questionmark.jpg"]
        ];
        $selectedLocations = isset($_GET["locations"]) ? $_GET["locations"] : [];
        foreach ($locations as &$location) {
            $location['checkedAttr'] = in_array($location['name'], $selectedLocations) ? "checked" : "";
        }
        $selectedLocations = implode("','", $selectedLocations);

        //(spellDmg + spellDmgFrost + intellect * 0.17 + spellCrit * 10 + spellHit * 13)

        $daysOldDate = $sql->quote(date("Y-m-d H:i:s", time() - $daysOld * 60 * 60 * 24));
        $dateClause = $sql->quote(date("Y-m-d H:i:s", time() - $lastSeenDays * 60 * 60 * 24));
        $query = "
          SELECT 
          items.charName, 
          items.itemName, 
          items.enchant as enchant,
          items.count,
          items.icon,
          enchants.enchantName as enchantName,
          type_groups.group,
          rarities.color
          FROM items
            JOIN rarities ON items.rarity = rarities.rarity
            JOIN types ON items.type = types.type 
            JOIN type_groups ON types.group = type_groups.group
            LEFT JOIN items_location ON items.itemId = items_location.itemId
            LEFT JOIN disabled_items ON items.itemId = disabled_items.itemId
            LEFT JOIN enchants ON items.enchant = enchants.enchant
          WHERE items.charName IN ('$implodedCharNames') 
            AND COALESCE(items_location.location, 'Non-raid') IN ('$selectedLocations')
            AND items.level >= 45
            AND disabled_items.itemId IS NULL
            AND items.lastSeen >= $dateClause
            AND items.firstSeen >= $daysOldDate
          ORDER BY
            items.level DESC, 
            items.rarity DESC, 
            items.itemName ASC;
        ";
        $items = $sql->raw($query)->fetchAll();

        $data = [
            "title" => "Boom Time",
            "chars" => $chars,
            "types" => $types,
            "groupNames" => $groupNames,
            "specs" => $specs,
            "selectedNames" => $selectedNames,
            "searchChars" => $searchChars,
            "lastSeenDays" => $lastSeenDays,
            "daysOld" => $daysOld,
            "locations" => $locations,
            "getItemsCount" => function(string $charName) use ($items) {
                return count(array_filter($items, function(array $item) use ($charName) {
                    return $charName === $item["charName"];
                }));
            },
            "getItems" => function(string $charName, int $group) use ($items) {
                return array_filter($items, function(array $item) use ($charName, $group) {
                    return $charName === $item["charName"] && $item['group'] === $group;
                });
            }
        ];
        $html = PHTML::create("src/Routes/Armory.phtml", $data, $ctx);

        return new HtmlTextRouteResponse($html);
    }
}
