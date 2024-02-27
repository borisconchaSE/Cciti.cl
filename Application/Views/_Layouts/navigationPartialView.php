<?php

use Application\BLL\BusinessObjects\Producto\EmpaqueBO;
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\DataTransferObjects\Core\PerfilDto;
use Application\BLL\DataTransferObjects\Core\ClienteDto;
use Application\BLL\Services\Core\PerfilSvc;
use Application\BLL\DataTransferObjects\Core\FuncionalidadDto;
use Application\Views\Widgets\TitleAndContent;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\Widget\ActionMenu;
use Intouch\Framework\Widget\ActionMenuItem;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\Container\Edge;
use Intouch\Framework\Widget\Definitions\Label\LabelSizeEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\Image;
use Intouch\Framework\Widget\Label;
use Intouch\Framework\Widget\Menu;
use Intouch\Framework\Widget\Panel;
use Intouch\Framework\Widget\Progress;
use Intouch\Framework\Widget\Text;
use Intouch\Framework\Widget\TitleDescription;


$usuario = null;

if (isset(Session::Instance()->usuario)) {

    $usuario = Session::Instance()->usuario;

    if (isset($usuario)) {

        if (isset($usuario->Contacto)) {
            $nombreUsuario  = $usuario->Contacto->Nombre;
            $cargo          = $usuario->Contacto->Cargo;
        }
        else {
            $nombreUsuario  = 'Usuario Actual';
            $cargo          = '---';
        }

        if (isset($usuario->Funcionalidades)) {
            $funcs = $usuario->Funcionalidades;
        }
    }
}




$stop = 1;


?>
<div class="vertical-menu">

    <div data-simplebar="init" class="h-100"><div class="simplebar-wrapper" style="margin: 0px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: -20px; bottom: 0px;"><div class="simplebar-content-wrapper" style="height: 100%; padding-right: 20px; padding-bottom: 0px; overflow: hidden scroll;"><div class="simplebar-content" style="padding: 0px;">
        <?php $usuario->Menu->Draw(); ?>     
    </div></div></div></div><div class="simplebar-placeholder" style="width: auto; height: 1261px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: visible;"><div class="simplebar-scrollbar" style="height: 90px; transform: translate3d(0px, 0px, 0px); display: block;"></div></div></div>
</div>