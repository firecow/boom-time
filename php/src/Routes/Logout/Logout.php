<?
declare(strict_types=1);

namespace App\Routes\Logout;

use App\Context;
use App\Responses\PlainTextRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;

class Logout extends Route
{

    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $routeResponse = new PlainTextRouteResponse("Logged out successfully");
        $routeResponse->setCookie("bearerToken", "", -1);
        return $routeResponse;
    }
}




