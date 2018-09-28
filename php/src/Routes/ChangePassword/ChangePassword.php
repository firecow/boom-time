<?
declare(strict_types=1);

namespace App\Routes\ChangePassword;

use App\Context;
use App\DAL\UserDAO;
use App\Exceptions\BadRequestRouteException;
use App\Exceptions\DAOException;
use App\Exceptions\JWTException;
use App\Exceptions\UnauthorizedRouteException;
use App\Exceptions\UnprocessableRouteException;
use App\Responses\JsonRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;

class ChangePassword extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws BadRequestRouteException
     * @throws DAOException
     * @throws JWTException
     * @throws UnauthorizedRouteException
     * @throws UnprocessableRouteException
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $superGlobals = $ctx->getSuperGlobals();
        $oldPassword = $superGlobals->getHTTPPostValue("oldPassword");
        $newPassword = $superGlobals->getHTTPPostValue("newPassword");

        if (!$ctx->isUserAuthenticated()) {
            throw new UnauthorizedRouteException("You are not authorized to see this page");
        }

        $passwordHasher = $ctx->getPasswordHasher();

        $userId = $ctx->getAuthenticatedUserId();
        $userDao = new UserDAO($ctx->createSQL());
        if (!$userDao->isPasswordValid($userId, $oldPassword, $passwordHasher)) {
            throw new UnprocessableRouteException("`oldPassword` does not match with the database");
        }

        $userDao->updatePassword($userId, $newPassword, $passwordHasher);

        return new JsonRouteResponse(array("success" => true));
    }
}
