<?
declare(strict_types=1);

namespace App\Router\FourOhFourPage;

use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Mocks\ContextMock;
use App\Mocks\SuperGlobalsMock;
use App\Testing\RegressionTest;

class FourOhFourTest extends RegressionTest
{
    /**
     * @throws FileErrorException
     * @throws FileNotFoundException
     */
    public function doTest(): string
    {
        $superGlobals = new SuperGlobalsMock();
        $ctx = new ContextMock($superGlobals);
        $fourOhFourResponse = FourOhFour::generateFourOhFourResponse($ctx);
        return $fourOhFourResponse->prettyPrint();
    }
}
