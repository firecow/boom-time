<?
declare(strict_types=1);

namespace App;

use App\Encoding\JSON;
use App\Exceptions\BadRequestRouteException;

class SuperGlobals
{

    /**
     * @param string $key
     * @return string
     * @throws BadRequestRouteException
     */
    public function getHTTPGetValue(string $key): string
    {
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        throw new BadRequestRouteException("`$key` in super global GET was not set");
    }

    /**
     * @param string $key
     * @return string
     * @throws BadRequestRouteException
     */
    public function getHTTPPostValue(string $key): string
    {
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        throw new BadRequestRouteException("`$key` in super global POST was not set");
    }

    /**
     * @param string $key
     * @return array
     * @throws BadRequestRouteException
     */
    public function getHTTPFilesData(string $key): array
    {
        if (isset($_FILES[$key])) {
            return $_FILES[$key];
        }
        throw new BadRequestRouteException("`$key` in super global FILES was not set");
    }

    public function haveHttpAuthorization(): bool
    {
        return isset($_SERVER['HTTP_AUTHORIZATION']);
    }

    public function getHttpAuthorization(): string
    {
        return $_SERVER['HTTP_AUTHORIZATION'];
    }

    public function getServerName(): string {
        return $_SERVER['SERVER_NAME'];
    }

    public function getServerPort(): string {
        return $_SERVER['SERVER_PORT'];
    }

    public function getRequestBodyAsJson(): array
    {
        $contents = File::loadFileTextContent('php://input');
        return JSON::decode($contents);
    }

}