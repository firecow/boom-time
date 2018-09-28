<?
declare(strict_types=1);

namespace App\Routes\UploadProfileImage;

use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Mocks\ContextMock;
use App\Mocks\SuperGlobalsMock;
use App\Router\RouteHandler;
use App\Testing\RegressionTest;

class UploadProfileImageTest extends RegressionTest
{

    /**
     * @return string
     * @throws FileErrorException
     * @throws FileNotFoundException
     */
    public function doTest(): string
    {
        $superGlobals = new SuperGlobalsMock();
        $superGlobals->setHTTPAuthorization("Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJzaW1wbGVwaHAuY29tIiwiaWF0IjoxNTE5MzA0NTIyLCJleHAiOjE1MTkzOTA5MjIsInN1YiI6Ijc0NTA3NTFmMzA3YmM3NmQ5NjBmMmQ3OTJjNjZmNzIwIiwiaW1nIjoiaHR0cDovL2ZpcmVjb3cuZGsvaW1hZ2VzL2JnLnBuZyIsInVuYSI6IkZpcmVjb3cifQ.uG03s_iBmB2Zmd3ZBf-MAnlmyWPRSPsUT9FLoH7CSGk");
        $superGlobals->setHTTPFilesData("image", array(
            "name" => "UploadProfileImageTest.png",
            "type" => "image\/png",
            "tmp_name" => "src/Routes/UploadProfileImage/UploadProfileImageTest.png",
            "error" => 0,
            "size" => 69376
        ));
        $ctx = new ContextMock($superGlobals);
        $routeHandler = new RouteHandler($ctx);
        $routeResponse = $routeHandler->run("/rest/uploadprofileimage/");
        return $routeResponse->prettyPrint();
    }
}