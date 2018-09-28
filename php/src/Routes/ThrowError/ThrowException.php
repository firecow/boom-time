<?
declare(strict_types=1);

namespace App\Routes\ThrowError;

use App\Context;
use App\Responses\RouteResponse;
use App\Router\Route;
use Exception;

class ThrowException extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws Exception
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        throw new Exception("This is a manually thrown exception that is not supposed to be caught");
    }
}