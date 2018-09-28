<?
declare(strict_types=1);

namespace App;

class Time
{

    public function nowInSeconds(): int
    {
        return intval($this->nowInMilliseconds() * 0.001);
    }

    public function nowInMilliseconds(): int
    {
        return intval(microtime(true) * 1000);
    }

}