<?
/** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);

namespace App;

use Exception;

class PHTML
{
    /**
     * @param string $phtmlPath
     * @param array $data
     * @param PHTMLContext $ctx
     * @return string
     * @throws Exception
     */
    public static function create(string $phtmlPath, array $data, PHTMLContext $ctx): string
    {
        $localizedText = $ctx->getLocalizedTexts();
        $languageCode = $ctx->getLanguageCode();

        /** @noinspection PhpUnusedLocalVariableInspection */
        $t = function (string $key, ...$args) use ($languageCode, $localizedText): string {
            return sprintf($localizedText->getText($languageCode, $key), ...$args);
        };

        /** @noinspection PhpUnusedLocalVariableInspection */
        $phtml = function (string $path, array $data) use ($ctx) {
            echo PHTML::create($path, $data, $ctx);
        };

        /** @noinspection PhpUnusedLocalVariableInspection */
        $d = function(string $opPath) use ($data) {
            return $data[$opPath];
        };

        // Do not expose entire phtml context to .phtml files.
        unset($ctx);

        ob_start();
        /** @noinspection PhpIncludeInspection */
        require $phtmlPath;
        $obContent = ob_get_clean();
        if (!$obContent) {
            throw new Exception("ObjectBuffer content is false.");
        }
        return $obContent ? $obContent : "";
    }

}