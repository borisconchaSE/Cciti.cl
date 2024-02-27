<?php

use Application\BLL\BusinessEnumerations\RolesEnum;
use Application\BLL\BusinessEnumerations\TipoClaveEnum;
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\DataTransferObjects\Core\stockDto;
use Application\BLL\Services\Core\PerfilSvc;
use Application\Dao\Entities\Core\Contacto;
use Intouch\Framework\BLL\Filters\DataTableSettingsFilterDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\Button;
use Intouch\Framework\View\DisplayDefinitions\TableButton;
use Intouch\Framework\View\DisplayDefinitions\TableCell;
use Intouch\Framework\View\DisplayEvents\ButtonOnClickEvent;
use Intouch\Framework\View\DisplayEvents\Event;
use Intouch\Framework\View\DisplayEvents\TableButtonOnClickEvent;
use Intouch\Framework\Widget\Card;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;
use Intouch\Framework\Widget\FaIcon;
use Intouch\Framework\Widget\FaIconText;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\Label;
use Intouch\Framework\Widget\PageHeader;
use Intouch\Framework\Widget\Text;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Trunc;

use function PHPSTORM_META\map;

$AppName        =   SystemConfig::Instance()->ApplicationName ?: "App";
$Usuario        =   Session::Instance()->usuario;
$display        =   new Display();

## ----------------------------------------------------------------------------------------------
## CONSTRUIMOS EL HEADER DE LA PAGINA DE ADMINISTRACIÓN
## ----------------------------------------------------------------------------------------------
?>

@@Layout(authenticated)


    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Usuarios</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/"><?= $AppName ?></a></li>
                        <li class="breadcrumb-item active">Administración de Usuarios</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>
<!-- end page title --> 
<?php 


## ----------------------------------------------------------------------------------------------
## UNA VEZ DEFINIDO, PROCEDEMOS A GENERAR LA VISUALIZACIÓN DE LOS COMPONENTES DE LA TABLA
## ----------------------------------------------------------------------------------------------
$display->AddButton(
    new Button(
        Key             :   'btnNuevoUsuario',
        Child           :   new FaIconText('fa-plus-circle','Nuevo Usuario'),
        Classes         :   ['pull-right'],
        ButtonStyle     :   ButtonStyleEnum::BUTTON_SUCCESS,
        Events          :   [
            new ButtonOnClickEvent()
        ]
    )
) ;


$CantidadCuentas    =   !empty($data['ListaUsuarios']) ? $data['ListaUsuarios']->count() : 0;

$tableheader =  new Container(
    Classes     :   ['row align-items-center'],
    Children    :   [
        new Container(
            Classes     :['col-md-6'],
            Children    :   [
                new Container(
                    Classes     :   ['mb-3'],
                    Children    :   [
                        new Html('<h5 class="card-title">Lista de cuentas <span class="text-muted fw-normal ms-2">('.$CantidadCuentas.')</span></h5>')
                    ]
                )
            ]
        ) ,
        new Container(
            Classes:['col-md-6'],
            Children:[
                $display->Widgets()['btnNuevoUsuario'],
            ]
        ), 
          
    ]
); 
 





## COMENZAMOS A DIBUJAR LA TABLA

$cellDefinitions    =   [
    new TableCell(
        PropertyName: 'IdUsuario',
        Label: 'Id'      
    ),
    new TableCell(
        PropertyName: 'Nombre',
        Colspan: 2,
        Label: 'Nombre'
    ),
    new TableCell(
        PropertyName: 'Cargo',
        Colspan: 2,
        Label: 'Cargo'
    ),
    new TableCell(
        PropertyName: 'LoginName',
        Colspan: 2,
        Label: 'Correo'
    ),
    new TableCell(
        PropertyName: 'Eliminado',
        Colspan: 2,
        Label: 'Estado',
        FormatFunction  :   function( UsuarioDto $data,$cell){
            $stop = 1;
            if ($data->Eliminado == 1){
                return new Html('<center> <span class="badge badge-soft-danger">Desactivado</span> </center>');
            }else{
                return new Html('<center> <span class="badge badge-soft-success">Activo</span> </center>');
            }
        }
    ),
    new TableCell(
        PropertyName: 'IdTipoClave',
        Colspan: 1,
        Label: 'Tipo Clave',
        FormatFunction: function($data,$cell) {
            
            $Widget = new Text($data->IdTipoClave);

            if ($data->IdTipoClave == TipoClaveEnum::DEFINITIVA){
                return new Html('<center> <span class="badge badge-soft-info">Definitiva</span> </center>');
            }else{
                return new Html('<center> <span class="badge badge-soft-warning">Temporal</span> </center>');
            }

            return new Container(
                Classes:['center'],
                Children:[
                    $Widget
                ]
            );
        }
    ),
    new TableCell(
        PropertyName: 'Sigla',
        Label: 'Sigla',
        FormatFunction  :  function($data,$cell){

            return new Container(
                Classes:['center'],
                Children:[
                    new Container(
                        Classes:['profile-circle'],
                        Children:[
                            new Html($data->Sigla)
                        ]
                    )
                ]
            ) ;
        }
    ),
] ;


