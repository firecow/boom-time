<?
declare(strict_types=1);

namespace App\Exceptions;

use App\Responses\ContentType;
use App\Responses\ResponseCode;
use Throwable;

class UnauthorizedRouteException extends RouteException
{

    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct($message, ResponseCode::UNAUTHORIZED, ContentType::PLAIN_TEXT, $previous);
    }

}