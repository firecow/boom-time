<?
declare(strict_types=1);

namespace App;

use App\HTTP\XMLHTTPRequest;

class IconHandler
{

    public static function initIcon(string $iconName): string {
        if (!file_exists("../data/icons/$iconName.jpg")) {
            $httpRequest = new XMLHTTPRequest("http://classicdb.ch/images/icons/large/$iconName.jpg");
            $response = $httpRequest->send();
            file_put_contents("../data/icons/$iconName.jpg", $response->getResponseText());
        }
        return "../data/icons/$iconName.jpg";
    }

}