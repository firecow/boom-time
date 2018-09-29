<?php

declare(strict_types=1);

namespace App\HTTP;

class XMLHTTPResponse
{

    private $response;
    private $parsedHeaders;

    public function __construct(string $response, array $headers)
    {
        $this->response = $response;
        $this->parsedHeaders = XMLHTTPResponse::ParseHeaders($headers);
    }

    public static function parseHeaders($headers)
    {
        $head = array();
        foreach ($headers as $k => $v) {
            $t = explode(':', $v, 2);
            if (isset($t[1])) {
                $head[trim($t[0])] = trim($t[1]);
            } else {
                $head[] = $v;
                if (preg_match("#HTTP/[0-9\.]+\s+([0-9]+)#", $v, $out)) {
                    $head['reponse_code'] = intval($out[1]);
                }
            }
        }
        return $head;
    }

    public function getStatus(): string
    {
        return $this->parsedHeaders["0"] ?? "HTTP/1.1 408 Request Timeout";
    }

    public function getResponseText(): string
    {
        return $this->response;
    }
}