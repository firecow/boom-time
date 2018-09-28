<?
declare(strict_types=1);

namespace App\Pages\Profile;

use App\Context;
use App\DAL\UserDAO;
use App\Pages\Page;
use App\PHTML;

class ProfilePage extends Page
{

    public function executePage(Context $ctx): string
    {
        $superGlobals = $ctx->getSuperGlobals();
        $username = $superGlobals->getHTTPGetValue("username");

        $userDao = new UserDAO($ctx->createSQL());
        $userId = $userDao->getUserIdByUsername($username);
        $userData = $userDao->getUserData($userId);
        $data = array(
            "username" => $userData["username"]
        );
        return PHTML::create("src/Pages/Profile/ProfilePage.phtml", $data, $ctx);
    }
}
