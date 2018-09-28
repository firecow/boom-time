<?
declare(strict_types=1);

namespace App\Routes\RenewToken;

use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Mocks\ContextMock;
use App\Mocks\SuperGlobalsMock;
use App\Router\RouteHandler;
use App\Testing\RegressionTest;

class RenewTokenTest extends RegressionTest
{

    /**
     * @return string
     * @throws FileErrorException
     * @throws FileNotFoundException
     */
    public function doTest(): string
    {
        $jwt = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJzaW1wbGVwaHAuY29tIiwiaWF0IjoxNTE5MzA0NTIyLCJleHAiOjE1MTkzOTA5MjIsInN1YiI6Ijc0NTA3NTFmMzA3YmM3NmQ5NjBmMmQ3OTJjNjZmNzIwIiwiaW1nIjoiaHR0cDovL2ZpcmVjb3cuZGsvaW1hZ2VzL2JnLnBuZyIsInVuYSI6IkZpcmVjb3ciLCJkb20iOiIxIn0._AtCMrPdbNLU41YhVIcCzpMW6K219HFS-ZMAO3n9v1s";
        $superGlobals = new SuperGlobalsMock();
        $superGlobals->setHTTPPostValue("jwt", $jwt);
        $ctx = new ContextMock($superGlobals);
        $routeHandler = new RouteHandler($ctx);
        $routeResponse = $routeHandler->run("/rest/renewtoken/");
        return $routeResponse->prettyPrint();
    }
}
