<?
declare(strict_types=1);

namespace App\Header;

use App\AuthenticatedUser;
use App\DAL\SQL;
use App\PHTMLContext;

interface HeaderContext extends AuthenticatedUser, PHTMLContext
{

    public function createSQL(): SQL;

}
