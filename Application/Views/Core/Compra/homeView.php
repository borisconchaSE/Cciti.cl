<?php

use Application\BLL\BusinessEnumerations\RolesEnum;
use Application\BLL\DataTransferObjects\Core\ordenCompraDto;
use Intouch\Framework\BLL\Filters\DataTableSettingsFilterDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\Button;
use Intouch\Framework\View\DisplayDefinitions\JSTable\JSTableCell;
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
                <h4 class="mb-sm-0 font-size-18">Compras e Insumos</h4>

                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="/"><?= $AppName ?></a></li>
                        <li class="breadcrumb-item active">Administración de Compras</li>
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
        Key             :   'btnNuevaCompra',
        Child           :   new FaIconText('fa-plus-circle','Agregar'),
        Classes         :   ['pull-right'],
        ButtonStyle     :   ButtonStyleEnum::BUTTON_SOFT_INFO,
        Events          :   [
            new ButtonOnClickEvent()
        ]
    )
) ;


$CantidadCompras    =   !empty($data['ListaCompras']) ? $data['ListaCompras']->count() : 0;

$tableheader =  new Container(
    Classes     :   ['row align-items-center'],
    Children    :   [
        new Container(
            Classes     :['col-md-6'],
            Children    :   [
                new Container(
                    Classes     :   ['mb-3'],
                    Children    :   [
                        new Html('<h5 class="card-title">Cantidad Total de Compras<span class="text-muted fw-normal ms-2">('.$CantidadCompras.')</span></h5>')
                    ]
                )
            ]
                    ),
        new Container(
            Classes:['col-md-6'],
            Children:[
                $display->Widgets()['btnNuevaCompra'],
            ]
        ), 
          
    ]
); 
 





## COMENZAMOS A DIBUJAR LA TABLA

$cellDefinitions    =   [
    new TableCell(
        PropertyName: 'Fecha_compra',
        Colspan: 2,
        Label: 'Fecha Compra'
    ),
    new TableCell(
        PropertyName: 'Descripcion',
        Colspan: 2,
        Label: 'Nombre Producto'
    ),
    new TableCell(
        PropertyName: 'marca',
        Colspan: 2,
        Label: 'Marca'
    ),
    new TableCell(
        PropertyName: 'modelo',
        Colspan: 2,
        Label: 'Modelo'
    ),
    new TableCell(
        PropertyName: 'Orden_compra',
        Colspan: 2,
        Label: 'Orden Compra'
    ),
    new TableCell(
        PropertyName: 'Factura_compra',
        Colspan: 2,
        Label: 'Factura Compra'
    ),
    new TableCell(
        PropertyName: 'Precio_U',
        Colspan: 2,
        Label: 'Precio Unitario'
    ),
    new TableCell(
        PropertyName: 'Cantidad',
        Colspan: 2,
        Label: 'Cantidad'
    ),
    new TableCell(
        PropertyName: 'Precio_total',
        Colspan: 2,
        Label: 'Precio Total'
    ),
    new TableCell(
        PropertyName: 'tipo',
        Colspan: 2,
        Label: 'Tipo',
        FormatFunction  :   function( ordenCompraDto $data,$cell){
            $stop = 1;
            if ($data->tipo == 'Alternativo'){
                return new Html('<center> <span class="badge badge-soft-warning">Alternativo</span> </center>');
            }else{
                return new Html('<center> <span class="badge badge-soft-success">Original</span> </center>');
            }
        }
    ),
    new TableCell(
        PropertyName: 'idProveedor',
        Colspan: 2,
        Label: 'Proveedor',
        FormatFunction: function($data,$cell) {
            
            $Widget = new Text($data->proveedor->Nombre);

            return new Container(
                Classes:['center'],
                Children:[
                    $Widget
                ]
            );
        }
    ),
    new TableCell(
        PropertyName: 'idEstado_oc',
        Colspan: 2,
        Label: 'Estado OC',
        FormatFunction: function($data,$cell) {
            
            $Widget = new Text($data->estadoOC->Descripcion);

            return new Container(
                Classes:['center'],
                Children:[
                    $Widget
                ]
            );
        }

    ),
    new TableCell(
        PropertyName: 'idEstado_FC',
        Colspan: 2,
        Label: 'Estado FC',
        FormatFunction: function($data,$cell) {
            
            $Widget = new Text($data->estadoFC->Descripcion);

            return new Container(
                Classes:['center'],
                Children:[
                    $Widget
                ]
            );
        }
    ),
    new TableCell(
        PropertyName: 'IdEmpresa',
        Colspan: 2,
        Label: 'Empresa',
        FormatFunction: function($data,$cell) {
            
            $Widget = new Text($data->empresa->Descripcion);

            return new Container(
                Classes:['center'],
                Children:[
                    $Widget
                ]
            );
        }
    ),
] ;


## ------------------------------------------------------------------------------
## VALIDAMOS LOS BOTONES A LOS QUE EL USUARIO TIENE ACCESO
## ------------------------------------------------------------------------------
$tableButtons    =   [];


## VALIDAMOS SI EL USUARIO TIENE PERMISOS PARA EDITAR EL STOCK
 
array_push($tableButtons,new TableButton(
    Key             :   'btnEditarCompra',
    Child           :   new FaIcon('fa-edit'),
    Classes         :   ['btn-sm'],
    OnClickClass    :   'btnEditarCompra',
    TogglePopUp     :   true,
    ToggleText      :   'Editar',
    ButtonStyle     :   ButtonStyleEnum::BUTTON_SUCCESS,
    Events          :   [ new TableButtonOnClickEvent() ],
  
)); 
  
 
$display->AddTableFromCollection(
    tableKey: 'tbListadoCompras',
    RowIdFieldName: 'idO_C',
    RowAttributeNames: ['idO_C'],
    CellDefinitions: $cellDefinitions,
    Data: $data['ListaCompras'],
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
                        $display->Widgets()['tbListadoCompras']
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
@@RenderBundle(compraJS)