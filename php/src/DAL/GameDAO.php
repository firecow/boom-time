<?
declare(strict_types=1);

namespace App\DAL;

use App\Exceptions\DAOException;

class GameDAO
{
    private $sql;

    public function __construct(SQL $sql)
    {
        $this->sql = $sql;
    }

    /**
     * @throws DAOException
     */
    public function getActiveGames(): array
    {
        $sql = $this->sql;
        $statement = "SELECT * FROM games WHERE active = 1";
        return $sql->fetchAll($statement, []);
    }

}