<?
declare(strict_types=1);

namespace App\Router;

use App\Context;
use App\Responses\RouteResponse;

abstract class Route
{

    abstract public function executeRoute(Context $ctx, array $routeArguments): RouteResponse;

}