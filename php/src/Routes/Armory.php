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
        $specs[] = ["specId" => "nospec", "specName" => "Unspecced"];
        $specCount = count($specs);
        $selectedSpecId = isset($_GET["specId"]) ? $sql->quote($_GET["specId"]) : '1';
        foreach ($specs as &$spec) {
            $spec['selectedAttr'] = "'{$spec['specId']}'" == $selectedSpecId ? "selected": "";
        }

        error_log($selectedSpecId);

        if ($selectedSpecId === "'nospec'") {
            $chars = $sql->raw("SELECT charName FROM characters WHERE specId IS NULL AND rank < 8")->fetchAll();
        } else {
            $chars = $sql->raw("SELECT charName FROM characters WHERE specId = $selectedSpecId AND rank < 8")->fetchAll();
        }

        $charNames = implode("','", array_map(function($char) { return $char['charName']; }, $chars));

        // Get filtered items.
        $items = $sql->raw("
          SELECT 
          characters.charName, 
          items.itemName, 
          items.enchant as enchant,
          items.icon,
          enchants.enchantName as enchantName,
          type_groups.group,
          rarities.color
          FROM characters
            JOIN items ON items.charName = characters.charName
            JOIN rarities ON items.rarity = rarities.rarity
            JOIN types ON items.type = types.type 
            JOIN type_groups ON types.group = type_groups.group
            LEFT JOIN disabled_items ON items.itemId = disabled_items.itemId
            LEFT JOIN enchants ON items.enchant = enchants.enchant
          WHERE characters.charName IN ('$charNames') 
            AND characters.rank IS NOT NULL 
            AND items.level >= 52
            AND disabled_items.itemId IS NULL
          ORDER BY
            characters.rank, 
            characters.highestPvpRank DESC, 
            characters.charName ASC, 
            items.level DESC, 
            items.rarity DESC, 
            items.itemName ASC;
        ")->fetchAll();

        // Extract charnames.
        $charNames = [];
        foreach ($items as $item) {
            $charNames[] = $item["charName"];
        }
        $charNames = array_unique($charNames);

        $data = [
            "title" => "Boom Time",
            "charNames" => $charNames,
            "types" => $types,
            "groupNames" => $groupNames,
            "specs" => $specs,
            "specCount" => $specCount,
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
