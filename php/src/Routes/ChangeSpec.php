<?
declare(strict_types=1);

namespace App\Routes;

use App\Context;
use App\Exceptions\UnprocessableRouteException;
use App\Responses\JsonRouteResponse;
use App\Responses\PlainTextRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use Exception;

class ChangeSpec extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws Exception
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $sql = $ctx->createSQL();
        $newSpecId = $sql->quote($ctx->getSuperGlobals()->getHTTPPostValue("newSpecId"));
        $charName = $sql->quote($ctx->getSuperGlobals()->getHTTPPostValue("charName"));

        if ($newSpecId === "'nospec'") {
            $sql->raw("UPDATE characters SET specId = NULL WHERE charName = $charName");
        } else {
            $sql->raw("UPDATE characters SET specId = $newSpecId WHERE charName = $charName");
        }


        return new PlainTextRouteResponse("");
    }
}
