<?
declare(strict_types=1);

namespace App\Responses;

use App\Encoding\JSON;

class JsonRouteResponse extends RouteResponse
{

    public function __construct(array $responseBody)
    {
        $responseBodyEncoded = JSON::encode($responseBody);
        parent::__construct(ResponseCode::OK, ContentType::JSON, $responseBodyEncoded);
    }

}