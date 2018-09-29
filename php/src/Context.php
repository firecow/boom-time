<?
declare(strict_types=1);

namespace App;

use App\DAL\SQL;
use App\Exceptions\JWTException;
use App\Exceptions\UnauthorizedRouteException;
use App\JWT\JWTHandler;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Context
{

    private $config;
    private $time;
    private $superGlobals;

    /**
     * Context constructor.
     * @param SuperGlobals $superGlobals
     */
    public function __construct(SuperGlobals $superGlobals)
    {
        $this->superGlobals = $superGlobals;
        $this->time = new Time();
        $this->config = new Config();
    }

    protected function getConfig(): Config
    {
        return $this->config;
    }

    public function getAccessToken(): ?string
    {
        $superGlobals = $this->getSuperGlobals();
        $bearerToken = null;

        // Get bearerToken from cookie
        if (isset($_COOKIE["bearerToken"])) {
            return $_COOKIE["bearerToken"];
        }

        // Get token from http auth authorization
        if ($superGlobals->haveHttpAuthorization() && preg_match('/Bearer\s(\S+)/', $superGlobals->getHttpAuthorization(), $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * @throws JWTException
     */
    protected function getUserIdFromAccessToken(): ?string
    {
        $bearerToken = $this->getAccessToken();
        if ($bearerToken !== null) {
            $jwtHandler = $this->createJWTHandler();
            $jwtPayload = $jwtHandler->decode($bearerToken);
            return $jwtPayload["sub"];
        }
        return null;
    }

    /**
     * @throws JWTException
     * @throws UnauthorizedRouteException
     */
    public function getAuthenticatedUserId(): string
    {
        $userIdFromBearer = $this->getUserIdFromAccessToken();
        if ($userIdFromBearer === null) {
            throw new UnauthorizedRouteException("Invalid or no access token");
        }
        return $userIdFromBearer;
    }

    /**
     * @throws JWTException
     */
    public function isUserAuthenticated(): bool
    {
        return $this->getUserIdFromAccessToken() !== null;
    }

    /**
     * @param string $facility
     * @return Logger
     * @throws Exception
     */
    public function createLogger(string $facility): Logger
    {
        $handler = new StreamHandler('php://stdout');
        return new Logger($facility, [$handler]);
    }

    public function createSQL(): SQL
    {
        $dataSourceName = $this->getConfig()->getPDODataSourceName();
        $username = $this->getConfig()->getPDOUsername();
        $password = $this->getConfig()->getPDOPassword();
        return new SQL($dataSourceName, $username, $password);
    }

    public function createJWTHandler(): JWTHandler
    {
        $secret = $this->getConfig()->getJWTSecret();
        return new JWTHandler($secret, $this->getTime());
    }

    public function getTime(): Time
    {
        return $this->time;
    }

    public function getSuperGlobals(): SuperGlobals
    {
        return $this->superGlobals;
    }

}