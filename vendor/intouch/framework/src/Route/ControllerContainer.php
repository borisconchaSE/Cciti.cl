<?php

namespace Intouch\Framework\Route;

use Intouch\Framework\Annotation\Attributes\CacheSingle;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Core\CacheableSingleton;
use Intouch\Framework\Route\RouteHelper;

#[CacheSingle]
class ControllerContainer extends CacheableSingleton {

    private static $Instance = null;

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }

    protected function __construct(public GenericCollection $controllers, public ?GenericCollection $methods = null) {}

    protected static function Create() {

        // Obtener los controladores
        $controllers = (new RouteHelper())->GetControllers();

        // Enumerar los métodos para búsquedas granulares
        $metodos = array();

        foreach($controllers as $controller) {
            if (isset($controller->ControllerMethodRoutes)) {
                foreach ($controller->ControllerMethodRoutes as $route) {
                    array_push($metodos, $route);
                }
            }
        }

        $controllerMethods = null;
        if (count($metodos) > 0) {
            $controllerMethods = new GenericCollection(
                DtoName : Route::class,
                Key     : 'FullPath',
                Values  : $metodos
            );
        }

        return new ControllerContainer(controllers: $controllers, methods: $controllerMethods);
    }

}