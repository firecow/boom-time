<?
declare(strict_types=1);

namespace App\Routes\ThrowError;

use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Mocks\ContextMock;
use App\Mocks\SuperGlobalsMock;
use App\Router\RouteHandler;
use App\Testing\RegressionTest;

class ThrowExceptionTest extends RegressionTest
{

    /**
     * @throws FileErrorException
     * @throws FileNotFoundException
     */
    public function doTest(): string
    {
        $superGlobals = new SuperGlobalsMock();
        $ctx = new ContextMock($superGlobals);
        $routeHandler = new RouteHandler($ctx);
        $routeResponse = $routeHandler->run("/throwexception/");
        return $routeResponse->getStatusCode();
    }
}