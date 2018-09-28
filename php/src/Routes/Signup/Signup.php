<?
declare(strict_types=1);

namespace App\Routes\Signup;

use App\Context;
use App\DAL\UserDAO;
use App\Exceptions\BadRequestRouteException;
use App\Exceptions\ConflictRouteException;
use App\Exceptions\DAOException;
use App\JWT\JWTPayloads;
use App\Responses\JsonRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;

class Signup extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws ConflictRouteException
     * @throws DAOException
     * @throws BadRequestRouteException
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $superGlobals = $ctx->getSuperGlobals();
        $username = $superGlobals->getHTTPPostValue("username");
        $password = $superGlobals->getHTTPPostValue("password");

        $userDao = new UserDAO($ctx->createSQL());

        if ($userDao->isUsernameTaken($username)) {
            throw new ConflictRouteException("Username is taken");
        }

        $passwordHasher = $ctx->getPasswordHasher();
        $time = $ctx->getTime();
        $random = $ctx->getRandom();
        $guid = $random->getRandomMD5();
        $userId = $userDao->createUser($guid, $username, $password, $passwordHasher);

        $userData = $userDao->getUserData($userId);

        // Encode jwt payload.
        $jwtHandler = $ctx->createJWTHandler();
        $payload = $payload = JWTPayloads::createFromUserData($time, 86400, $userData);
        $jwt = $jwtHandler->encode($payload);

        $response = new JsonRouteResponse(array("jwt" => $jwt));
        $response->setCookie("bearerToken", $jwt, $payload["exp"]);
        return $response;
    }
}
