<?
declare(strict_types=1);

namespace App\Mocks;

use App\SuperGlobals;

class SuperGlobalsMock extends SuperGlobals
{

    /**
     * @var string|null
     */
    private $httpAuthorization = null;

    private $mockServerName = "0.0.0.0";
    private $mockServerPort = "3333";
    private $httpGetValues = array();
    private $httpPostValue = array();
    private $httpFilesData = array();

    public function getServerName(): string
    {
        return $this->mockServerName;
    }

    public function getServerPort(): string
    {
        return $this->mockServerPort;
    }

    public function setHTTPGetValue(string $key, string $value)
    {
        $this->httpGetValues[$key] = $value;
    }

    public function setHTTPPostValue(string $key, string $value)
    {
        $this->httpPostValue[$key] = $value;
    }

    public function setHTTPFilesData(string $key, array $filesData)
    {
        $this->httpFilesData[$key] = $filesData;
    }

    public function setHTTPAuthorization(string $value)
    {
        $this->httpAuthorization = $value;
    }

    public function haveHttpAuthorization(): bool
    {
        return $this->httpAuthorization !== null;
    }

    public function getHttpAuthorization(): string
    {
        if ($this->httpAuthorization !== null) {
            return $this->httpAuthorization;
        }
        return parent::getHttpAuthorization();
    }

    public function getHTTPGetValue(string $key): string
    {
        if (isset($this->httpGetValues[$key])) {
            return $this->httpGetValues[$key];
        }
        return parent::getHTTPGetValue($key);
    }

    public function getHTTPPostValue(string $key): string
    {
        if (isset($this->httpPostValue[$key])) {
            return $this->httpPostValue[$key];
        }
        return parent::getHTTPPostValue($key);
    }

    public function getHTTPFilesData(string $key): array
    {
        if (isset($this->httpFilesData[$key])) {
            return $this->httpFilesData[$key];
        }
        return parent::getHTTPFilesData($key);
    }

}