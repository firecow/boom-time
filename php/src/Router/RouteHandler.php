<?
declare(strict_types=1);

namespace App\Router;

use App\Context;
use App\Exceptions\RouteException;
use App\Responses\RouteResponse;
use App\Router\ErrorPage\InternalServerError;
use App\Router\FourOhFourPage\FourOhFour;
use App\Routes\Armory;
use Throwable;

class RouteHandler
{

    private $routesQueries = [];

    private $ctx;

    public function __construct(Context $ctx)
    {
        $this->ctx = $ctx;

        $this->addRouteFunction("/armory/", function($endOfRoute) use ($ctx) {
            $route = new Armory();
            return $route->executeRoute($ctx, ["photoId" => $endOfRoute]);
        });

    }

    private function addRouteFunction(string $uri, $func) {
        $this->routesQueries[$uri] = $func;
    }

    public function run(string $phpSelf): RouteResponse
    {
        $ctx = $this->ctx;
        $routeQueries = $this->routesQueries;

        preg_match("/(\/.*\/)(.*)/", $phpSelf, $matches);

        $routeWithoutArguments = $matches[1] ?? $phpSelf;
        $endOfRoute = $matches[2] ?? "";

        if (!isset($routeQueries[$routeWithoutArguments])) {
            return FourOhFour::generateFourOhFourResponse($ctx);
        }

        try {
            return $routeQueries[$routeWithoutArguments]($endOfRoute);
        }
        /** @noinspection PhpRedundantCatchClauseInspection */
        catch (RouteException $routeException) {
            $statusCode = $routeException->getStatusCode();
            $contentType = $routeException->getContentType();
            $requestBody = $routeException->getMessage();
            return new RouteResponse(
                $statusCode,
                $contentType,
                $requestBody
            );
        } catch (Throwable $throwable) {
            return InternalServerError::generateInternalServerErrorResponse($ctx, $throwable);
        }
    }

}
