<?
declare(strict_types=1);

namespace App\Router\FourOhFourPage;

use App\PHTML;
use App\PHTMLContext;
use App\Responses\RouteResponse;
use App\Responses\StatusCode;

class FourOhFour
{
    public static function generateFourOhFourResponse(PHTMLContext $ctx): RouteResponse
    {
        $html = PHTML::create('src/Router/FourOhFourPage/FourOhFour.phtml', array(), $ctx);
        return new RouteResponse(StatusCode::NOT_FOUND, "text/html", $html);
    }
}
