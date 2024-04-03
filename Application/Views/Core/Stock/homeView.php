<?php

use Application\BLL\DataTransferObjects\Core\stockDto;
use Intouch\Framework\BLL\Filters\CustomExcelSettingsFilterDto;
use Intouch\Framework\BLL\Filters\DataTableSettingsFilterDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\OfficeHelper\Excel;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\Button;
use Intouch\Framework\View\DisplayDefinitions\JSTable\JSTableCell;
use Intouch\Framework\Widget\JSTableContent;
use Intouch\Framework\Widget\JSTableScriptFilter;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldHidden;
use Intouch\Framework\View\DisplayDefinitions\TableButton;
use Intouch\Framework\View\DisplayDefinitions\TableCell;
use Intouch\Framework\View\DisplayEvents\ButtonOnClickEvent;
use Intouch\Framework\View\DisplayEvents\Event;
use Intouch\Framework\View\DisplayEvents\TableButtonOnClickEvent;
use Intouch\Framework\Widget\Card;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\FaIcon;
use Intouch\Framework\Widget\FaIconText;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\Text;

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
                <h4 class="mb-sm-0 font-size-18">Stock de toners</h4>
            </div>
        </div>
    </div>
<!-- end page title --> 
<?php 


## ----------------------------------------------------------------------------------------------
## UNA VEZ DEFINIDO, PROCEDEMOS A GENERAR LA VISUALIZACIÓN DE LOS COMPONENTES DE LA TABLA
## ----------------------------------------------------------------------------------------------

// $display->AddButton(
//     new Button(
//         Key             :   'btnAgregarStock',
//         Child           :   new FaIconText('fa-plus-circle','Agregar Stock'),
//         Classes         :   ['pull-right'],
//         ButtonStyle     :   ButtonStyleEnum::BUTTON_SOFT_INFO,
//         Events          :   [
//             new ButtonOnClickEvent()
//         ]
//     )
// ) ;



$CantidadStock    =   !empty($data['Stock']) ? $data['Stock']->count() : 0;

$tableheader =  new Container(
    Classes     :   ['row align-items-center'],
    Children    :   [
        new Container(
            Classes     :['col-md-6'],
            Children    :   [
                new Container(
                    Classes     :   ['mb-3'],
                    Children    :   [
                        new Html('<h5 class="card-title">Cantidad Actual de Stock <span class="text-muted fw-normal ms-2">('.$CantidadStock.')</span></h5>')
                    ]
                )
            ]
                    ), 
        new Container(
            Classes:['col-md-6'],
            Children:[
                $display->Widgets()['btnAgregarStock'],
            ]
        ), 
          
    ]
); 


## COMENZAMOS A DIBUJAR LA TABLA

$cellDefinitions    =   [
    new TableCell(
        PropertyName: 'Fecha',
        Colspan: 2,
        Label: 'Fecha Llegada'
    ),
    new TableCell(
        PropertyName: 'Descripcion',
        Colspan: 2,
        Label: 'Nombre Producto'
    ),
    new TableCell(
        PropertyName: 'Cantidad',
        Colspan: 2,
        Label: 'Cantidad'
    ),
    new TableCell(
        PropertyName: 'Precio_Unitario',
        Colspan: 2,
        Label: 'Precio Unitario'
    ),
    new TableCell(
        PropertyName: 'idMarca',
        Colspan: 2,
        Label: 'Marca',
        FormatFunction: function($data,$cell) {
            
            $Widget = new Text($data->marca->Descripcion);

            return new Container(
                Classes:['center'],
                Children:[
                    $Widget
                ]
            );
        }
    ),
    new TableCell(
        PropertyName: 'idModelo',
        Colspan: 2,
        Label: 'Modelo',
        FormatFunction: function($data,$cell) {
            
            $Widget = new Text($data->modelo->Descripcion);

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
        Label: 'Empresa producto',
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
    new TableCell(
        PropertyName: 'tipo',
        Colspan: 2,
        Label: 'Tipo toner',
        FormatFunction  :   function( stockDto $data,$cell){
            $stop = 1;
            if ($data->tipo == 'Alternativo'){
                return new Html('<center> <span class="badge badge-soft-warning">Alternativo</span> </center>');
            }else{
                return new Html('<center> <span class="badge badge-soft-success">Original</span> </center>');
            }
        }
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'EstadoTipo',
        //         Script: [
        //             '
        //             if ($data->tipo == "Alternativo"){
        //                  return new Html(`<center> <span class="badge badge-soft-warning">Alternativo</span> </center>`);
        //             }else{
        //                 return new Html(`<center> <span class="badge badge-soft-success">Original</span> </center>`);
        //             }
        //             '
        //         ]
        //     )
        //         ],
        //         WidgetFunction: function() {
        //             return new JSTableContent(
        //                 PropertyName: 'tipo',
        //                 JSFilterName: 'EstadoTipo'
        //             );
        //         },
    ),
    new TableCell(
        PropertyName: 'estado_stock',
        Colspan: 2,
        Label: 'Estado producto'
    ),
] ;


## ------------------------------------------------------------------------------
## VALIDAMOS LOS BOTONES A LOS QUE EL USUARIO TIENE ACCESO
## ------------------------------------------------------------------------------
$tableButtons    =   [];


## VALIDAMOS SI EL USUARIO TIENE PERMISOS PARA EDITAR EL STOCK
 
array_push($tableButtons,new TableButton(
    Key             :   'btnEditarStock',
    Child           :   new FaIcon('fa-edit'),
    Classes         :   ['btn-sm'],
    OnClickClass    :   'btnEditarStock',
    TogglePopUp     :   true,
    ToggleText      :   'Editar',
    ButtonStyle     :   ButtonStyleEnum::BUTTON_SUCCESS,
    Events          :   [ new TableButtonOnClickEvent() ],
  
)); 
  
 
$display->AddTableFromCollection(
    tableKey: 'tbListadoStock',
    RowIdFieldName: 'id_stock',
    RowAttributeNames: ['id_stock'],
    CellDefinitions: $cellDefinitions,
    Data: $data['Stock'],
    Buttons: $tableButtons,
    TablaSimple: false,
    // customExcel : new CustomExcelSettingsFilterDto(
    //     ShowButton      :   true,
    //     Controller      :   "lista",
    //     FileName        :   "Lista Stock Toner",
    //     Estado          :   null,
    //     TableKey        :   'tbListadoStock'
    // ),
    CustomDataTable: new DataTableSettingsFilterDto(
        HideAllButtons  : false,
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
                        $display->Widgets()['tbListadoStock']
                    ]
                )
            ]
        )
    ]
);

## DIBUJAMOS LA TABLA COMO TAL
$content->Draw();


// ## DIBUJAMOS LOS SCRIPTS GENERADOS POR EL FRAMEWORK
$display->DrawScripts(addLoadEvent:true);


?>

@@RenderBundle(stockJS)

