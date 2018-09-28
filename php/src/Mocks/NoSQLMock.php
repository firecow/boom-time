<?
declare(strict_types=1);

namespace App\Mocks;

use App\DAL\NoSQL;
use App\Random;
use App\Time;
use MongoDB\BSON\ObjectId;

class NoSQLMock extends NoSQL
{

    private $random;

    public function __construct(string $mongoUri, Time $time, Random $random)
    {
        parent::__construct($mongoUri, $time);

        $this->random = $random;
    }

    public function createObjectId(?string $id = null): ObjectId
    {
        if ($id !== null) {
            return new ObjectId($id);
        }

        $rnd = $this->random->getRandomMD5();
        $bin = sprintf(
            "%s%s%s%s",
            pack('N', $rnd),
            substr(md5($rnd), 0, 3),
            pack('n', $rnd),
            substr(pack('N', $rnd), 1, 3)
        );
        // Convert binary to hex.
        $result = '';
        for ($i = 0; $i < 12; $i++) {
            $result .= sprintf("%02x", ord($bin[$i]));
        }
        return new ObjectId($result);
    }


}