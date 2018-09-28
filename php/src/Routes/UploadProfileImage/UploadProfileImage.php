<?
declare(strict_types=1);

namespace App\Routes\UploadProfileImage;

use App\Context;
use App\Exceptions\BadRequestRouteException;
use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\UnprocessableRouteException;
use App\File;
use App\Responses\JsonRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use Exception;
use MongoDB\BSON\Binary;
use MongoDB\BSON\UTCDateTime;
use function getimagesize;

class UploadProfileImage extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws BadRequestRouteException
     * @throws FileErrorException
     * @throws FileNotFoundException
     * @throws UnprocessableRouteException
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $superGlobals = $ctx->getSuperGlobals();
        $filesData = $superGlobals->getHTTPFilesData("image");
        $error = $filesData["error"];
        if ($error !== 0) {
            throw new Exception("There was a file upload error");
        }
        $tmpImageFile = $filesData["tmp_name"];
        $imageInfo = getimagesize($tmpImageFile);
        $imageData = File::loadFileTextContent($tmpImageFile);
        $mime = $imageInfo["mime"];
        $imageWidth = $imageInfo[0];
        $imageHeight = $imageInfo[1];

        $noSQL = $ctx->createNoSQL();
        $collection = $noSQL->selectCollection("simple-php", "photos");
        $document = array(
            '_id' => $noSQL->createObjectId(),
            'mime' => $mime,
            'width' => $imageWidth,
            'height' => $imageHeight,
            'uploaded' => new UTCDateTime($ctx->getTime()->nowInMilliseconds()),
            'lastSeen' => null,
            'seenCount' => 0,
            'blob' => new Binary($imageData, Binary::TYPE_GENERIC)
        );
        $oid = $collection->insertOne($document);

        return new JsonRouteResponse(array(
            'photoId' => "$oid"
        ));
    }
}