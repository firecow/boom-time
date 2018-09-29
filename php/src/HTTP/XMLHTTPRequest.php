<?php
declare(strict_types=1);

namespace App\HTTP;

use App\Encoding\JSON;

class XMLHTTPRequest
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

    public function sendFormPostData(array $data): XMLHTTPResponse
    {
        $boundary = md5(time());

        $this->headers["Content-type"] = "multipart/form-data; boundary=$boundary";

        $content = "";
        foreach ($data as $key => $value) {
            $content .= "--$boundary\n";
            $content .= "Content-Disposition: form-data; name=\"$key\"\n\n";
            $content .= "$value\n";
        }

        $content .= "--$boundary\n";
        $content .= "\n";

        return $this->execute($content, "POST");
    }

    public function sendJsonPostData(array $data): XMLHTTPResponse
    {
        $this->headers["Content-type"] = "application/javascript";
        $encodedContent = JSON::Encode($data);
        return $this->execute($encodedContent, "POST");
    }

    public function send(): XMLHTTPResponse
    {
        return $this->execute("", "GET");
    }

    private function execute(string $content, string $method): XMLHTTPResponse
    {

        // Generate header array from headers maps.
        $header = array();
        foreach ($this->headers as $key => $value) {
            $header[] = "$key: $value";
        }

        $opts = array(
            "http" => array(
                "ignore_errors" => true,
                "method" => $method,
                "header" => $header,
                "content" => $content
            ),
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false
            )
        );
        $url = $this->url;
        $ctx = stream_context_create($opts);
        $response = file_get_contents($url, false, $ctx);
        return new XMLHTTPResponse($response, $http_response_header ?? array());
    }

}