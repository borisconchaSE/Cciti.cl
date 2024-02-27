<?php

use Application\BLL\Helpers\AvatarHelper;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Mensajes\Mensaje;
use Intouch\Framework\Widget\Action;
use Intouch\Framework\Widget\ActionMenu;
use Intouch\Framework\Widget\ActionMenuItem;
use Intouch\Framework\Widget\ActionMenuSeparator;
use Intouch\Framework\Widget\ActionMenuTitle;
use Intouch\Framework\Widget\ActionMenuTree;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\FaIcon;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\Image;
use Intouch\Framework\Widget\Menu;
use Intouch\Framework\Widget\MenuItem;
use Intouch\Framework\Widget\Text;

// Analizar si vienen datos del usuario en la sesion
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

// Get the avatar
$avatar = AvatarHelper::GetAvatar();

// INICIALIZAR SISTEMA DE MENU DE LOGIN
//
$menuLogin = new Menu(
    Items : [
        new ActionMenu(
            Title: substr($cargo, 0, 29),
            Classes: ['pull-right spacer-both-md separator-left'],
            Styles: [
                ['margin-top', '-6px'],
                ['max-width', '195px'],
                ['white-space', 'nowrap']
            ],
            TitleClasses: ['text-personal-menu'],
            Items: [
                new ActionMenuItem(
                    Content: 'Cerrar Sesión', 
                    Action: '/core/logout'
                )
            ]
        ),
    ]    
    //, ContainerClasses: ['navbar-left']
    //, MenuClasses: ['nav', 'navbar-nav', 'no-borders']
);

// Agregar los elementos de idioma
//
if (SystemConfig::Instance()->ShowLanguageSelector) {
    $idiomas = Mensaje::ObtenerIdiomas();
    $productos = Mensaje::ObtenerProductos();

    if (isset($idiomas) && count($idiomas) > 0) {
        $itemsIdiomas = [];

        foreach ($productos as $producto) {
            // Solo se deben mostrar las opciones de idioma para ESTE PRODUCTO (el que el cliente tiene configurado)
            if ($producto == Session::Instance()->producto) {
                foreach($idiomas as $codigoIdioma => $idioma) {
                    if ($codigoIdioma == Session::Instance()->idioma ) {
                        $activo = "active";
                    }
                    else {
                        $activo = "";
                    }

                    $itemsIdiomas[$codigoIdioma] = new MenuItem(
                        Title: $idioma,
                        Action: '#',
                        // Attributes: [
                        //     ['data-idioma', $codigoIdioma],
                        //     ['data-locale', $producto . '_' . $codigoIdioma],
                        //     ['onclick', 'return false;']
                        // ]
                    );
                }
            }
        }

        if (count($itemsIdiomas) > 0) {
            $nuevoMenu = new ActionMenu(
                Key:'mnu-idioma',
                Title: Session::Instance()->idioma,
                Icon: new FaIcon('fa fa-globe')
            );

            array_push($itemsMenuSistema, $nuevoMenu);
        }
    }
}


(new Container(
    Classes: ['color-line'],
    Children: [
        new Html('')
    ]
))->Draw();

(new Container(
    Key: 'logo',
    Classes: ['light-version'],
    Children: [
        // Profile Picture
        new Container(
            Classes: ['profile-picture'],
            Children: [
                new Action(
                    Action: '/home',
                    Child: new Image(
                        Source: $avatar,
                        Title: 'avatar',
                        Classes: ['user-picture', 'm-b']
                    )
                ),
                new Container(
                    Classes: ['stats-label', 'text-color'],
                    Styles: [
                        ['padding-top', '8px']
                    ],
                    Children: [
                        new Text(
                            Classes: ['font-extra-bold', 'font-uppercase'],
                            Content: $nombreUsuario
                        ),
                        $menuLogin
                    ]
                )
            ]
        ),
        new Container(
            Classes: ['light-version-dark'],
            Children: [new Html('')]
        )
    ]
))->Draw();

?>

<nav role="navigation">        
    <div class="header-link side hide-menu"><i class="fa fa-bars"></i></div>
<?php
    // Dibujar el MENU DE SISTEMA
    //
    $usuario->Menu->Draw();
?>

</nav>

<div class="modal fade" id="popupModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div id="modal-size" class="modal-dialog">
        <div id="popupModal-modal-content" class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade" id="dynModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="color-line primary"></div>
            <div class="modal-header translate">
                <h4 class="modal-title" id="modal-title"></h4>
                <small class="font-bold" id="modal-description"></small>
            </div>
            <div class="modal-body_" id="modal-body_" style="height: auto;max-height: 600px;overflow-y: scroll;">
                <div id="modal-body" class="modal-body">
                </div>
            </div>
            <div class="modal-footer">
                <span id="dynModal-btn-aceptar-container" class='hide'></span>&nbsp;&nbsp;
                <span id="dynModal-btn-cerrar-container"></span>
                <!-- <button type="button" class="btn btn-default" data-dismiss="modal" id="dynModal-btn-cerrar">@@{BtnCerrar, Cerrar}</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="languageModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <input type="hidden" id="hidLanguageMessageId" value="">
            <input type="hidden" id="hidLanguageLocale" value="">
            <div class="color-line warning"></div>
            <div class="modal-header">
                <h4 class="modal-title" id="language-modal-title">Traducción</h4>
                <small class="font-bold" id="language-modal-description">Traducción de elemento de interfaz</small>
            </div>
            <div class="modal-body_" id="modal-body_" style="max-height: 400px; overflow-y: scroll">
                <div id="language-modal-body" class="modal-body">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="btn-guardar-traduccion">Guardar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>

<div class='hide' id='content-wait-modal'>
    <div>
        @@{LblCargandoContenido}<br>
        @@{LblWait}
    </div>
    <div >
        <div class="loader">
            <span class="ball"></span>
            <span class="ball2"></span>
            <ul>
                <li></li><li></li><li></li><li></li><li></li>
            </ul>
        </div>
    </div>
</div>