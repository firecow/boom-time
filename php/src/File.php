<?
declare(strict_types=1);

namespace App;

use App\Exceptions\FileErrorException;
use App\Exceptions\FileNotFoundException;

class File
{

    /**
     * @param string $filePath
     * @return string
     * @throws FileNotFoundException
     * @throws FileErrorException
     */
    public static function loadFileTextContent(string $filePath): string
    {
        if (!self::fileExists($filePath)) {
            throw new FileNotFoundException("File not found $filePath");
        }
        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new FileErrorException("File error. Could not get content $filePath");
        }
        return $content;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    private static function fileExists(string $filePath): bool
    {
        return file_exists($filePath);
    }

}