## ------------------------------------------------------------------------------
## VALIDAMOS LOS BOTONES A LOS QUE EL USUARIO TIENE ACCESO
## ------------------------------------------------------------------------------
$tableButtons    =   [];

## VALIDAMOS SI EL USUARIO TIENE PERMISOS PARA REINICIAR LA CONTRASEÑA DE LOS USUARIOS 

array_push($tableButtons,new TableButton(
    Key             :   'btnGenerarContraseña',
    Classes         :   ['btn-sm disabled'],
    Child           :   new FaIcon('fa-reply'),
    OnClickClass    :   'btnGenerarContraseña',
    TogglePopUp     :   true,
    ToggleText      :   'Desactivar Usuario',
    ButtonStyle     :   ButtonStyleEnum::BUTTON_PRIMARY,
    EnabledFunction : function($btn,$Usuario,$Perfil,$Roles, $data) {
        PerfilSvc::HasRol($Usuario->Perfil, RolesEnum::R_ADMIN_CHANGE_OTHER_USER_PASSWORD);
    }
));

 

## VALIDAMOS SI EL USUARIO TIENE PERMISOS PARA EDITAR LOS USUARIOS
 
array_push($tableButtons,new TableButton(
    Key             :   'btnEditarUsuario',
    Child           :   new FaIcon('fa-edit'),
    Classes         :   ['btn-sm'],
    OnClickClass    :   'btnEditarUsuario',
    TogglePopUp     :   true,
    ToggleText      :   'Editar Usuario',
    ButtonStyle     :   ButtonStyleEnum::BUTTON_WARNING,
    Events          :   [ new TableButtonOnClickEvent() ],
  
)); 
  

## VALIDAMOS SI EL USUARIO TIENE PERMISOS PARA ACTIVAR / DESACTIVAR USUARIOS 
array_push($tableButtons,        new TableButton(
    Key             :   'btnDesactivarUsuario',
    Classes         :   ['btn-sm'],
    Child           :   new FaIcon('fa-power-off'),
    OnClickClass    :   'btnDesactivarUsuario',
    TogglePopUp     :   true,
    ToggleText      :   'Desactivar Usuario',
    ButtonStyle     :   ButtonStyleEnum::BUTTON_DANGER,
    Events          :   [ new TableButtonOnClickEvent() ],
    DisplayFunction : function($btn,$Usuario,$Perfil,$Roles, $data) { 
        if ($data->Eliminado == 0) {
            $btn->ButtonStyle   =   "btn-danger";
            $btn->ToggleText    =   "Desactivar Usuario";
        }else{
            $btn->ButtonStyle   =   "btn-success";
            $btn->ToggleText    =   "Activar Usuario";
        }
        return true;
    },
    EnabledFunction : function($btn,$Usuario,$Perfil,$Roles, $data) {
        $status =  PerfilSvc::HasRol($Perfil, RolesEnum::R_ADMIN_ENABLE_DISABLE_USERS);
        return $status;
    }
    
));

 



$display->AddTableFromCollection(
    tableKey: 'tbListadoUsuarios',
    RowIdFieldName: 'IdUsuario',
    RowAttributeNames: ['IdUsuario'],
    CellDefinitions: $cellDefinitions,
    Data: $data['ListaUsuarios'],
    Buttons: $tableButtons,
    TablaSimple: false,
    CustomDataTable: new DataTableSettingsFilterDto(
        HideAllButtons  : true,
        CustomPdf       : false,
        TableHasButtons : false,
    )
);


$content    =   new Container(
    Classes     :   ['row'],
    Children    :   [ 
        new Container(
            Classes     :   ['col-lg-12'],
            Children    :   [
                $tableheader,
                new Card(
                    Children:[
                        new Html('<br>'),
                        $display->Widgets()['tbListadoUsuarios']
                    ]
                )
            ]
        )
    ]
);





 

## DIBUJAMOS LA TABLA COMO TAL
$content->Draw();


## DIBUJAMOS LOS SCRIPTS GENERADOS POR EL FRAMEWORK
$display->DrawScripts(addLoadEvent:false);

?>
@@RenderBundle(adminJS)

