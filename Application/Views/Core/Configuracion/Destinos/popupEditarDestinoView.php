<?php

use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldHidden;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelectDefinition;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldText;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\View\DisplayEvents\FormButtonOnClickEvent;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Definitions\PopUpModal\ColorLineStyleEnum;
use Intouch\Framework\Widget\PopUpContent;
use Intouch\Framework\Widget\Text;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Search;

$display = new Display();


$rowGroups = [

    new FormRowGroup(
        Key: 'optionOne',
        Rows: [
            [
                new FormRowFieldHidden(
                    PropertyName:'IdDestino'
                ),
                new FormRowFieldSelect(
                    Label               : 'Tipo de Equipo',
                    PropertyName        : 'IdTipoDestino',
                    Colspan             : 6,
                    Required            : true,
                    SelectDefinition    : new FormRowFieldSelectDefinition(
                        Key             : 'IdTipoDestino',
                        Description     : 'Descripcion',
                        SelectedValue   : $data->Destino->IdTipoDestino,
                        Values          : $data->TiposDestino,
                        DisplaySearch   : true
                    ) 
                ),
                new FormRowFieldText(
                    PropertyName        : 'Descripcion' ,
                    Label               : 'Nombre Destino',
                    Colspan             : 6,
                ), 
            ] ,
            [
                new FormRowFieldText(
                    PropertyName        : 'Enlace',
                    Label               : 'Enlace',
                    Colspan             : 6,
                ), 
                new FormRowFieldSelect(
                    Label               : 'Estado',
                    PropertyName        : 'IdTipoEstadoDestino',
                    Colspan             : 6,
                    Required            : true,
                    SelectDefinition    : new FormRowFieldSelectDefinition(
                        Key             : 'IdTipoEstadoDestino',
                        Description     : 'Descripcion',
                        SelectedValue   : $data->EquipoDestinosDto->IdTipoEstadoDestino,
                        Values          : $data->EstadosDestino,
                    
                    ) 
                ),
            ]
        ]
    )

] ;

$display->AddFormFromObject(
    formKey     : 'frmEditarDestino',
    object      : $data->Destino,
    rowGroups   : $rowGroups,
    keyFieldName: 'IdDestino',
    fillData    : true
) ;

$display->AddButton(
    new FormButton(
        Key         :'btnGuardarCambiosEditDestino',
        FormKey     : 'frmEditarDestino',
        ButtonStyle : ButtonStyleEnum::BUTTON_WARNING,
        Child       : new Text('Guardar Cambios'),
        Events      : [
            new FormButtonOnClickEvent()
        ]
    )
) ;


$popup = new PopUpContent(
    Key: 'popupEditarDestino',
    ColorLineStyle: ColorLineStyleEnum::LINE_WARNING,
    Title: 'Editar Destino',
    DismissButtonText: 'Cancelar',
    SubTitle: 'Editando el destino seleccionado',
    Content: new Container(
        Children: [
            $display->Widgets()['frmEditarDestino']
        ]
    ),
    Buttons:[
        $display->Widgets()['btnGuardarCambiosEditDestino']
    ]
    
);

$popup->Draw();

$display->DrawScripts(addLoadEvent:false);