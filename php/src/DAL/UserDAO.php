<?
declare(strict_types=1);

namespace App\DAL;

use App\Exceptions\DAOException;
use App\PasswordHasher;

class UserDAO
{

    private $sql;

    public function __construct(SQL $sql)
    {
        $this->sql = $sql;
    }

    /**
     * @param string $username
     * @return bool
     * @throws DAOException
     */
    public function isUsernameTaken(string $username): bool
    {
        return $this->sql->fetchColumnInt("SELECT COUNT(*) FROM users WHERE username = ?", array($username)) > 0;
    }

    /**
     * @param string $username
     * @param string $password
     * @param PasswordHasher $passwordHasher
     * @return bool
     * @throws DAOException
     */
    public function isCredentialsValid(string $username, string $password, PasswordHasher $passwordHasher): bool
    {
        $statement = "SELECT COUNT(*) FROM users WHERE username = ?";
        $inputParams = array($username);
        $count = $this->sql->fetchColumnInt($statement, $inputParams);
        if ($count > 0) {
            $userId = $this->getUserIdByUsername($username);
            return $this->isPasswordValid($userId, $password, $passwordHasher);
        }
        return false;
    }

    /**
     * @param string $userId
     * @param string $password
     * @param PasswordHasher $passwordHasher
     * @return bool
     * @throws DAOException
     */
    public function isPasswordValid(string $userId, string $password, PasswordHasher $passwordHasher): bool
    {
        $statement = "SELECT password FROM user_passwords WHERE userId = ?";
        $inputParams = array($userId);
        $hashedPassword = $this->sql->fetchColumnString($statement, $inputParams);
        return $passwordHasher->verifyPassword($password, $hashedPassword);
    }

    /**
     * @param string $guid
     * @param string $username
     * @param string $password
     * @param PasswordHasher $passwordHasher
     * @return string
     * @throws DAOException
     */
    public function createUser(string $guid, string $username, string $password, PasswordHasher $passwordHasher): string
    {
        $sql = $this->sql;

        $hashedPassword = $passwordHasher->hashPassword($password);
        $statement = "INSERT INTO users (userId, username) VALUES (?, ?)";
        $inputParams = array($guid, $username);
        $sql->execute($statement, $inputParams);

        $statement = "INSERT INTO user_passwords (userId, password) VALUES (?, ?)";
        $inputParams = array($guid, $hashedPassword);
        $sql->execute($statement, $inputParams);
        return $guid;
    }

    /**
     * @param string $userId
     * @param string $password
     * @param PasswordHasher $passwordHasher
     * @throws DAOException
     */
    public function updatePassword(string $userId, string $password, PasswordHasher $passwordHasher): void
    {
        $sql = $this->sql;
        $hashedPassword = $passwordHasher->hashPassword($password);
        $sql->execute("UPDATE user_passwords SET password = ? WHERE userId = ?", array($hashedPassword, $userId));
    }

    /**
     * @param string $userId
     * @return array
     * @throws DAOException
     */
    public function getUserData(string $userId): array
    {
        $sql = $this->sql;
        return $sql->fetchAssoc("SELECT * FROM users WHERE userId = ?", array($userId));
    }

    /**
     * @param string $username
     * @return string
     * @throws DAOException
     */
    public function getUserIdByUsername(string $username): string
    {
        $sql = $this->sql;
        return $sql->fetchColumnString("SELECT userid FROM users WHERE username = ?", array($username));
    }

}