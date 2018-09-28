<?
declare(strict_types=1);

namespace App\Mocks;

use App\Time;

class TimeMock extends Time
{

    private $nowInMilliseconds;

    public function __construct(int $nowInMilliseconds)
    {
        $this->nowInMilliseconds = $nowInMilliseconds;
    }

    /**
     * @inheritdoc
     */
    public function nowInMilliseconds(): int
    {
        return $this->nowInMilliseconds;
    }

}