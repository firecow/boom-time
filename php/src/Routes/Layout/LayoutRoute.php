<?
declare(strict_types=1);

namespace App\Routes\Layout;

use App\Context;
use App\Exceptions\DAOException;
use App\Exceptions\NotFoundRouteException;
use App\Exceptions\RouteException;
use App\Header\Header;
use App\Pages\Page;
use App\PHTML;
use App\Responses\HtmlTextRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use ReflectionException;

class LayoutRoute extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws DAOException
     * @throws NotFoundRouteException
     * @throws ReflectionException
     * @throws RouteException
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $pageKey = $routeArguments["pageKey"];
        $data = array(
            "title" => "SimplePHP",
            "subPageHTML" => Page::getPageHTML($ctx, $pageKey),
            "headerHTML" => Header::getHeaderHTML($ctx)
        );
        $html = PHTML::create("src/Routes/Layout/LayoutRoute.phtml", $data, $ctx);

        return new HtmlTextRouteResponse($html);
    }
}
