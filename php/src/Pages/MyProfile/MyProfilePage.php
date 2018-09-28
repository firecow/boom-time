<?
declare(strict_types=1);

namespace App\Pages\MyProfile;

use App\Context;
use App\DAL\UserDAO;
use App\Pages\Page;
use App\PHTML;

class MyProfilePage extends Page
{

    public function executePage(Context $ctx): string
    {
        $userDao = new UserDAO($ctx->createSQL());
        $authenticatedUserId = $ctx->getAuthenticatedUserId();
        $userData = $userDao->getUserData($authenticatedUserId);
        $data = array(
            "username" => $userData["username"]
        );
        return PHTML::create("src/Pages/MyProfile/MyProfilePage.phtml", $data, $ctx);
    }
}
