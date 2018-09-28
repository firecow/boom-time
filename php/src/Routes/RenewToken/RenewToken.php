<?
declare(strict_types=1);

namespace App\Routes\RenewToken;

use App\Context;
use App\DAL\UserDAO;
use App\Exceptions\BadRequestRouteException;
use App\Exceptions\DAOException;
use App\Exceptions\JWTException;
use App\JWT\JWTPayloads;
use App\Responses\JsonRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;

class RenewToken extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws BadRequestRouteException
     * @throws DAOException
     * @throws JWTException
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $superGlobals = $ctx->getSuperGlobals();
        $jwt = $superGlobals->getHTTPPostValue("jwt");

        $jwtHandler = $ctx->createJWTHandler();

        $jwtPayload = $jwtHandler->decode($jwt);
        $userId = $jwtPayload["sub"];
        $userDao = new UserDAO($ctx->createSQL());
        $userData = $userDao->getUserData($userId);
        $time = $ctx->getTime();

        // Encode jwt payload.
        $payload = $payload = JWTPayloads::createFromUserData($time, 86400, $userData);
        $jwt = $jwtHandler->encode($payload);

        $response = new JsonRouteResponse(array("jwt" => $jwt));
        $response->setCookie("bearerToken", $jwt, $payload["exp"]);
        return $response;
    }
}
