<?
declare(strict_types=1);

namespace App\Exceptions;

use App\Responses\ContentType;
use App\Responses\ResponseCode;
use Throwable;

class ConflictRouteException extends RouteException
{

    /**
     * ConflictRouteException constructor.
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct($message, ResponseCode::CONFLICT, ContentType::PLAIN_TEXT, $previous);
    }

}