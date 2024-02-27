<?php

namespace Application\BLL\DataTransferObjects\Core;

class FuncionalidadDto {
    public $IdFuncionalidad = '';
    public $IdFuncionalidadPadre = '';
    public $Descripcion = '';
    public $Icono = '';
    public $Tipo = 'MENUITEM';
    public $Uri = '';
    public $Controller = '';
    public $Roles = array();
    public $IsMenu = 0;
    public $AuthorizationAction = 'Redirect'; // Enforce: error si no esta autorizado, Redirect: redirige a LOGIN si no esta autorizado, Allow: deja pasar si no esta autorizado
    public $ParseAllController = true;

    // Coleccion para armado de menus
    public $Items = array();

    public function __construct() {
    }

    public function withParameters($idFuncionalidad, $idFuncionalidadPadre, $descripcion, $icono, $uri, $roles, $isMenu, $authorizationAction, $tipo="MENUITEM") {
        $this->IdFuncionalidad = $idFuncionalidad;
        $this->IdFuncionalidadPadre = $idFuncionalidadPadre;
        $this->Descripcion = $descripcion;
        $this->Icono = $icono;
        $this->Tipo = $tipo;
        $this->Uri = $uri;
        $this->Roles = $roles;
        $this->IsMenu = $isMenu;
        $this->AuthorizationAction = $authorizationAction;
    }
}