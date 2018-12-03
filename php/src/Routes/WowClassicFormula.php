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

        // TODO: Class to types.

        // Extract and default query parameters.
        $formula = isset($_GET["formula"]) ? $_GET["formula"] : "";
        $class = isset($_GET["class"]) ? $_GET["class"] : "";

        // Setup class specificness.
        $itemIdsToClasses = [];
        $statement = "SELECT * FROM item_stats_classes";
        $iter = $sql->execute($statement, []);
        foreach ($iter as $row) {
            if (!isset($itemIdsToClasses[$row['itemId']])) {
                $itemIdsToClasses[$row['itemId']] = [];
            }
            $itemIdsToClasses[$row['itemId']][] = $row['className'];
        }

        if (empty($formula)) {
            $statement = "SELECT *, 0 as gearpoint FROM item_stats";
        } else {
            $statement = "
              SELECT item_stats.*, ($formula) as gearpoint, location
              FROM item_stats
              LEFT JOIN items_location
                ON items_location.itemId = item_stats.itemId
              ORDER BY gearpoint DESC";
        }

        $iter = $sql->execute($statement, []);
        $slots = [];
        $items = [];
        foreach ($iter as $row) {
            $items[] = $row;
            $slots[] = $row['slot'];
        }
        $slots = array_unique($slots);
        usort($slots, function($a, $b) {
            return $a > $b;
        });

        $data =[
            "title" => "WowClassicFormula",
            "items" => $items,
            "slots" => $slots,
            "class" => $class,
            "formula" => $formula,
            "getItemsBySlot" => function(string $slot) use ($items, $itemIdsToClasses, $class) {
                $relevant = array_filter($items, function(array $item) use ($slot) {
                    if ($item['gearpoint'] > 0) {
                        return $slot === $item["slot"];
                    }
                    return false;
                });

                $i = 0;
                $lastNonRaidItemIndex = 0;
                $lastRaidItemIndex = 0;
                foreach ($relevant as $item) {
                    if ($item["location"] == null && $item["rarity"] !== "epic") {
                        $lastRaidItemIndex = $i;
                        break;
                    }
                    $i++;
                }

                $relevant = array_filter($relevant, function(array $item) use ($itemIdsToClasses, $class) {
                    if (empty($class)) {
                        return true;
                    }
                    $itemId = $item['itemId'];
                    if (isset($itemIdsToClasses[$itemId]) && !in_array($class, $itemIdsToClasses[$itemId])) {
                        return false;
                    }
                    return true;
                });

                $relevant = array_splice($relevant, 0, $lastRaidItemIndex + 5);
                return $relevant;
            },
        ];
        $html = PHTML::create("src/Routes/WowClassicFormula.phtml", $data, $ctx);

        return new HtmlTextRouteResponse($html);
    }
}
