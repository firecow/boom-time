<?
declare(strict_types=1);

namespace App\Responses;

class HtmlTextRouteResponse extends RouteResponse
{

    public function __construct(string $responseBody)
    {
        parent::__construct(ResponseCode::OK, ContentType::HTML_TEXT, $responseBody);
    }

}