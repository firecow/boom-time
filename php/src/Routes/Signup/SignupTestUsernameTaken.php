<?
declare(strict_types=1);

namespace App\Routes\Signup;

use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Mocks\ContextMock;
use App\Mocks\SuperGlobalsMock;
use App\Router\RouteHandler;
use App\Testing\RegressionTest;

class SignupTestUsernameTaken extends RegressionTest
{

    /**
     * @return string
     * @throws FileErrorException
     * @throws FileNotFoundException
     */
    public function doTest(): string
    {
        $superGlobals = new SuperGlobalsMock();
        $superGlobals->setHTTPPostValue("username", "Mogberg");
        $superGlobals->setHTTPPostValue("password", "immaverybadpassword");
        $ctx = new ContextMock($superGlobals);
        $routeHandler = new RouteHandler($ctx);
        $routeResponse = $routeHandler->run("/rest/signup/");
        return $routeResponse->prettyPrint();
    }
}