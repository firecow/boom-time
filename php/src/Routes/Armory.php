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

        $chars = $sql->raw("
          SELECT charName, classColor, signAttendance, attendance
          FROM characters
          JOIN classes ON classes.classId = characters.classId  
          WHERE COALESCE(specId, 'nospec') IN ('$selectedSpecs') 
            AND rank < 8 
            AND characters.rank IS NOT NULL 
            AND characters.level >= 58
          ORDER BY 
            characters.rank, 
            characters.highestPvpRank DESC, 
            characters.charName ASC
        ")->fetchAll();

        $charNames = array_map(function($char) { return $char['charName']; }, $chars);
        $implodedCharNames = implode("','", $charNames);

        $locations = [
            ["name" => "BWL", "icon" => "inv_misc_head_dragon_black.jpg"],
            ["name" => "MC", "icon" => "achievement_boss_ragnaros.jpg"],
            ["name" => "Onyxia", "icon" => "inv_misc_head_dragon_01.jpg"],
            ["name" => "Non-raid", "icon" => "inv_misc_questionmark.jpg"]
        ];


        $selectedLocations = isset($_GET["locations"]) ? $_GET["locations"] : [];
        foreach ($locations as &$location) {
            $location['checkedAttr'] = in_array($location['name'], $selectedLocations) ? "checked" : "";
        }
        $showBestInSlot = in_array('preraid-bis', $selectedLocations);
        $selectedLocations = implode("','", $selectedLocations);

        //AND (spec_best_in_slot.sbisId IS NOT NULL OR

        // Get filtered items.
        $items = $sql->raw("
          SELECT 
          items.charName, 
          items.itemName, 
          items.enchant as enchant,
          items.icon,
          enchants.enchantName as enchantName,
          type_groups.group,
          rarities.color
          FROM items
            JOIN rarities ON items.rarity = rarities.rarity
            JOIN types ON items.type = types.type 
            JOIN type_groups ON types.group = type_groups.group
            LEFT JOIN items_location ON items.itemId = items_location.itemId
            LEFT JOIN spec_best_in_slot ON spec_best_in_slot.itemId = items.itemId
            LEFT JOIN disabled_items ON items.itemId = disabled_items.itemId
            LEFT JOIN enchants ON items.enchant = enchants.enchant
          WHERE items.charName IN ('$implodedCharNames') 
            AND COALESCE(items_location.location, 'Non-raid') IN ('$selectedLocations')
            AND items.level >= 52
            AND disabled_items.itemId IS NULL
          ORDER BY
            items.level DESC, 
            items.rarity DESC, 
            items.itemName ASC;
        ")->fetchAll();

        $data = [
            "title" => "Boom Time",
            "chars" => $chars,
            "types" => $types,
            "groupNames" => $groupNames,
            "specs" => $specs,
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
