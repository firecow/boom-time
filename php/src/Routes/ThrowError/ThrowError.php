<?
declare(strict_types=1);

namespace App\Routes\ThrowError;

use App\Context;
use App\Responses\PlainTextRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use Exception;

class ThrowError extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws Exception
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $result = 10 / 0;
        return new PlainTextRouteResponse("Error handler was not called as expected");
    }
}