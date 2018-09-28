<?
declare(strict_types=1);

namespace App\Router;

use App\Context;
use App\Exceptions\RouteException;
use App\Responses\RouteResponse;
use App\Router\ErrorPage\InternalServerError;
use App\Router\FourOhFourPage\FourOhFour;
use App\Routes\ChangePassword\ChangePassword;
use App\Routes\GetModalHTML;
use App\Routes\GetPageHTML;
use App\Routes\Layout\LayoutRoute;
use App\Routes\Login\Login;
use App\Routes\Logout\Logout;
use App\Routes\Photo\ShowPhoto;
use App\Routes\RenewToken\RenewToken;
use App\Routes\Signup\Signup;
use App\Routes\ThrowError\ThrowError;
use App\Routes\ThrowError\ThrowException;
use App\Routes\ThrowError\ThrowFatal;
use App\Routes\UploadProfileImage\UploadProfileImage;
use Throwable;

class RouteHandler
{

    private $routesQueries = [];

    private $ctx;

    public function __construct(Context $ctx)
    {
        $this->ctx = $ctx;

        $this->addRouteFunction("/", function() use ($ctx) {
            $route = new LayoutRoute();
            return $route->executeRoute($ctx, ["pageKey" => "/games/"]);
        });
        $this->addRouteFunction("/myprofile/", function() use ($ctx) {
            $route = new LayoutRoute();
            return $route->executeRoute($ctx, ["pageKey" => "/myprofile/"]);
        });
        $this->addRouteFunction("/profile/", function() use ($ctx) {
            $route = new LayoutRoute();
            return $route->executeRoute($ctx, ["pageKey" => "/profile/"]);
        });
        $this->addRouteFunction("/myprofile/", function() use ($ctx) {
            $route = new LayoutRoute();
            return $route->executeRoute($ctx, ["pageKey" => "/myprofile/"]);
        });
        $this->addRouteFunction("/games/", function() use ($ctx) {
            $route = new LayoutRoute();
            return $route->executeRoute($ctx, ["pageKey" => "/games/"]);
        });
        $this->addRouteFunction("/empty/", function() use ($ctx) {
            $route = new LayoutRoute();
            return $route->executeRoute($ctx, ["pageKey" => "/empty/"]);
        });
        $this->addRouteFunction("/photo/", function($endOfRoute) use ($ctx) {
            $route = new ShowPhoto();
            return $route->executeRoute($ctx, ["photoId" => $endOfRoute]);
        });

        $this->addRouteFunction("/rest/getpagehtml/", function() use ($ctx) {
            $route = new GetPageHTML();
            return $route->executeRoute($ctx, []);
        });
        $this->addRouteFunction("/rest/getmodalhtml/", function() use ($ctx) {
            $route = new GetModalHTML();
            return $route->executeRoute($ctx, []);
        });
        $this->addRouteFunction("/rest/login/", function() use ($ctx) {
            $route = new Login();
            return $route->executeRoute($ctx, []);
        });
        $this->addRouteFunction("/rest/logout/", function() use ($ctx) {
            $route = new Logout();
            return $route->executeRoute($ctx, []);
        });
        $this->addRouteFunction("/rest/renewtoken/", function() use ($ctx) {
            $route = new RenewToken();
            return $route->executeRoute($ctx, []);
        });
        $this->addRouteFunction("/rest/signup/", function() use ($ctx) {
            $route = new Signup();
            return $route->executeRoute($ctx, []);
        });
        $this->addRouteFunction("/rest/changepassword/", function() use ($ctx) {
            $route = new ChangePassword();
            return $route->executeRoute($ctx, []);
        });
        $this->addRouteFunction("/rest/uploadprofileimage/", function() use ($ctx) {
            $route = new UploadProfileImage();
            return $route->executeRoute($ctx, []);
        });

        $this->addRouteFunction("/throwexception/", function() use ($ctx) {
            $route = new ThrowException();
            return $route->executeRoute($ctx, []);
        });
        $this->addRouteFunction("/throwerror/", function() use ($ctx) {
            $route = new ThrowError();
            return $route->executeRoute($ctx, []);
        });
        $this->addRouteFunction("/throwfatal/", function() use ($ctx) {
            $route = new ThrowFatal();
            return $route->executeRoute($ctx, []);
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
