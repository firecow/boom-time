<?
declare(strict_types=1);

namespace App\Header;

use App\Exceptions\DAOException;
use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Mocks\ContextMock;
use App\Mocks\SuperGlobalsMock;
use App\Testing\RegressionTest;

class HeaderTestAuthed extends RegressionTest
{

    /**
     * @throws FileErrorException
     * @throws FileNotFoundException
     * @throws DAOException
     */
    public function doTest(): string
    {
        $superGlobals = new SuperGlobalsMock();
        $superGlobals->setHTTPAuthorization("Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJzaW1wbGVwaHAuY29tIiwiaWF0IjoxNTE5MzA0NTIyLCJleHAiOjE1MTkzOTA5MjIsInN1YiI6Ijc0NTA3NTFmMzA3YmM3NmQ5NjBmMmQ3OTJjNjZmNzIwIiwiaW1nIjoiaHR0cDovL2ZpcmVjb3cuZGsvaW1hZ2VzL2JnLnBuZyIsInVuYSI6IkZpcmVjb3cifQ.uG03s_iBmB2Zmd3ZBf-MAnlmyWPRSPsUT9FLoH7CSGk");
        $ctx = new ContextMock($superGlobals);
        $html = Header::getHeaderHTML($ctx);
        return $html;
    }
}
