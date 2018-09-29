<?
declare(strict_types=1);

namespace App\Router\ErrorPage;

use App\Context;
use App\PHTML;
use App\Responses\RouteResponse;
use App\Responses\StatusCode;
use Throwable;

class InternalServerError
{
    public static function generateInternalServerErrorResponse(Context $ctx, Throwable $throwable): RouteResponse
    {
        $html = PHTML::create('src/Router/ErrorPage/InternalServerError.phtml', array(
            'pretty' => "$throwable"
        ), $ctx);
        return new RouteResponse(StatusCode::INTERNAL_SERVER_ERROR, "text/html", $html);
    }
}
