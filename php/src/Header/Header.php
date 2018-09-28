<?
declare(strict_types=1);

namespace App\Header;

use App\DAL\UserDAO;
use App\Exceptions\DAOException;
use App\PHTML;

class Header
{

    /**
     * @param HeaderContext $headerCtx
     * @return string
     * @throws DAOException
     */
    public static function getHeaderHTML(HeaderContext $headerCtx): string
    {
        $userIsAuthenticated = $headerCtx->isUserAuthenticated();
        if ($userIsAuthenticated) {
            $authenticatedUserId = $headerCtx->getAuthenticatedUserId();
            $userDAO = new UserDAO($headerCtx->createSQL());
            $userData = $userDAO->getUserData($authenticatedUserId);
            $data = array(
                "userData" => $userData
            );
            return PHTML::create("src/Header/HeaderLoggedIn.phtml", $data, $headerCtx);
        } else {
            return PHTML::create("src/Header/HeaderLoggedOut.phtml", array(), $headerCtx);
        }
    }

}
