<?
declare(strict_types=1);

namespace App;

interface AuthenticatedUser
{

    public function isUserAuthenticated(): bool;

    public function getAuthenticatedUserId(): string;

}