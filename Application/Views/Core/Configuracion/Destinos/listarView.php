<?php

use Application\BLL\DataTransferObjects\Administracion\VWEquiposDto;
use Application\BLL\DataTransferObjects\Etiquetado\VWDestinosDto;
use Application\Dao\Entities\Administracion\VWEquipos;
use Intouch\Framework\BLL\Filters\DataTableSettingsFilterDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\JSTable\JSTableCell;
use Intouch\Framework\View\DisplayDefinitions\JSTableButton;
use Intouch\Framework\View\DisplayDefinitions\TableButton;
use Intouch\Framework\View\DisplayDefinitions\TableCell;
use Intouch\Framework\View\DisplayEvents\TableButtonOnClickEvent;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelSizeEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;
use Intouch\Framework\Widget\FaIcon;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\Image;
use Intouch\Framework\Widget\JSTableContent;
use Intouch\Framework\Widget\JSTableScriptFilter;
use Intouch\Framework\Widget\Label;
use Intouch\Framework\Widget\Script;
use Intouch\Framework\Widget\ScriptDirect;

    $display = new Display();

if(!empty($data)){
    $data = array_map(
        function($element) {

            $enlace = $element->Enlace;            
            $altname2 = substr($enlace, 0, 60) . "...";  
            $uri = '<a  class="title-content-text-blue" style="color:  #3498db;" href="'.$enlace.'"   target="_blank" >'.$altname2.'</a>';
   
            $element->Enlace = $uri;

            return $element;
        }, $data->Values
    );

    $data = new GenericCollection(
        DtoName: VWDestinosDto::class,
        Key: 'IdDestinos',
        Values: $data
    ) ;
}


$tableButtons = [];
array_push($tableButtons,

    new JSTableButton(
        Key: 'BtnEditar',
        OnClickClass: 'btn-editar',
        TogglePopUp: true,
        ToggleText: 'Editar este Destino',
        Child: new FaIcon('fa-edit'),
        ButtonStyle: ButtonStyleEnum::BUTTON_WARNING,
        Events: [ new TableButtonOnClickEvent() ],
    ),
    new JSTableButton(
        Key: 'BtnVerQr',
        OnClickClass: 'btn-ver-qr',
        TogglePopUp: true,
        ToggleText: 'Ver código QR',
        Child: new FaIcon('fa-qrcode'),
        ButtonStyle: ButtonStyleEnum::BUTTON_INFO,
        Events: [ new TableButtonOnClickEvent() ],
    ),
    new JSTableButton(
        Key: 'BtnEliminarDestino',
        OnClickClass: 'btn-eliminar-destino',
        TogglePopUp: true,
        ToggleText: 'Eliminar Destino',
        Child: new FaIcon('fa-trash'),
        ButtonStyle: ButtonStyleEnum::BUTTON_DANGER,
        Events: [ new TableButtonOnClickEvent() ],
    ),

);

$cellDefinitions = [
    new JSTableCell(
        PropertyName    : 'IdDestino',
        Label           : ' ',
        JSDataFilter    : [
            new JSTableScriptFilter(
                FunctionName    : 'checkbox_input',
                Script          : [
                    '
                        return `<input onchange="_checkTeamList(this)" class="form-check-input team-checkbox" type="checkbox" value="${element.IdDestino}" data-code="${element.IdDestino}" id="checkbox_${element.IdDestino}">`;

                    '
                ]
            ),
        ],
        WidgetFunction  : function() {
            return new Container(
                Classes:['text-center'],
                Children:[
                    new JSTableContent(
                        PropertyName: 'IdDestino',
                        JSFilterName: 'checkbox_input'
                    ) 
                ]
            ) ;
        }
    ) , 
    new JSTableCell(
       PropertyName: 'TipoDestino',
       Label: 'Tipo de Destino',
       BodyClasses: ['one-line-text'],
    ),
    new JSTableCell(
       PropertyName: 'Descripcion',
       Label: 'Nombre Destino',
       BodyClasses: ['one-line-text'],
    ),
    new JSTableCell(
       PropertyName: 'Enlace',
       Label: 'Enlace'
    ),
    new JSTableCell(
       PropertyName: 'Estado',
       PropertyList: [
        "IdTipoEstadoDestino",
        "RutaArchivo",
        "Enlace",
        "QrToken"
       ],
       Label: 'Estado',
       BodyClasses: ['text-center one-line-text'],
       JSDataFilter : [
        new JSTableScriptFilter(
            FunctionName:'EstadoFilter',
            Script: [
                '
                if (element.IdTipoEstadoEquipo == 2) {
                    return `<span class="label label-danger md" id="">${element.Estado}</span>`;
                }else{
                    return `<span class="label label-success md" id="">${element.Estado}</span>`;
                }
                '
            ]
        )
       ],
       WidgetFunction: function() {
            return new JSTableContent(
                PropertyName: 'estado',
                JSFilterName: 'EstadoFilter'
            );
        }, 
        
    ),
];

