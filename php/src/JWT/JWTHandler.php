<?
declare(strict_types=1);

namespace App\JWT;

use App\Encoding\Base64;
use App\Encoding\JSON;
use App\Exceptions\JWTException;
use App\Time;
use Exception;

/**
 * Class for operating with JWT's
 */
class JWTHandler
{

    private $secret;
    private $time;

    public function __construct(string $secret, Time $time)
    {
        $this->time = $time;
        $this->secret = $secret;
    }

    /**
     * @param string $jwt
     * @return array
     * @throws JWTException
     */
    public function decode(string $jwt): array
    {
        $split = explode(".", $jwt);

        if (count($split) < 3) {
            throw new JWTException("This is not a valid JWT");
        }

        // Fetch encoded parts from split
        $headerBase64Encoded = $split[0];
        $payloadBase64Encoded = $split[1];
        $signatureBase64Encoded = $split[2];

        // Decode header
        $headerDecoded = Base64::urlDecode($headerBase64Encoded);
        $headerDecoded = JSON::decode($headerDecoded);
        $headerType = $headerDecoded["typ"];
        if ($headerType !== "JWT") {
            throw new JWTException("Header typ not JWT: '$headerType'");
        }

        $headerAlgoritm = $headerDecoded["alg"];
        if ($headerAlgoritm !== "HS256") {
            throw new JWTException("Header alg not HS256: '$headerAlgoritm'");
        }

        // Decode payload
        $timeInSeconds = $this->time->nowInSeconds();
        $payloadDecoded = Base64::urlDecode($payloadBase64Encoded);
        $payloadDecoded = JSON::decode($payloadDecoded);
        if (isset($payloadDecoded["exp"]) && $payloadDecoded["exp"] < $timeInSeconds) {
            throw new JWTException("Expired");
        }

        // Reproduce signature and verify it.
        $secret = $this->secret;
        $reproducedSignature = hash_hmac("SHA256", "$headerBase64Encoded.$payloadBase64Encoded", $secret, true);
        $reproducedSignatureBase64Encoded = Base64::urlEncode($reproducedSignature);
        if (!hash_equals($signatureBase64Encoded, $reproducedSignatureBase64Encoded)) {
            throw new JWTException("Signature invalid");
        }

        return $payloadDecoded;
    }

    /**
     * @param array $payload
     * @return string
     * @throws Exception
     */
    public function encode(array $payload): string
    {
        // Generate header.
        $header = array(
            "typ" => "JWT",
            "alg" => "HS256"
        );

        // Encode header
        $headerJsonEncoded = JSON::encode($header);
        $headerBase64Encoded = Base64::urlEncode($headerJsonEncoded);

        // Encode payload
        $payloadJsonEncoded = JSON::encode($payload);
        $payloadBase64Encoded = Base64::urlEncode($payloadJsonEncoded);

        // Generate signature
        $secret = $this->secret;
        $signature = hash_hmac("SHA256", "$headerBase64Encoded.$payloadBase64Encoded", $secret, true);
        $signatureBase64Encoded = Base64::urlEncode($signature);

        // Combine encoded headed, encoded payload and encoded signature
        $jwt = "$headerBase64Encoded.$payloadBase64Encoded.$signatureBase64Encoded";
        return $jwt;
    }

}


