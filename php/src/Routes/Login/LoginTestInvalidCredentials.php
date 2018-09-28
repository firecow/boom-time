<?
declare(strict_types=1);

namespace App\Routes\Login;

use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Mocks\ContextMock;
use App\Mocks\SuperGlobalsMock;
use App\Router\RouteHandler;
use App\Testing\RegressionTest;

class LoginTestInvalidCredentials extends RegressionTest
{

    /**
     * @return string
     * @throws FileErrorException
     * @throws FileNotFoundException
     */
    public function doTest(): string
    {
        $superGlobals = new SuperGlobalsMock();
        $superGlobals->setHTTPPostValue("username", "notacorrectusername");
        $superGlobals->setHTTPPostValue("password", "B9AF6C72E5CF65DB52A44E495BD027CB87A1E90CB1DDD45AEB17B96DE64D613");
        $ctx = new ContextMock($superGlobals);
        $routeHandler = new RouteHandler($ctx);
        $routeResponse = $routeHandler->run("/rest/login/");
        return $routeResponse->prettyPrint();
    }
}