/**
 * Dibujando la tabla
 */
$display->AddTableFromCollection(
    tableKey: 'tbListaDestinos',
    RowIdFieldName: 'IdDestino',
    Data: $data,
    CellDefinitions: $cellDefinitions,
    RowAttributeNames: ['IdDestino'],
    Buttons: $tableButtons,
    JSRenderTheTable: true, 
    CustomDataTable : new DataTableSettingsFilterDto(
        HideAllButtons      : false,
        GroupedButtons      : false,
        CustomPdf           : true,
        HideDefaultButtons  : true,
        TituloPdf           : 'Lista Destinos',
        Modulo              : 'Lista Destinos',
        DrawTableCallback   : [
            '
            // ------------------------------------------------------------------------------------------------------------------
            // este callback se utilizara para dejar visiblemente marcado las opciones seleccionadas al interactuar con la tabla;
            // Cualquier Script JS que se escriba dentro de DrawTableCallback se ejecuta despues de dibujar los campos de la tabla;
            // ----
            // Al ejecutarse posteriormente al dibujado de la tabla se puede reaizar un llamado a dos parametros
            // settings -> Contiene la configuración del datatable
            // data     -> Contiene los datos utilizados para renderizar la tabla
            // adicionalemnte al utilizar el metodo `this` se tiene acceso al DataTable
            // ------------------------------------------------------------------------------------------------------------------
            if(window.selectedTeams != null){

                var _array      = window.selectedTeams;
                var iterations  = _array.length;
                let parent      = $("#tbListaDestinos");
           
                if(iterations > 0){                    
                    for(var i = 0 ; i < iterations; i ++ ){

                        let val = _array[i]; 

                        let target = parent.find(`.team-checkbox[data-code="${val}"]`);

                        if(target){
                            $(target).prop( "checked", true );
                        } 
                    }
                }
            }
            '
        ],
        JSCustomButton      : [
            "
            // ------------------------------------------------------------------------------------------------------------------
            // La Funcion JSCustomButton permite inyectar botones utilizando la configuración del datatable
            // ------------------------------------------------------------------------------------------------------------------

            {
                text: 'Seleccionar Visibles',
                className: 'btn-secondary btn-sm',
                action: function ( e, dt, node, config ) {

                    let parent      = $('#tbListaDestinos');
                    let Inputs   = parent.find('.team-checkbox');                

                    if(Inputs != null && Inputs.length > 0){

                        if(window.selectedTeams == null){
                            window.selectedTeams = new Array();
                        }

                        let iterations = Inputs.length;

                        for(var i = 0; i < iterations; i++){
                            let value = Inputs[i].value;

                            Inputs[i].checked = true;

                            if( window.selectedTeams.includes(value) == false ){
                                window.selectedTeams.push(value);
                            }   
                        } 
                    }



                }
            },
            {
                text: 'Deseleccionar Visibles',
                className: 'btn-secondary btn-sm',
                action: function ( e, dt, node, config ) {

                    let parent      = $('#tbListaDestinos');
                    let Inputs   = parent.find('.team-checkbox');                

                    if(Inputs != null && Inputs.length > 0){

                        if(window.selectedTeams == null){
                            window.selectedTeams = new Array();
                        }

                        let iterations = Inputs.length;

                        for(var i = 0; i < iterations; i++){
                            let value = Inputs[i].value;

                            Inputs[i].checked = false;

                            if( window.selectedTeams.includes(value) != false ){

                                let position = window.selectedTeams.indexOf(value);
                                window.selectedTeams.splice(position, 1);
                            }   
                        } 
                    }



                }
            },
            {
                text: '|',
                className: 'btn-sm',
                action: function ( e, dt, node, config ) { return;}
            },
            {
                text: `<i class='fa fa-file-pdf-o' aria-hidden='true'></i> Exportar QR`,
                className: 'btn-danger btn-sm',
                action: function ( e, dt, node, config ) {

                    if(window.selectedTeams == null){
                        window.selectedTeams = new Array();
                    } 

                    PopUpOpcionesPDF() 
                
                }

            }
            "
        ]
    )
);



// Dibujar el Panel
//

(new Container(
    Children: [
        $display->Widgets()['tbListaDestinos'],
        new Container(
            Classes     :['hide'],
            Key         : 'qrcontainer',
            Children    :[ 
                
            ]
        )    
    ]
))->Draw();


$display->DrawScripts(
    addLoadEvent: false
); 

 