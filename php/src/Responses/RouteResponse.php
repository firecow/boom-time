<?
declare(strict_types=1);

namespace App\Responses;

use App\Encoding\JSON;
use function md5;
use const DATE_COOKIE;

class RouteResponse
{

    private $statusCode;
    private $responseBody;
    private $cookies = array();
    private $headers = array();

    public function __construct(string $statusCode, string $contentType, string $responseBody)
    {
        $contentLength = strlen($responseBody);
        $this->setHeader("Content-Length", "$contentLength");
        $this->setHeader("Content-Type", $contentType);
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
    }

    public function getResponseBody(): string
    {
        return $this->responseBody;
    }

    public function getStatusCode(): string
    {
        return $this->statusCode;
    }

    public function getCookies(): iterable
    {
        return $this->cookies;
    }

    public function getHeaders(): iterable
    {
        return $this->headers;
    }

    public function setCookie(string $name, string $value, int $expire): void
    {
        $this->cookies[$name] = array(
            "value" => $value,
            "expire" => $expire,
            "path" => "/",
            "domain" => "",
            "secure" => false,
            "httponly" => true
        );
    }

    public function setExpiresHeader(int $unixTimestamp) {
        $this->setHeader("Expires", gmdate("D, d M Y H:i:s", $unixTimestamp) . " GMT");
    }

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function prettyPrint()
    {
        $status = $this->statusCode;
        $response = $this->responseBody;
        $contentType = $this->headers["Content-Type"];

        $result = "HTTP/1.1 $status";

        foreach ($this->headers as $key => $value) {
            $result .= "\n$key: $value";
        }

        foreach ($this->cookies as $cookieName => $cookie) {

            $cookieValue = $cookie["value"];
            $cookieExpire = $cookie["expire"];
            $readableExpireDate = date(DATE_COOKIE, $cookieExpire);
            $result .= "\n\n$cookieName=$cookieValue";
            $result .= "\nexpires=$readableExpireDate";
        }

        if ($contentType === "application/json") {
            $decodedResponse = JSON::decode($response);
            $prettyJsonString = JSON::encodePretty($decodedResponse);
            $result .= "\n\n" . $prettyJsonString;
        } else if ($contentType === "image/png") {
            $result .= "\n\nImage MD5: ". md5($response);
        } else {
            $result .= "\n\n" . $response;
        }

        return $result;
    }

}