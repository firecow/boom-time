<?
declare(strict_types=1);

namespace App\Pages\Games;

use App\Context;
use App\DAL\GameDAO;
use App\Exceptions\DAOException;
use App\Pages\Page;
use App\PHTML;

class GamesPage extends Page
{

    /**
     * @param Context $ctx
     * @return string
     * @throws DAOException
     */
    public function executePage(Context $ctx): string
    {
        $gameDAO = new GameDAO($ctx->createSQL());
        $activeGames = $gameDAO->getActiveGames();

        $data = array(
            "games" => $activeGames
        );
        return PHTML::create("src/Pages/Games/GamesPage.phtml", $data, $ctx);
    }
}
