<?php
declare(strict_types=1);

namespace App\DAL;

use App\Exceptions\BadRequestRouteException;
use App\Time;
use InvalidArgumentException;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;

class NoSQL {

    private $client;
    private $time;

    public function __construct(string $mongoUri, Time $time)
    {
        $this->time = $time;
        $this->client = new Client($mongoUri);
    }

    public function selectCollection(string $dbName, string $collectionName): NoSQLCollection {
        $mongoCollection = $this->client->selectCollection($dbName, $collectionName);
        return new NoSQLCollection($mongoCollection);
    }

    public function createNowDate(): UTCDateTime
    {
        $timeInMilliseconds = $this->time->nowInMilliseconds();
        return new UTCDateTime($timeInMilliseconds);
    }

    public function createObjectId(?string $id = null): ObjectId
    {
        try {
            if ($id !== null) {
                $oid = new ObjectId($id);
            } else {
                $oid = new ObjectId();
            }
        } catch (InvalidArgumentException $ex) {
            throw new BadRequestRouteException("`$id` is not a well formed no sql id.");
        }
        return $oid;
    }
}