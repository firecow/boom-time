<?
declare(strict_types=1);

namespace App\Routes\Photo;

use App\Context;
use App\Exceptions\BadRequestRouteException;
use App\Exceptions\NotFoundRouteException;
use App\Exceptions\UnprocessableRouteException;
use App\Responses\RouteResponse;
use App\Responses\StatusCode;
use App\Router\Route;
use MongoDB\BSON\Binary;

class ShowPhoto extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws BadRequestRouteException
     * @throws NotFoundRouteException
     * @throws UnprocessableRouteException
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $photoId = $routeArguments["photoId"] ?? null;
        if ($photoId === null) {
            throw new BadRequestRouteException("`photoId` not supplied");
        }

        $noSql = $ctx->createNoSQL();
        $collection = $noSql->selectCollection("simple-php", "photos");
        $oid = $noSql->createObjectId($photoId);

        $result = $collection->findOne(array("_id" => $oid));

        /** @var Binary $blob */
        $blob = $result['blob'];
        $mime = $result['mime'];

        // Increment count and set date.
        $mongoDate = $noSql->createNowDate();
        $collection->updateOne(array("_id" => $oid), array('$inc' => array("seenCount" => 1), '$set' => array("lastSeen" => $mongoDate)));

        $response = new RouteResponse(StatusCode::OK, $mime, $blob->getData());
        $expiresTimestamp = $ctx->getTime()->nowInSeconds() + 15 * 60;
        $response->setExpiresHeader($expiresTimestamp); // Client is allowed to cache in 15 minutes.
        return $response;
    }
}
