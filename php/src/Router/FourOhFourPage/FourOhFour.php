<?
declare(strict_types=1);

namespace App\Router\FourOhFourPage;

use App\Context;
use App\PHTML;
use App\Responses\RouteResponse;
use App\Responses\StatusCode;
use Throwable;

class FourOhFour
{
    /**
     * @param Context $ctx
     * @return RouteResponse
     * @throws Throwable
     */
    public static function generateFourOhFourResponse(Context $ctx): RouteResponse
    {
        $html = PHTML::create('src/Router/FourOhFourPage/FourOhFour.phtml', array(), $ctx);
        return new RouteResponse(StatusCode::NOT_FOUND, "text/html", $html);
    }
}
