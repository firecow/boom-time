<?
declare(strict_types=1);

namespace App\Header;

use App\Exceptions\DAOException;
use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Mocks\ContextMock;
use App\Mocks\SuperGlobalsMock;
use App\Testing\RegressionTest;

class HeaderTestUnauthed extends RegressionTest
{

    /**
     * @throws FileErrorException
     * @throws FileNotFoundException
     * @throws DAOException
     */
    public function doTest(): string
    {
        $superGlobals = new SuperGlobalsMock();
        $ctx = new ContextMock($superGlobals);
        return Header::getHeaderHTML($ctx);
    }
}
