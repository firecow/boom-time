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

        $classes = $sql->raw("SELECT * FROM classes")->fetchAll();
        $classId = isset($_GET["classId"]) ? $sql->quote($_GET['classId']) : $sql->quote("{$classes[0]['classId']}");
        foreach ($classes as &$class) {
            $class['selectedAttr'] = "'{$class['classId']}'" == $classId ? "selected": "";
        }

        $specs = $sql->raw("SELECT * FROM specs WHERE classId = $classId")->fetchAll();
        $specCount = count($specs);
        $selectedSpecId = isset($_GET["specId"]) && $specCount > 0 ? $sql->quote($_GET["specId"]) : null;
        foreach ($specs as &$spec) {
            $spec['selectedAttr'] = "'{$spec['specId']}'" == $selectedSpecId ? "selected": "";
        }

        if ($selectedSpecId == null) {
            $characters = $sql->raw("SELECT * FROM characters WHERE classId = $classId")->fetchAll();
        } else {
            $characters = $sql->raw("SELECT * FROM characters WHERE classId = $classId AND specId = $selectedSpecId")->fetchAll();
        }

        // Get filtered items.
        $items = $sql->raw("
          SELECT * FROM characters
            JOIN items ON items.charName = characters.charName
            JOIN rarities ON items.rarity = rarities.rarity
            JOIN types ON items.type = types.type 
            JOIN type_groups ON types.group = type_groups.group
            LEFT JOIN disabled_items ON items.itemId = disabled_items.itemId
          WHERE characters.classId = $classId 
            AND characters.rank IS NOT NULL 
            AND rank < 6 
            AND items.level >= 52
            AND disabled_items.itemId IS NULL
          ORDER BY 
            characters.rank, 
            characters.highestPvpRank DESC, 
            characters.charName ASC, 
            items.type ASC, 
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
            "classes" => $classes,
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
