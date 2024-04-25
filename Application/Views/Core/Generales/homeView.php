<?php

use Intouch\Framework\BLL\Filters\CustomExcelSettingsFilterDto;
use Intouch\Framework\BLL\Filters\DataTableSettingsFilterDto;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\Button;
use Intouch\Framework\View\DisplayDefinitions\JSTable\JSTableCell;
use Intouch\Framework\View\DisplayDefinitions\JSTableButton;
use Intouch\Framework\View\DisplayDefinitions\TableButton;
use Intouch\Framework\View\DisplayDefinitions\TableCell;
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
                <h4 class="mb-sm-0 font-size-18">Compras de Activos</h4>
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
        Child           :   new FaIconText('fa-plus-circle','Agregar Activo'),
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
                        new Html('<h5 class="card-title">Cantidad Total de Activos<span class="text-muted fw-normal ms-2">('.$CantidadCompras.')</span></h5>')
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
    new JSTableCell(
        PropertyName: 'Fecha_Compra',
        Label: 'Fecha Compra',
        Colspan: 2,
        BodyClasses :   ["center"]   
    ),
    new JSTableCell(
        PropertyName: 'Empresa',
        Colspan: 2,
        Label: 'Empresa',
        // PropertyList :[
        //     "empresa"
        // ],
        BodyClasses :   ["center"],
        // WidgetFunction: function($x) {
        //     return new JSTableContent(
        //         PropertyName: 'IdEmpresa',
        //         JSFilterName: 'NombreEmpresa'
        //     );
        // },
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'NombreEmpresa',
        //         Script: [
        //             '
        //             var Empresa = element.empresa.Descripcion;

        //             return `<center> <span class="Center">${Empresa}</span> </center>`;
        //             '
        //         ]
        //     )
        // ],
    ),
    new JSTableCell(
        PropertyName: 'Rut_Proveedor',
        Colspan: 2,
        Label: 'Rut Proveedor',
        // PropertyList :[
        //     "proveedor"
        // ],
        BodyClasses :   ["center"],
        // WidgetFunction: function($x) {
        //     return new JSTableContent(
        //         PropertyName: 'Proveedor',
        //         JSFilterName: 'RutProveedor'
        //     );
        // },
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'RutProveedor',
        //         Script: [
        //             '
        //             var Rut = element.proveedor.Rut;

        //             return `<center> <span class="Center">${Rut}</span> </center>`;
        //             '
        //         ]
        //     )
        // ],
    ),
    new JSTableCell(
        PropertyName: 'Proveedor',
        Colspan: 2,
        Label: 'Proveedor',
        // PropertyList :[
        //     "proveedor"
        // ],
        BodyClasses :   ["center"],
        // WidgetFunction: function($x) {
        //     return new JSTableContent(
        //         PropertyName: 'idProveedor',
        //         JSFilterName: 'NombreProveedor'
        //     );
        // },
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'NombreProveedor',
        //         Script: [
        //             '
        //             var NombreP = element.proveedor.Nombre;

        //             return `<center> <span class="Center">${NombreP}</span> </center>`;
        //             '
        //         ]
        //     )
        // ],
    ),
    new JSTableCell(
        PropertyName: 'Nombre_Producto',
        Label: 'Descripcion compra',
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
        Colspan: 2,
        Label: 'Tipo',
        // PropertyList :[
        //     "tipoproducto"
        // ],
        BodyClasses :   ["center"],
        // WidgetFunction: function($x) {
        //     return new JSTableContent(
        //         PropertyName: 'tipo',
        //         JSFilterName: 'TipoProduct'
        //     );
        // },
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'TipoProduct',
        //         Script: [
        //             '
        //             var TipoProducto = element.tipoproducto.DescripcionProducto;

        //             return `<center> <span class="Center">${TipoProducto}</span> </center>`;
        //             '
        //         ]
        //     )
        // ],
    ),
    new JSTableCell(
        PropertyName: 'Estado_Activo',
        Colspan: 2,
        Label: 'Estado Activo',
        // PropertyList :[
        //     "estadosactivos"
        // ],
        BodyClasses :   ["center"],
        // WidgetFunction: function($x) {
        //     return new JSTableContent(
        //         PropertyName: 'IdEstadoActivo',
        //         JSFilterName: 'EstadoActivo'
        //     );
        // },
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'EstadoActivo',
        //         Script: [
        //             '
        //             var Activo = element.estadosactivos.DescripcionActivo;

        //             return `<center> <span class="Center">${Activo}</span> </center>`;
        //             '
        //         ]
        //     )
        // ],
    ),
    new JSTableCell(
        PropertyName: 'Estado_OC',
        Colspan: 2,
        Label: 'Estado OC',
        // PropertyList :[
        //     "estadoOC"
        // ],
        BodyClasses :   ["center"],
        // WidgetFunction: function($x) {
        //     return new JSTableContent(
        //         PropertyName: 'idEstado_oc',
        //         JSFilterName: 'EstadoOC'
        //     );
        // },
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'EstadoOC',
        //         Script: [
        //             '
        //             var OC = element.estadoOC.Descripcion;

        //             return `<center> <span class="Center">${OC}</span> </center>`;
        //             '
        //         ]
        //     )
        // ],
    ),
    new JSTableCell(
        PropertyName: 'Estado_FC',
        Colspan: 2,
        Label: 'Estado FC',
        // PropertyList :[
        //     "estadoFC"
        // ],
        BodyClasses :   ["center"],
        // WidgetFunction: function($x) {
        //     return new JSTableContent(
        //         PropertyName: 'idEstado_FC',
        //         JSFilterName: 'EstadoFC'
        //     );
        // },
        // JSDataFilter: [
        //     new JSTableScriptFilter(
        //         FunctionName:'EstadoFC',
        //         Script: [
        //             '
        //             var FC = element.estadoFC.Descripcion;

        //             return `<center> <span class="Center">${FC}</span> </center>`;
        //             '
        //         ]
        //     )
        // ],
    ),
] ;
 
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
        FileName        :   "Lista Activos",
        Estado          :   null,
        TableKey        :   'tbListadoActivos'
    ),
    CustomDataTable: new DataTableSettingsFilterDto(
        HideAllButtons  : false,
        CustomPdf       : false,
        TableHasButtons : true,
        GroupedButtons  : false,
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
@@RenderBundle(generalesJS)
