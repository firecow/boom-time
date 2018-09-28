<?
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

use App\Context;
use App\Router\RouteHandler;
use App\SuperGlobals;

require 'vendor/autoload.php';
require 'error_web.php';

$phpSelf = $_SERVER['PHP_SELF'];

$superGlobals = new SuperGlobals();
$ctx = new Context($superGlobals);
$routeHandler = new RouteHandler($ctx);

$routeResponse = $routeHandler->run($phpSelf);
$statusCode = $routeResponse->getStatusCode();
$responseBody = $routeResponse->getResponseBody();

header("HTTP/1.1 $statusCode");
foreach ($routeResponse->getHeaders() as $key => $value) {
    header("$key: $value");
}

foreach ($routeResponse->getCookies() as $cookieName => $cookie) {
    setcookie(
        $cookieName,
        $cookie["value"],
        $cookie["expire"],
        $cookie["path"],
        $cookie['domain'],
        $cookie["secure"],
        $cookie["httponly"]
    );
}
echo "$responseBody\n";