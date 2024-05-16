<?php

use Intouch\Framework\BLL\Filters\CustomExcelSettingsFilterDto;
use Intouch\Framework\BLL\Filters\DataTableSettingsFilterDto;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\Button;
use Intouch\Framework\View\DisplayDefinitions\JSTable\JSTableCell;
use Intouch\Framework\View\DisplayDefinitions\JSTableButton;
use Intouch\Framework\View\DisplayEvents\ButtonOnClickEvent;
use Intouch\Framework\View\DisplayEvents\TableButtonOnClickEvent;
use Intouch\Framework\Widget\Card;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\FaIcon;
use Intouch\Framework\Widget\FaIconText;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\JSTableContent;
use Intouch\Framework\Widget\JSTableScriptFilter;


$AppName        =   SystemConfig::Instance()->ApplicationName ?: "App";
$Usuario        =   Session::Instance()->usuario;
$display        =   new Display();

## ----------------------------------------------------------------------------------------------
## CONSTRUIMOS EL HEADER DE LA PAGINA DE COMPRA DE TONNERS
## ----------------------------------------------------------------------------------------------
?>

@@Layout(authenticated)
<!-- TITULO DE LA PAGINA -->
<div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Compras de Tonners</h4>
            </div>
        </div>
    </div>
<?php 


## ----------------------------------------------------------------------------------------------
## UNA VEZ DEFINIDO, PROCEDEMOS A GENERAR LA VISUALIZACIÓN DE LOS COMPONENTES DE LA TABLA
## ----------------------------------------------------------------------------------------------

//GENERAMOS BOTON PARA PODER AGREGAR UN NUEVO TONNER
$display->AddButton(
    new Button(
        Key             :   'btnNuevaCompra',
        Child           :   new FaIconText('fa-plus-circle','Agregar Tonner'),
        Classes         :   ['pull-right'],
        ButtonStyle     :   ButtonStyleEnum::BUTTON_SOFT_INFO,
        Events          :   [
            new ButtonOnClickEvent()
        ]
    )
) ;


//CONTAMOS LA CANTIDAD DE COMPRAS DE TONNER HECHAS
$CantidadCompras    =   !empty($data['ListaCompras']) ? $data['ListaCompras']->count() : 0;

//GENERAMOS EL HEADER DE LA TABLA DONDE MOSTRAMOS LA CANTIDAD TOTAL Y EL BOTON PARA AGREGAR COMPRA
$tableheader =  new Container(
    Classes     :   ['row align-items-center'],
    Children    :   [
        new Container(
            Classes     :['col-md-6'],
            Children    :   [
                new Container(
                    Classes     :   ['mb-3'],
                    Children    :   [
                        new Html('<h5 class="card-title">Total de Compras de este año<span class="text-muted fw-normal ms-2">('.$CantidadCompras.')</span></h5>')
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






## COMENZAMOS A DIBUJAR LA TABLA CON SUS COLUMNAS

$cellDefinitions    =   [
    new JSTableCell(
        PropertyName: 'Fecha_Compra',
        Label: 'Fecha Compra',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Nombre_Producto',
        Label: 'Nombre Producto',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Marca',
        Label: 'Marca',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Modelo',
        Label: 'Modelo',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Orden_Compra',
        Label: 'Orden Compra',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Factura_Compra',
        Label: 'Factura Compra',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Precio_Unitario',
        Label: 'Precio Unitario',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Cantidad',
        Label: 'Cantidad',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Precio_Total',
        Label: 'Precio Total',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Tipo',
        Label: 'Tipo',
        Colspan: 2,
        BodyClasses :   ["center"],
        WidgetFunction: function($x) {
            return new JSTableContent(
                PropertyName: 'Tipo',
                JSFilterName: 'EstadoTipo'
            );
        },
        JSDataFilter: [
            new JSTableScriptFilter(
                FunctionName:'EstadoTipo',
                Script: [
                    '
                    var newtipo = element.Tipo;

                    if ( newtipo == "Alternativo"){
                         return `<center> <span class="badge badge-soft-warning">Alternativo</span> </center>`;
                    }else{
                         return`<center> <span class="badge badge-soft-success">Original</span> </center>`;
                    }
                    '
                ]
            )
        ],    
    ),
    new JSTableCell(
        PropertyName: 'Proveedor',
        Colspan: 2,
        Label: 'Proveedor',
        BodyClasses :   ["center"],
    ),
    new JSTableCell(
        PropertyName: 'Estado_OC',
        Colspan: 2,
        Label: 'Estado OC',
        BodyClasses :   ["center"],
    ),
    new JSTableCell(
        PropertyName: 'Estado_FC',
        Colspan: 2,
        Label: 'Estado FC',
        BodyClasses :   ["center"],
    ),    new JSTableCell(
        PropertyName: 'Empresa',
        Colspan: 2,
        Label: 'Empresa',
        BodyClasses :   ["center"],
    ),
] ;

  
//AGREGAMOS LA INFORMACION QUE TENDRA LA TABLA
$display->AddTableFromCollection(
    tableKey: 'tbListadoCompras',
    RowIdFieldName: 'idO_C',
    RowAttributeNames: ['idO_C'],
    CellDefinitions: $cellDefinitions,
    Data: $data['ListaCompras'],
    Buttons: [
        new JSTableButton(
            Key             :   'btnEditarCompra',
            Child           :   new FaIcon('fa-edit'),
            Classes         :   ['btn-sm'],
            OnClickClass    :   'btnEditarCompra',
            TogglePopUp     :   true,
            ToggleText      :   'Editar',
            ButtonStyle     :   ButtonStyleEnum::BUTTON_SUCCESS,
            Events          :   [ new TableButtonOnClickEvent() ],)
    ],
    TablaSimple: false,
    JSRenderTheTable : true,
    customExcel : new CustomExcelSettingsFilterDto(
        ShowButton      :   true,
        Controller      :   "lista",
        FileName        :   "Lista Compra",
        Estado          :   null,
        TableKey        :   'tbListadoCompra'
    ),
    CustomDataTable: new DataTableSettingsFilterDto(
        HideAllButtons  : false,
        CustomPdf       : false,
        TableHasButtons : true,
        GroupedButtons  : false,
    )
);

// GENERAMOS LA ESTRUCTURA QUE TIENE LA TABLA
$content    =   new Container(
    Classes     :   ['row'],
    Children    :   [ 
        new Container(
            Classes     :   ['col-lg-12'],
            Children    :   [
                $tableheader,
                new Card(
                    Classes:['table-responsive'],
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
$display->DrawScripts(addLoadEvent:true);

?>
@@RenderBundle(compraJS)
