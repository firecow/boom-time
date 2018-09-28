<?php
declare(strict_types=1);

namespace App\DAL;

use App\Encoding\JSON;
use App\Exceptions\NotFoundRouteException;
use App\Exceptions\UnprocessableRouteException;
use MongoDB\BSON\ObjectId;
use MongoDB\Collection;

class NoSQLCollection {

    private $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function findOne(array $filter): array {
        $result = $this->collection->findOne($filter);
        if ($result === null) {
            $encodedFilter = JSON::encode($filter);
            throw new NotFoundRouteException("Could not find one `$encodedFilter`");
        }
        return (array)$result;
    }

    public function updateOne(array $filter, array $update): void {
       $result = $this->collection->updateOne($filter, $update);
       if (!$result->isAcknowledged()) {
           $encodedFilter = JSON::encode($filter);
           throw new UnprocessableRouteException("updateOne error: '$encodedFilter'");
       }
       if ($result->getModifiedCount() === 0) {
           $encodedFilter = JSON::encode($filter);
           throw new UnprocessableRouteException("updateOne didn't modify anything: '$encodedFilter'");
       }
    }

    public function insertOne(array $document): ObjectId {
        $result = $this->collection->insertOne($document);
        if (!$result->isAcknowledged()) {
            $encodedDoc = JSON::encode($document);
            throw new UnprocessableRouteException("insertOne error: '$encodedDoc'");
        }

        if ($result->getInsertedCount() === 0) {
            $encodedDoc = JSON::encode($document);
            throw new UnprocessableRouteException("insertOne didn't insert anything: '$encodedDoc'");
        }

        return $result->getInsertedId();
    }

}