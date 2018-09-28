<?
declare(strict_types=1);

namespace App;

class CLI
{

    public static function green(string $str)
    {
        return "\033[32m$str\e[0m";
    }

    public static function red(string $str)
    {
        return "\033[31m$str\e[0m";
    }

    public static function blue(string $str)
    {
        return "\033[1;34m$str\e[0m";
    }

}