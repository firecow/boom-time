<?
declare(strict_types=1);

namespace App\Responses;

class PlainTextRouteResponse extends RouteResponse
{

    public function __construct(string $responseBody)
    {
        parent::__construct(ResponseCode::OK, ContentType::PLAIN_TEXT, $responseBody);
    }

}