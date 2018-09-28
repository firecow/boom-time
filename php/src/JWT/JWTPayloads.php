<?
declare(strict_types=1);

namespace App\JWT;

use App\Time;

class JWTPayloads
{

    public static function createFromUserData(Time $time, int $duration, array $userData): array
    {
        $mediaId = $userData["mediaId"];
        $img = $mediaId !== null ? "/photo/$mediaId" : null;

        return [
            "iss" => "simplephp.com",
            "iat" => $time->nowInSeconds() - 10, // 10 seconds leeway
            "exp" => ($time->nowInSeconds() + $duration),
            "sub" => $userData["userId"],
            "img" => $img,
            "una" => $userData["username"]
        ];
    }

}