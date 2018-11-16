<?
declare(strict_types=1);

namespace App\Routes;

use App\Context;
use App\PHTML;
use App\Responses\HtmlTextRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;

class WowClassicFormula extends Route
{

    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $sql = $ctx->createSQL();

        $formula = isset($_GET["formula"]) ? $_GET["formula"] : "";
        if (empty($formula)) {
            $statement = "SELECT *, 0 as gearpoint FROM item_stats";
        } else {
            $statement = "SELECT *, ($formula) as gearpoint FROM item_stats ORDER BY gearpoint DESC";
        }

        $iter = $sql->execute($statement, []);

        $slots = [];
        $items = [];
        foreach ($iter as $row) {
            $items[] = $row;
            $slots[] = $row['slot'];
        }
        $slots = array_unique($slots);

        $data =[
            "title" => "WowClassicFormula",
            "items" => $items,
            "slots" => $slots,
            "formula" => $formula,
            "getItemsBySlot" => function(string $slot) use ($items) {
                return array_filter($items, function(array $item) use ($slot) {
                    if ($item['gearpoint'] > 0) {
                        return $slot === $item["slot"];
                    }
                    return false;
                });
            },
        ];
        $html = PHTML::create("src/Routes/WowClassicFormula.phtml", $data, $ctx);

        return new HtmlTextRouteResponse($html);
    }
}
