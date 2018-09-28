<?
declare(strict_types=1);

namespace App\HTTP;

use App\Encoding\JSON;
use App\File;
use Exception;

class HTTPRequest
{
    private $url;
    private $headers = array();

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function setHeader(string $key, string $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * @param array $keyValues
     * @return HTTPResponse
     * @throws Exception
     */
    public function sendFormPostData(array $keyValues): HTTPResponse
    {
        $now = time();
        $boundary = md5("$now");

        $this->headers["Content-type"] = "multipart/form-data; boundary=$boundary";

        $content = "";
        foreach ($keyValues as $key => $value) {
            $content .= "--$boundary\n";
            $content .= "Content-Disposition: form-data; name=\"$key\"\n\n";
            $content .= "$value\n";
        }

        $content .= "--$boundary\n";
        $content .= "\n";

        return $this->execute($content, "POST");
    }

    /**
     * @param array $data
     * @return HTTPResponse
     * @throws Exception
     */
    public function sendJsonPostData(array $data): HTTPResponse
    {
        $this->headers["Content-type"] = "application/javascript";
        $encodedContent = JSON::encode($data);
        return $this->execute($encodedContent, "POST");
    }

    public function send()
    {
        return $this->execute("", "GET");
    }

    private function execute(string $content, string $method): HTTPResponse
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        $result = curl_exec($ch);
        if ($result === false) {
            $curlError = curl_error($ch);
            throw new Exception("Curl error: '$curlError'");
        }
        curl_close($ch);


        return new HTTPResponse($data, []);
    }

    public static function sendFromHttpFile(string $httpFilePath): HTTPResponse
    {
        $content = File::loadFileTextContent($httpFilePath);
        $lines = explode("\n", $content);

        $url = "";
        $method = "GET";
        $headers = [];
        $content = "";

        // Run throught the lines
        foreach ($lines as $index => $value) {
            if ($value === "###") {
                break;
            }

            // Match method and url;
            if (preg_match("/(GET|POST|PUT|DELETE)\s(http.*\/)/", $value, $matches)) {
                $url = $matches[2];
                $url = str_replace("localhost:3333", "nginx", $url);
                $method = $matches[1];
            } else if (preg_match("/(.*): ?(.*)/", $value, $matches)) {
                $headerKey = $matches[2];
                $headerValue = $matches[1];
                $headers[$headerKey] = $headerValue;
            } else {
                $content .= "$value\n";
            }

        }

        $httpRequest = new HTTPRequest($url);
        foreach ($headers as $headerKey => $headerValue) {
            $httpRequest->setHeader($headerKey, $headerValue);
        }
        return $httpRequest->execute($content, $method);
    }

}