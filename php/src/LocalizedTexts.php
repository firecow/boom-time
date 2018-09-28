<?
declare(strict_types=1);

namespace App;

use App\Encoding\JSON;
use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;

class LocalizedTexts
{

    private $localizedTextsData;

    /**
     * LocalizedTexts constructor.
     * @throws FileErrorException
     * @throws FileNotFoundException
     */
    public function __construct()
    {
        $fileContent = File::loadFileTextContent("localizedtexts.json");
        $this->localizedTextsData = JSON::decode($fileContent);
    }

    public function getText(string $languageCode, string $key): string
    {
        $localizedTexts = $this->localizedTextsData[$languageCode];
        if (isset($localizedTexts[$key])) {
            return $localizedTexts[$key];
        }
        return "[$key]";
    }

}
