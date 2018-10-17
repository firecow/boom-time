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
            $filePath = "../data/icons/$iconName.jpg";
            file_put_contents($filePath, $response->getResponseText());
            chown($filePath, 'www-data');
            chgrp($filePath, 'www-data');
        }
        return "../data/icons/$iconName.jpg";
    }

}