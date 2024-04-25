<?php
use Application\BLL\Helpers\AvatarHelper;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\View\DisplayDefinitions\Button;
use Intouch\Framework\View\DisplayDefinitions\TableButton;
use Intouch\Framework\Widget\Action;
use Intouch\Framework\Widget\ActionButton;
use Intouch\Framework\Widget\ActionMenu;
use Intouch\Framework\Widget\ActionMenuItem;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\FaIconText;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\Image;
use Intouch\Framework\Widget\Menu;
use Intouch\Framework\Widget\Text;

// Analizar si vienen datos del usuario en la sesion
$usuario = null;

if (isset(Session::Instance()->usuario)) {

    $usuario = Session::Instance()->usuario;

    if (isset($usuario)) {

        if (isset($usuario->Contacto)) {
            $nombreUsuario  = $usuario->Nombre;
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
$genero = $usuario->Genero;
$avatar = AvatarHelper::GetAvatar($idCliente = null, $idUsuario = null, $avatar = null, $genero);
 
$AppName     =   SystemConfig::Instance()->ApplicationName ?: "App";
 
 

## --------------------------------------------------------------------------------------------------------------------------
## CONSTRUIMOS LA VISTA DEL TOOLBAR
## --------------------------------------------------------------------------------------------------------------------------
$ToolbarLogoContainer    =   new Container(
    Classes:['d-flex'],
    Children:[
        new Container(
            Classes: ['navbar-brand-box'],
            Children:[
            
                ## -------------------------------------------------------
                ## DEFINIMOS EL LOGO DEL MODO OSCURO
                ## -------------------------------------------------------
                new Html('<a href="index.php" class="logo logo-dark">
                <span class="logo-sm">
                    <img src="/assets/images/favicon.ico" alt="" height="24">
                </span>
                <span class="logo-lg">
                    <img src="/assets/images/favicon.ico" alt="" height="24"> <span class="logo-txt">'.$AppName.'</span>
                </span>
            </a>') ,
                ## -------------------------------------------------------
                ## DEFINIMOS EL LOGO DEL MODO CLARO
                ## -------------------------------------------------------
                new Container(
                    Classes     :   ['logo logo-light'],
                    Attributes  :   [["onclick","console.log('hola HOLA')"]],
                    Children:[
                        new Container(
                            Classes: ['logo-sm'],
                            Children:[
                                new Image(
                                    Source  : '/assets/images/favicon.ico',
                                    Height  : 24
                                ), 
                            ]
                        ),
                        new Container(
                            Classes: ['logo-lg'],
                            Children:[
                                new Image(
                                    Source: '/assets/images/favicon.ico',
                                    Height  : 24
                                ), 
                                new Html('<span class="logo-txt">'.$AppName.'</span>')
                            ]
                        )                                 
                    ]
                ) 
            ]
        ), 
        ## DEFINIMOS EL BTN DEL MENU
        // new Html('<button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn"><i class="fa fa-fw fa-bars"></i></button>'), 
        // new Html('<form class="app-search d-none d-lg-block">
        //         <div class="position-relative">
        //             <input type="text" class="form-control" placeholder="Search...">
        //             <button class="btn btn-primary" type="button"><i class="fa fa-search" aria-hidden="true"></i></button>
        //         </div>
        //     </form>')
    ]
);

## -----------------------------------------------------------------------------------------------------------------------------------
## CONSTRUIMOS LAS NOTIFICACIONES DEL SISTEMA
## -----------------------------------------------------------------------------------------------------------------------------------
$NotificacionDemo   =  new Html(
    '<a href="#!" class="text-reset notification-item">
    <div class="d-flex">
        <div class="flex-shrink-0 me-3">
            <img src="/assets/images/users/avatar-3.jpg" class="rounded-circle avatar-sm" alt="user-pic">
        </div>
        <div class="flex-grow-1">
            <h6 class="mb-1">James Lemire</h6>
            <div class="font-size-13 text-muted">
                <p class="mb-1">It will seem like simplified English.</p>
                <p class="mb-0"><i class="mdi mdi-clock-outline"></i> <span>1 hours ago</span></p>
            </div>
        </div>
    </div>
</a>'
) ;




## -----------------------------------------------------------------------------------------------------------------------------------
## CONSTRUIMOS EL TOOLBAR DE LOS USUARIOS | AQUI VAN LAS ACCIONES QUE PUEDE REALIZAR EL USUASRIO CON LA INTERFACE
## -----------------------------------------------------------------------------------------------------------------------------------


    // $adminMenu  =   new Container(
    //     Classes     :  ['dropdown d-inline-block'],
    //     Children    :[
    //     new Html('
    //          <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown_settings" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    //          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings icon-lg"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
    //         </button>'
    //     ),
    //     new Container(
    //         Classes : ['dropdown-menu dropdown-menu-end'],
    //         Styles  :   [
    //             ["position", "absolute"],
    //             ["inset", "0px auto auto 0px"],
    //             ["margin", "0px"],
    //             ["transform", "translate(-15px, 72px)"]
    //         ],
    //         Children: [
    //             new Action(
    //                 Action  :   "/Core/nuevaclave",
    //                 Classes :   ['dropdown-item'],
    //                 Child   :   new FaIconText(
    //                     Name    :   'fa-user',
    //                     Text    : 'Administrar usuarios'
    //                 )
    //                 ),
    //             new Action(
    //                 Action  :   "/Core/nuevaclave",
    //                 Classes :   ['dropdown-item'],
    //                 Child   :   new FaIconText(
    //                     Name    :   'fa-user',
    //                     Text    : 'Cambiar Contraseña'
    //                 )
    //             )
    //         ]
    //     )
    //     ]
    // );



 


$toolbarUserOption  =   new Container(
    Classes: ['d-flex'],
    Children: [
       
        // ## DEFINIMOS EL BTN DONDE SE GUARDARÁN LAS NOTIFICACIONES
        // new Container(
        //     Classes : ['dropdown d-inline-block'],
        //     Children: [
        //         ## DIBUJAMOS EL BTN QUE NOS PERMITIRÁ GENERAR LAS NOTIFICACIONES
        //         new Html('
        //             <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        //                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell icon-lg"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
        //                 <span class="badge bg-danger rounded-pill">5</span>
        //             </button>'
        //         ) ,
        //         new Container(
        //             Classes     :   ['dropdown-menu dropdown-menu-lg dropdown-menu-end p-0'],
        //             Attributes  :   [['aria-labelledby="','page-header-notifications-dropdown']],
        //             Children    :   [
        //                 ## CONSTRUIMOS EL HEADER DE LA VENTANA DE LAS NOTIFICACIONES
        //                 new Container(
        //                     Classes     :   ['p-3'],
        //                     Children    :   [
        //                         new Container(
        //                             Classes:['row align-items-center'],
        //                             Children:[
        //                                 new Container(
        //                                     Classes:['col'],
        //                                     Children:[
        //                                         new Text(
        //                                             Classes :   ['m-0'],
        //                                             Content :   'Notifications'
        //                                         )
        //                                     ]
        //                                 )
        //                             ]
        //                         )
        //                     ]
        //                 ) ,

        //                 ## COSNTRUIMOS LA VENTANA DONDE SE ALOJAN LAS NOTIFICACIONES
        //                 new Container(
        //                     Attributes: [['data-simplebar',"init"]],
        //                     Styles  :   [['height' ,'230px']],
        //                     Children: [
        //                         new Container(
        //                             Classes     : ['simplebar-wrapper'],
        //                             Styles      :   [ ["margin" , "0px"]] ,
        //                             Children    :[
        //                                 new Html('<div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div>'),
        //                                 new Container(
        //                                     Classes:['simplebar-mask'],
        //                                     Children:[
        //                                         new Container(
        //                                             Classes :   ['simplebar-offset'],
        //                                             Styles  :   [
        //                                                 ["right"    , "-20px"],
        //                                                 ["bottom"   , "0px"]
        //                                             ], 
        //                                             Children    :[
        //                                                 new Container(
        //                                                     Classes :   ['simplebar-content-wrapper'],
        //                                                     Styles  :   [
        //                                                         ["height", "auto"],
        //                                                         ["padding-right", "20px"],
        //                                                         ["padding-bottom", "0px"],
        //                                                         ["overflow", "hidden scroll"],
        //                                                     ],
        //                                                     Children: [
        //                                                         new Container(
        //                                                             Classes :   ['simplebar-content'],
        //                                                             Styles  :   [["padding", "0px"] ],
        //                                                             Children:   [
        //                                                                 $NotificacionDemo
        //                                                             ]
        //                                                         )
        //                                                     ]
        //                                                 )
        //                                             ]
        //                                         )
        //                                     ]
        //                                 )
        //                             ]
        //                         )
        //                     ]
        //                 ),
                        
        //                 ## CONSTRUIMOS EL BOTON PARA VER MÁS
        //                 new Html('
        //                     <div class="p-2 border-top d-grid">
        //                         <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
        //                             <i class="mdi mdi-arrow-right-circle me-1"></i> <span>View More</span> 
        //                         </a>
        //                     </div>'
        //                 )
        //             ]
        //         )
        //     ]
        // ) ,
        $adminMenu,
        new Container(
            Classes:['dropdown d-inline-block'],
            Children:[
                new Html(
                    '
                    <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="rounded-circle header-profile-user" src='.$avatar.' alt="Header Avatar">
                        <span class="d-none d-xl-inline-block ms-1 fw-medium">'.$nombreUsuario.'</span>
                        <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" style="">
                        <!-- item-->
                        
                        <a class="dropdown-item" href="https://sergioescobar.sd.cloud.invgate.net/">
                            Ir a mesa de ayuda
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="/core/logout">
                            <i class="mdi mdi-logout font-size-16 align-middle me-1"></i> <i class="fa fa-sign-out" aria-hidden="true"></i> Salir 
                        </a>
                    </div>
                    '
                )
            ]
        )
    ]
) ;


## GENERAMOS EL CONTENEDOR DEL WIDGET
$ToolbarWidgetContainer = new Container(
    Classes     :   ['navbar-header'],
    Children    :   [
        $ToolbarLogoContainer, 
        $toolbarUserOption
    ]
);





?>
 
<header id="page-topbar">
    <?php $ToolbarWidgetContainer->Draw()  ?>
</header>

 
 
<?php # $usuario->Menu->Draw(); ?>
 




































<!----------------------------------------------------------------------------------------------------------------------------------------->
<!---------------------------------     ELEMENTOS QUE PERMITEN LEVANTAR POPUPS SOBRE LA INTERFAZ ------------------------------------------>
<!----------------------------------------------------------------------------------------------------------------------------------------->
<div class="modal fade" id="popupModal"   role="dialog"  aria-hidden="true">
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