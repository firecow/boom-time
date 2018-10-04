<?
/** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);

namespace App;

use Exception;
use Throwable;

class PHTML
{
    /**
     * @param string $phtmlPath
     * @param array $data
     * @param Context $ctx
     * @return string
     * @throws Throwable
     */
    public static function create(string $phtmlPath, array $data, Context $ctx): string
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $phtml = function (string $path, array $data) use ($ctx) {
            echo PHTML::create($path, $data, $ctx);
        };

        /** @noinspection PhpUnusedLocalVariableInspection */
        $d = function(string $dataKey) use ($data){
            return $data[$dataKey];
        };

        // Do not expose entire phtml context to .phtml files.
        unset($ctx);

        ob_start();
        try {
            /** @noinspection PhpIncludeInspection */
            require $phtmlPath;
        } catch (Throwable $ex) {
            ob_get_clean();
            throw $ex;
        }
        $obContent = ob_get_clean();
        if (!$obContent) {
            throw new Exception("ObjectBuffer content is false.");
        }
        return $obContent ? $obContent : "";
    }

}