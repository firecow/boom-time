<?
declare(strict_types=1);

namespace App\Router\ErrorPage;

use App\PHTML;
use App\PHTMLContext;
use App\Responses\RouteResponse;
use App\Responses\StatusCode;
use Throwable;

class InternalServerError
{
    public static function generateInternalServerErrorResponse(PHTMLContext $ctx, Throwable $throwable): RouteResponse
    {
        $html = PHTML::create('src/Router/ErrorPage/InternalServerError.phtml', array(
            'pretty' => "$throwable"
        ), $ctx);
        return new RouteResponse(StatusCode::INTERNAL_SERVER_ERROR, "text/html", $html);
    }
}
