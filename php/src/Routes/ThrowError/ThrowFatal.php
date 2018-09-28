<?
declare(strict_types=1);

namespace App\Routes\ThrowError;

use App\Context;
use App\Responses\PlainTextRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use Exception;

class ThrowFatal extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws Exception
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        /** @noinspection PhpIncludeInspection */
        require "somethinginvalid.php";
        return new PlainTextRouteResponse("Fatal handler was not called as expected");
    }
}