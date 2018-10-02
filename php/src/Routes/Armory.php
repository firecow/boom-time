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
        $classId = 4;
        $sql = $ctx->createSQL();

        $tableHeaders = [""];

        $statement = $sql->raw("
          SELECT characters.charName as charName, items.itemName as itemName FROM characters
            JOIN items ON items.charName = characters.charName
          WHERE characters.classId = $classId AND characters.rank IS NOT NULL AND rank < 7
          ORDER BY characters.rank, characters.highestPvpRank DESC, characters.charName, items.level DESC, items.rarity DESC;
        ");

        $charItems = [];

        foreach ($statement as $row) {
            $tableHeaders[] = $row["charName"];
            $charItems[$row["charName"]] = $row['itemName'];
        }
        $tableHeaders = array_unique($tableHeaders);

        $data = [
            "title" => "Boom Time",
            "tableHeaders" => $tableHeaders,
            "charItems" => $charItems
        ];
        $html = PHTML::create("src/Routes/Armory.phtml", $data, $ctx);

        return new HtmlTextRouteResponse($html);
    }
}
