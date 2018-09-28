<?
declare(strict_types=1);

namespace App\Mocks;

use App\Context;
use App\DAL\NoSQL;
use App\PasswordHasher;
use App\Random;
use App\SuperGlobals;
use App\Time;

class ContextMock extends Context
{

    private $mockTime;
    private $mockRandom;
    private $mockSuperGlobals;
    private $mockPasswordHasher;

    public function __construct(SuperGlobalsMock $superGlobalsMock)
    {
        parent::__construct($superGlobalsMock);

        $this->mockTime = new TimeMock(1519304522000);
        $this->mockRandom = new RandomMock();
        $this->mockSuperGlobals = $superGlobalsMock;
        $this->mockPasswordHasher = new PasswordHasherMock();
    }

    public function getPasswordHasher(): PasswordHasher
    {
        return $this->mockPasswordHasher;
    }

    public function getTime(): Time
    {
        return $this->mockTime;
    }

    public function getRandom(): Random
    {
        return $this->mockRandom;
    }

    public function getSuperGlobals(): SuperGlobals
    {
        return $this->mockSuperGlobals;
    }

    public function createNoSQL(): NoSQL
    {
        $mongoUri = $this->getConfig()->getMongoURI();
        return new NoSQLMock($mongoUri, $this->getTime(), $this->getRandom());
    }


}