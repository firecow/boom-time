<?
declare(strict_types=1);

namespace App\Routes;

use App\Context;
use App\Exceptions\UnprocessableRouteException;
use App\LUAParser;
use App\ObjectPath\ObjectPath;
use App\Responses\JsonRouteResponse;
use App\Responses\RouteResponse;
use App\Router\Route;
use Exception;

class UploadCharacterProfilerLua extends Route
{

    /**
     * @param Context $ctx
     * @param array $routeArguments
     * @return RouteResponse
     * @throws Exception
     */
    public function executeRoute(Context $ctx, array $routeArguments): RouteResponse
    {
        $charName = $ctx->getAuthenticatedUserId();

        $jwtHandler = $ctx->createJWTHandler();
        $jwt = $ctx->getAccessToken();
        $payload = $jwtHandler->decode($jwt);
        $realm = $payload["realm"];
        $sql = $ctx->createSQL();

        $filesData = $ctx->getSuperGlobals()->getHTTPFilesData("file");
        if ($filesData["error"] !== 0) {
            throw new UnprocessableRouteException('$_FILES error');
        }

        file_put_contents("../data/character_profiler/$realm"."-"."$charName.lua", file_get_contents($filesData["tmp_name"]));

        $luaParser = new LUAParser();
        $luaParser->parseFile("../data/character_profiler/$realm"."-"."$charName.lua");

        $objectPath = new ObjectPath($luaParser->data);
        $obValue = $objectPath->get("myProfile[Kronos III][Guild][Members]");
        if ($obValue) {
            $members = $obValue->getPropertyValue();
            foreach ($members as $member) {
                $charName = $member["Name"];
                $officerNote = $member["OfficerNote"];
                $sql->execute("UPDATE characters SET officerNote = ? WHERE charName = ?", [$officerNote, $charName]);
            }
        }

        return new JsonRouteResponse($filesData);
    }
}
