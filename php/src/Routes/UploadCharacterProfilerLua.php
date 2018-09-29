<?
declare(strict_types=1);

namespace App\Routes;

use App\Context;
use App\Exceptions\UnprocessableRouteException;
use App\Responses\JsonRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use Exception;

class UploadCharacterProfilerLua extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws Exception
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $charName = $ctx->getAuthenticatedUserId();

        $jwtHandler = $ctx->createJWTHandler();
        $jwt = $ctx->getAccessToken();
        $payload = $jwtHandler->decode($jwt);
        $realm = $payload["realm"];

        $filesData = $ctx->getSuperGlobals()->getHTTPFilesData("file");
        if ($filesData["error"] !== 0) {
            throw new UnprocessableRouteException('$_FILES error');
        }

        file_put_contents("../data/character_profiler/$realm"."-"."$charName.lua", file_get_contents($filesData["tmp_name"]));
        return new JsonRouteResponse($filesData);
    }
}
