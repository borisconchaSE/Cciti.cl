<?php

use Intouch\Framework\View\Display;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelectDefinition;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldText;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\View\DisplayEvents\FormButtonOnClickEvent;
use Intouch\Framework\View\DisplayEvents\FormOnChangeEvent;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\PageHeader;
use Intouch\Framework\Widget\Panel;
use Intouch\Framework\Widget\Text;

?>

@@Layout(authenticatedperiodo)

<?php

(new PageHeader(
    Title: 'Nuevo Destino',
    Description: 'Agregue un nuevo destino y defina el enlace asociado',
    IconName: 'fa-file', 
))->Draw();

$display = new Display();

// Crear el formulario
$display->AddFormFromObject(
    formKey: 'frmNuevoDestino',
    object: $data->Destino,
    fillData: false,
    keyFieldName: 'IdDestino',
    rowGroups: [
        new FormRowGroup(
            Key: 'grp-informacion-destino',
            Rows: [
                [
                    new FormRowFieldSelect(
                        PropertyName: 'IdTipoDestino',
                        Label: 'Clasificación',
                        Colspan: 3,
                        SelectDefinition: new FormRowFieldSelectDefinition(
                            Values: $data->TiposDestino,
                            Key: 'IdTipoDestino',
                            Description: 'Descripcion',
                            TriggerChangeOnload: true,
                        ),
                        Events: [
                            new FormOnChangeEvent()
                        ]
                    ),
                    new FormRowFieldText(
                        PropertyName: 'Descripcion',
                        Label: 'Nombre Destino',
                        Colspan: 3
                    ),
                    new FormRowFieldText(
                        PropertyName: 'Enlace',
                        Label: 'Enlace',
                        Colspan: 6
                    ),
                ],
            ]
        ),
    ]
);

$display->AddButton(
    new FormButton(
        Key: 'BtnGuardar',
        FormKey: 'frmNuevoDestino',
        ButtonStyle: ButtonStyleEnum::BUTTON_SUCCESS,
        Classes: ['pull-right'],
        Child: new Text('Guardar'),
        Events: [ 
            new FormButtonOnClickEvent() 
        ]
    )
);


(new Container(
    Classes: ['view-content'],
    Children: [
        new Panel(
            Body: new Container(
                Children: [$display->Widgets()['frmNuevoDestino']]
            ),
            Footer: new Container(
                Classes: ['text-right'],
                Styles: [
                    ['margin-bottom', '4px']
                ],
                Children: [
                    $display->Widgets()['BtnGuardar']
                ]
            )
        ),
        new Container(
            Key:'conpreview-new-team',
            Children:[]
        )
    ]
))->Draw();
?>

@@IncludeScriptBundle(NuevoDestinoJS)

<?php

$display->DrawScripts();