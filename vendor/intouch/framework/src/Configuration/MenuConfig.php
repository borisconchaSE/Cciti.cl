<?php

namespace Intouch\Framework\Configuration;

use Intouch\Framework\Annotation\Attributes\CacheMulti;
use Intouch\Framework\Annotation\Attributes\ConfigDetails;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Controllers\BaseController;
use Intouch\Framework\Mapping\BaseMapper;
use Intouch\Framework\Mensajes\Mensaje;
use Intouch\Framework\Route\BaseDispatcher;
use Intouch\Framework\Widget\ActionMenu;
use Intouch\Framework\Widget\ActionMenuItem;
use Intouch\Framework\Widget\ActionMenuMiniSeparator;
use Intouch\Framework\Widget\ActionMenuSeparator;
use Intouch\Framework\Widget\ActionMenuTitle;
use Intouch\Framework\Widget\FaIcon;
use Intouch\Framework\Widget\Menu;
use Intouch\Framework\Widget\MenuItem;

#[CacheMulti, ConfigDetails(name: 'menu.config.json')]
class MenuConfig extends BaseConfig {

    public $Description = '';
    public $Icon = '';
    public $Uri = '';
    public $Decorators = array();
    public $Items = array();
    
    private static $Instance = null;

    final protected static function GetInstance() {
        return self::$Instance;
    }

    final protected static function SetInstance($instance) {
        self::$Instance = $instance;
    }

    public static function FilterUserMenu(BaseDispatcher $dispatcher, $idioma, $locale) {

        $result = array();

        $menus   = self::Instance();
        $methods = $dispatcher->__ControllerContainer->methods;

        $userMenu = array();
        if (isset($methods) && isset($menus)) {

            foreach ($menus as $idMenu => $menu) {

                // Revisar si el menú está autorizado
                $authorizedMenu = self::GetMenuIfAuthorized($menu, $dispatcher, $idioma, $locale);

                // es menu suelto o tiene submenu?
                if (count($menu->Items) == 0) { // <- menú suelto sin hijos
                    if (isset($authorizedMenu)) {
                        $userMenu[$idMenu] = $authorizedMenu;
                    }                    
                }
                else { // <- menú principal con hijos

                    // Se deben chequear primero los hijos, si al menos tiene 1, entonces agregamos el menú padre
                    $childMenus = array();

                    foreach($menu->Items as $childMenu) {
                        $child = (new BaseMapper((object)$childMenu))->MapTo(MenuConfig::class);
                        $authorizedChildMenu = self::GetMenuIfAuthorized($child, $dispatcher, $idioma, $locale);

                        if (isset($authorizedChildMenu)) {
                            array_push($childMenus, $authorizedChildMenu);
                        }
                    }

                    // Revisar si exiten menus hijos
                    //
                    if (count($childMenus) > 0) {
                        // Si tiene hijos autorizados, autorizamos el menu padre también
                        $menu->Items = $childMenus;
                        $userMenu[$idMenu] = $menu;
                    }
                }
            }
        }

        return $userMenu;
    }

    private static function GetMenuIfAuthorized(MenuConfig $menu, BaseDispatcher $dispatcher, $idioma, $locale) {

        // Buscar primero la ruta URI del menu en los métodos
        //
        $methods = $dispatcher->__ControllerContainer->methods;
        $foundRoutes = $methods->Where('FullPath == "' . $menu->Uri . '"');

        // Si no encontramos el URI en los métodos, buscar si existe a nivel de controlador
        //
        if (!isset($foundRoutes) || !$foundRoutes || $foundRoutes->Count() == 0) {
            $found = array();        
            foreach($dispatcher->__ControllerContainer->controllers as $controller) {
                if ($controller->ControllerRoute->FullPath == $menu->Uri)
                    array_push($found, $controller->ControllerRoute);
            }

            if (count($found) > 0) {
                $foundRoutes = new GenericCollection(
                    Key       : 'FullPath',
                    DtoName   : Route::class,
                    Values    : $found
                );
            }
        }        

        if (isset($foundRoutes) && $foundRoutes && $foundRoutes->Count() > 0) {
            $route = $foundRoutes->First();

            // agregar si está autorizado o el menu es público
            //
            if ($route->Authorization == 'ALLOW_ALL' || $dispatcher->HasAnyRol($route->Roles)) {
                $result = new MenuConfig();

                if (strpos($menu->Description, '@@')) {
                    $descripcion = BaseController::RenderMensaje($menu->Description, '@@', $idioma, $locale);

                    foreach($descripcion as $key => $value) {
                        $result->Description = $value;
                        break;
                    }
                }
                else {
                    $result->Description = $menu->Description;
                }
                
                $result->Icon = $menu->Icon;
                $result->Uri = $menu->Uri;
                $result->Decorators = $menu->Decorators;
                $result->Items = array();

                foreach($result->Decorators as $decorator) {
                    if ($decorator['Type'] == 'TITULO' && strpos($decorator['Definition'], '@@')) {
                        $descripcion = BaseController::RenderMensaje($decorator['Definition'], '@@', $idioma, $locale);
    
                        foreach($descripcion as $key => $value) {
                            $decorator['Definition'] = $value;
                            break;
                        }
                    }
                }

                return $result;
            }
        }

        return null;
    }

