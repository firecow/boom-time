<?
declare(strict_types=1);

namespace App\Routes\Login;

use App\Context;
use App\DAL\UserDAO;
use App\Exceptions\BadRequestRouteException;
use App\Exceptions\DAOException;
use App\Exceptions\UnprocessableRouteException;
use App\JWT\JWTPayloads;
use App\Responses\JsonRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;

class Login extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws BadRequestRouteException
     * @throws DAOException
     * @throws UnprocessableRouteException
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $superGlobals = $ctx->getSuperGlobals();
        $username = $superGlobals->getHTTPPostValue("username");
        $password = $superGlobals->getHTTPPostValue("password");

        $passwordHasher = $ctx->getPasswordHasher();
        $userDao = new UserDAO($ctx->createSQL());

        if (!$userDao->isCredentialsValid($username, $password, $passwordHasher)) {
            throw new UnprocessableRouteException("username or password is incorrect.");
        }

        $userId = $userDao->getUserIdByUsername($username);
        $userData = $userDao->getUserData($userId);
        $time = $ctx->getTime();

        // Encode jwt payload.
        $jwtHandler = $ctx->createJWTHandler();
        $payload = JWTPayloads::createFromUserData($time, 86400, $userData);
        $token = $jwtHandler->encode($payload);

        $response = new JsonRouteResponse(array("jwt" => $token));
        $response->setCookie("bearerToken", $token, $payload["exp"]);
        return $response;
    }
}


