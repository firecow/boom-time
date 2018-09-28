<?
declare(strict_types=1);

namespace App\Routes\Layout;

use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Mocks\ContextMock;
use App\Mocks\SuperGlobalsMock;
use App\PHTML;
use App\Testing\RegressionTest;

class LayoutRouteTest extends RegressionTest
{
    /**
     * @throws FileErrorException
     * @throws FileNotFoundException
     */
    public function doTest(): string
    {
        $superGlobals = new SuperGlobalsMock();
        $ctx = new ContextMock($superGlobals);
        $data = array(
            "title" => "SimplePHP",
            "subPageHTML" => "Empty sub page html",
            "headerHTML" => "Empty header html"
        );
        return PHTML::create("src/Routes/Layout/LayoutRoute.phtml", $data, $ctx);
    }
}
