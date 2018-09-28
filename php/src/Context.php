<?
declare(strict_types=1);

namespace App;

use App\DAL\NoSQL;
use App\DAL\SQL;
use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;
use App\Exceptions\JWTException;
use App\Exceptions\UnauthorizedRouteException;
use App\Header\HeaderContext;
use App\JWT\JWTHandler;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Context implements AuthenticatedUser, HeaderContext
{

    private $config;
    private $time;
    private $random;
    private $localizedTexts;
    private $superGlobals;
    private $passwordHasher;

    /**
     * Context constructor.
     * @param SuperGlobals $superGlobals
     * @throws FileErrorException
     * @throws FileNotFoundException
     * @throws Exception
     */
    public function __construct(SuperGlobals $superGlobals)
    {
        $this->superGlobals = $superGlobals;

        $this->random = new Random();
        $this->config = new Config();
        $this->passwordHasher = new PasswordHasher();
        $this->time = new Time();
        $this->localizedTexts = new LocalizedTexts();
    }

    protected function getConfig(): Config
    {
        return $this->config;
    }

    private function getAccessToken(): ?string
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


    public function getPasswordHasher(): PasswordHasher
    {
        return $this->passwordHasher;
    }

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

    public function createNoSQL(): NoSQL
    {
        $mongoUri = $this->getConfig()->getMongoURI();
        return new NoSQL($mongoUri, $this->getTime());
    }

    public function createJWTHandler(): JWTHandler
    {
        $secret = $this->getConfig()->getJWTSecret();
        return new JWTHandler($secret, $this->getTime());
    }

    public function getSuperGlobals(): SuperGlobals
    {
        return $this->superGlobals;
    }

    public function getTime(): Time
    {
        return $this->time;
    }

    public function getLocalizedTexts(): LocalizedTexts
    {
        return $this->localizedTexts;
    }

    public function getLanguageCode(): string
    {
        return "eng";
    }

    public function getRandom(): Random
    {
        return $this->random;
    }

}