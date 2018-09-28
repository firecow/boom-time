<?
declare(strict_types=1);

namespace App\Pages\Games;

use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Mocks\ContextMock;
use App\Mocks\SuperGlobalsMock;
use App\Router\RouteHandler;
use App\Testing\RegressionTest;

class GamesPageTest extends RegressionTest
{

    /**
     * @throws FileErrorException
     * @throws FileNotFoundException
     */
    public function doTest(): string
    {
        $superGlobals = new SuperGlobalsMock();
        $superGlobals->setHTTPGetValue("pageKey", "/games/");
        $ctx = new ContextMock($superGlobals);
        $routeHandler = new RouteHandler($ctx);
        $routeResponse = $routeHandler->run("/rest/getpagehtml/");
        return $routeResponse->prettyPrint();
    }
}
