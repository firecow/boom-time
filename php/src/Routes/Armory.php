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
        $data = array(
            "title" => "Boom Time",
        );
        $html = PHTML::create("src/Routes/Armory.phtml", $data, $ctx);

        return new HtmlTextRouteResponse($html);
    }
}
