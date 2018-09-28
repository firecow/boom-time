<?
declare(strict_types=1);

namespace App\Mocks;

use App\PasswordHasher;

class PasswordHasherMock extends PasswordHasher
{

    public function hashPassword(string $password): string
    {
        return md5("hashedpassword$password");
    }
}