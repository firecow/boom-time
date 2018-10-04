<?
declare(strict_types=1);

namespace App\Routes;

use App\Context;
use App\PHTML;
use App\Responses\HtmlTextRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use PDO;
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
        $classId = $sql->quote($_GET["class"] ?? "1");

        $charNames = [];
        $types = [];
        $typeNames = [];
        $items = [];

        $statement = $sql->raw("SELECT * FROM types WHERE enabled = true ORDER BY position");
        foreach ($statement as $row) {
            $typeNames[$row["type"]] = $row["typeName"];
            $types[] = $row["type"];
        }

        $statement = $sql->raw("
          SELECT * FROM characters
            JOIN items ON items.charName = characters.charName
            JOIN rarities ON items.rarity = rarities.rarity
          WHERE characters.classId = $classId 
            AND characters.rank IS NOT NULL 
            AND rank < 6 
            AND items.level >= 52
          ORDER BY 
            characters.rank, 
            characters.highestPvpRank DESC, 
            characters.charName ASC, 
            items.type ASC, 
            items.level DESC, 
            items.rarity DESC, 
            items.itemName ASC;
        ");

        foreach ($statement as $row) {
            $charNames[] = $row["charName"];
            $items[] = $row;
        }
        $charNames = array_unique($charNames);
        $types = array_unique($types);

        $data = [
            "title" => "Boom Time",
            "charNames" => $charNames,
            "types" => $types,
            "getTypeName" => function(int $type) use ($typeNames) {
                return $typeNames[$type] ?? "''$type''";
            },
            "classSelected" => function($class) use ($classId) {
                return strcmp("'$class'", $classId) == 0 ? "selected" : "";
            },
            "getItems" => function(string $charName, int $type) use ($items) {
                return array_filter($items, function(array $item) use ($charName, $type) {
                    return $charName === $item["charName"] && $item['type'] === $type;
                });
            }
        ];
        $html = PHTML::create("src/Routes/Armory.phtml", $data, $ctx);

        return new HtmlTextRouteResponse($html);
    }
}
