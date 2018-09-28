<?
declare(strict_types=1);

namespace App\Encoding;

use Exception;

class JSON
{

    public static function encode(array $arr): string
    {
        $result = json_encode($arr, JSON_UNESCAPED_SLASHES);
        if (!$result) {
            throw new Exception("JSON encode failed");
        }
        return $result;
    }

    public static function encodePretty(array $arr): string
    {
        $result = json_encode($arr, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        if (!$result) {
            throw new Exception("JSON encode failed");
        }
        return $result;
    }

    public static function decode(string $str): array
    {
        return json_decode($str, true);
    }

}