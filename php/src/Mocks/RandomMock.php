<?
declare(strict_types=1);

namespace App\Mocks;

use App\Random;

class RandomMock extends Random
{

    public function __construct()
    {
        srand(382901830921);
    }

    public function getRandomMD5()
    {
        $rnd = rand();
        return md5("$rnd");
    }

}