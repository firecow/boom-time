<?
declare(strict_types=1);

namespace App\Pages;

use App\Context;
use App\Exceptions\NotFoundRouteException;
use App\Exceptions\RouteException;
use ReflectionClass;
use ReflectionException;

abstract class Page
{

    private static $pagesMap = array(
        "/myprofile/" => "App\\Pages\\MyProfile\\MyProfilePage",
        "/profile/" => "App\\Pages\\Profile\\ProfilePage",
        "/games/" => "App\\Pages\\Games\\GamesPage",
        "/" => "App\\Pages\\Games\\GamesPage"
    );

    /**
     * @param Context $ctx
     * @param string $pageKey
     * @return string
     * @throws NotFoundRouteException
     * @throws ReflectionException
     * @throws RouteException
     */
    public static function getPageHTML(Context $ctx, string $pageKey): string
    {
        $pagesMap = self::$pagesMap;

        if (!isset($pagesMap[$pageKey])) {
            throw new NotFoundRouteException("'$pageKey' page not found");
        }

        return Page::createExecutePage($ctx, $pagesMap, $pageKey);
    }

    /**
     * @noinspection PhpDocRedundantThrowsInspection
     * @param Context $ctx
     * @param array $pagesMap
     * @param string $subPageKey
     * @return string
     * @throws ReflectionException
     * @throws RouteException
     */
    private static function createExecutePage(Context $ctx, array $pagesMap, string $subPageKey): string
    {
        $pageClassName = $pagesMap[$subPageKey];
        $page = new ReflectionClass($pageClassName);
        $instance = $page->newInstance();
        return $instance->executePage($ctx);
    }

    public abstract function executePage(Context $ctx): string;
}
