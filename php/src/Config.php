<?
declare(strict_types=1);

namespace App;

class Config
{

    private $configData;

    /**
     * Config constructor.
     */
    public function __construct()
    {
        $this->configData = [
            "jwt" => [
                "secret" => "megasecret",
            ],
            "pdo" => [
                "dataSourceName" => "mysql:host=sql;dbname=simple-php",
                "username" => "root",
                "password" => "root"
            ],
            "mongo" => [
                "uri" =>"mongodb://mongo:27017"
            ]
        ];
    }

    public function getPDODataSourceName(): string
    {
        return $this->configData["pdo"]["dataSourceName"];
    }

    public function getPDOUsername(): string
    {
        return $this->configData["pdo"]["username"];
    }

    public function getPDOPassword(): string
    {
        return $this->configData["pdo"]["password"];
    }

    public function getMongoURI(): string
    {
        return $this->configData["mongo"]["uri"];
    }

    public function getMongoDatabase(): string
    {
        return $this->configData["mongo"]["database"];
    }

    public function getJWTDuration(): int
    {
        return $this->configData["jwt"]["duration"];
    }

    public function getJWTSecret(): string
    {
        return $this->configData["jwt"]["secret"];
    }

}