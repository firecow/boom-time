<?
declare(strict_types=1);

namespace App\Modals;

use App\Context;
use App\Exceptions\NotFoundRouteException;
use App\Exceptions\RouteException;
use ReflectionClass;
use ReflectionException;

abstract class Modal
{

    private static $modalMap = array(
        "login" => "App\\Modals\\Login\\LoginModal",
        "signup" => "App\\Modals\\Signup\\SignupModal",
    );

    /**
     * @param Context $ctx
     * @param string $modalKey
     * @return string
     * @throws NotFoundRouteException
     * @throws ReflectionException
     * @throws RouteException
     */
    public static function getModalHTML(Context $ctx, string $modalKey): string
    {
        $pagesMap = self::$modalMap;

        if (!isset($pagesMap[$modalKey])) {
            throw new NotFoundRouteException("'$modalKey' modal not found");
        }

        return self::createExecuteModal($ctx, $pagesMap[$modalKey]);
    }

    /**
     * @noinspection PhpDocRedundantThrowsInspection
     * @param Context $ctx
     * @param string $className
     * @return string
     * @throws ReflectionException
     */
    private static function createExecuteModal(Context $ctx, string $className): string
    {
        $page = new ReflectionClass($className);
        $instance = $page->newInstance();
        return $instance->executeModal($ctx);
    }

    public abstract function executeModal(Context $ctx): string;
}