    public static function GetUserMenuWidget($funcs) {

        $itemsMenuSistema = [];

        if (isset($funcs)) {
            foreach($funcs as $idFunc=>$func) {
                
                // Ver si es un menu suelto o un menu con items
                if (count($func->Items) == 0) {
                    $activa = ($_SERVER['REQUEST_URI'] == $func->Uri) ? " active " : "";
                     
        
                    $nuevoMenu = new MenuItem(
                        Title: $func->Description,
                        Action: $func->Uri,
                        Classes: [$activa],
                        Icon: ($func->Icon != '') ? new FaIcon(Name: $func->Icon, Styles: [['font-size','12px']]  ) : null,
                        Active: ($_SERVER['REQUEST_URI'] == $func->Uri)
                    );
                }
                else {
                    $activaMnu = false;
                    foreach ($func->Items as $idItem=>$item)
                    {
                        if ($_SERVER['REQUEST_URI'] == $item->Uri) {
                            $activaMnu = true;
                            break;
                        }
                    }
        
                    $menuItems = [];
        
                    foreach ($func->Items as $idItem=>$item)
                    {
                        $activa = ($_SERVER['REQUEST_URI'] == $item->Uri);
        
                        // Agregar decoradores
                        if (count($item->Decorators) > 0) {
                            foreach($item->Decorators as $decorator) {
                                if ($decorator['Type'] == 'TITULO') {
                                    array_push($menuItems, new ActionMenuTitle(
                                        Content: $decorator['Definition'],
                                        Classes: ['menu-titulo']
                                    ));
                                }
                                else if ($decorator['Type'] == 'SEPARATOR') {
                                    array_push($menuItems, new ActionMenuSeparator(
                                        Classes: ['menu-separador']
                                    ));
                                }
                                else if ($decorator['Type'] == 'MINISEPARATOR') {
                                    array_push($menuItems, new ActionMenuMiniSeparator(
                                        Classes: ['menu-mini-separador']
                                    ));
                                }
                            }
                        }

                        $icon = null;
                        if (isset($item->Icon) && $item->Icon != '') {
                            $icon = new FaIcon(
                                Name        :   $item->Icon,
                                Styles      :   [['font-size','12px']],
                                Classes     :   ['']
                            );
                        }
        
                        // Agregar el item de menu
                        array_push($menuItems, new ActionMenuItem(
                            Classes: [''],
                            Action: $item->Uri,
                            Content: $item->Description,
                            Icon: $icon,
                            Active: $activa
                        ));
                    }
        
                    $nuevoMenu = new ActionMenu(
                        Title: $func->Description,
                        Items: $menuItems,
                        Classes: [$activaMnu],
                        Active: $activaMnu,
                        Icon : $icon
                    );
                }
        
                array_push($itemsMenuSistema, $nuevoMenu);
            }
        }
        
        $menuSistema = new Menu(
            Items: $itemsMenuSistema,
            ContainerClasses: [],
            MenuClasses: ['metismenu list-unstyled mm-show ']
        );

        return $menuSistema;
    }
}