<?
declare(strict_types=1);

namespace App\Routes;

use App\Context;
use App\Exceptions\BadRequestRouteException;
use App\Exceptions\NotFoundRouteException;
use App\Exceptions\RouteException;
use App\Pages\Page;
use App\Responses\HtmlTextRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use ReflectionException;

class GetPageHTML extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws NotFoundRouteException
     * @throws ReflectionException
     * @throws RouteException
     * @throws BadRequestRouteException
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $superGlobals = $ctx->getSuperGlobals();
        $pageKey = $superGlobals->getHTTPGetValue("pageKey");
        $html = Page::getPageHTML($ctx, $pageKey);

        return new HtmlTextRouteResponse($html);
    }
}
