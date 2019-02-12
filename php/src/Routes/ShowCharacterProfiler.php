<?
declare(strict_types=1);

namespace App\Routes;

use App\Context;
use App\Exceptions\NotFoundRouteException;
use App\IconHandler;
use App\LUAParser;
use App\ObjectPath\ObjectPath;
use App\PHTML;
use App\Responses\HtmlTextRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use Throwable;

class ShowCharacterProfiler extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws Throwable
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $charName = $routeArguments['charName'];
        $filePath = "../data/character_profiler/$charName";
        if (!file_exists($filePath)) {
            throw new NotFoundRouteException("(404) Cannot find '$charName'");
        }

        $luaParser = new LUAParser();
        $luaParser->parseFile($filePath);
        $strp = explode('-', str_replace("../data/character_profiler/", "", $filePath));

        $realm = str_replace("_", " ", $strp[0]);
        $charName = str_replace(".lua", "", $strp[1]);

        $objectPath = new ObjectPath($luaParser->data);
        $itemMap = $this->parseToItemMap($objectPath, $realm, $charName);

        $ob = $objectPath->get("myProfile[$realm][$charName][Money][Gold]");
        $gold = $ob ? $ob->getPropertyValue() : 0;
        $data = [
            "title" => $charName,
            "itemMap" => $itemMap,
            "gold" => $gold,
        ];
        $phtml = PHTML::create("src/Routes/ShowCharacterProfiler.phtml", $data, $ctx);
        return new HtmlTextRouteResponse($phtml);
    }

    private function getValueOrEmptyArray(ObjectPath $objectPath, string $path) {
        $objectPathValue = $objectPath->get($path);
        if ($objectPathValue != null) {
            return $objectPath->get($path)->getPropertyValue();
        }
        return [];
    }

    private function parseToItemMap(ObjectPath $objectPath, string $realm, string $charName): array {

        $items = array_merge(
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Bank][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Bank][Bag1][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Bank][Bag2][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Bank][Bag3][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Bank][Bag4][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Bank][Bag5][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Bank][Bag6][Contents]"),

            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Inventory][Bag0][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Inventory][Bag1][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Inventory][Bag2][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Inventory][Bag3][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Inventory][Bag4][Contents]"),
            $this->getValueOrEmptyArray($objectPath, "myProfile[$realm][$charName][Inventory][Bag5][Contents]")

        );
        $items = array_map(function($item) {
            $item["Quantity"] = (int)$item["Quantity"];
            $item["Color"] = "#" . substr($item["Color"], 2);
            $item["Id"] = $item["Item"];
            $colonPos = strpos($item["Id"], ":");
            $item["Id"] = substr($item["Id"], 0, $colonPos);
            $item["Texture"] = strtolower(str_replace("Interface\\\\Icons\\\\", "", $item["Texture"]));
            return $item;
        }, $items);

        $itemMap = [];
        foreach ($items as $item) {
            $itemName = $item["Name"];
            $iconName = $item["Texture"];

            if (isset($itemMap[$itemName])) {
                $itemMap[$itemName]["Quantity"] += $item["Quantity"];
            } else {
                $itemMap[$itemName] = $item;

                $imageData = base64_encode(file_get_contents($imagePath));
                $itemMap[$itemName]["DataURI"] = 'data: '.mime_content_type($imagePath).';base64,'.$imageData;
            }
        }
        return $itemMap;
    }
}
