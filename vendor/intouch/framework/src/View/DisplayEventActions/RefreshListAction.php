<?php

namespace Intouch\Framework\View\DisplayEventActions;

use Intouch\Framework\View\DisplayDefinitions\FormRowField;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldTypeEnum;

class RefreshListAction extends Action {

    public function __construct(
        public array $TargetElementKeys = []
    ) {
    }

    public function GetScriptContent(?FormRowField $field, ?object $object, array $fields = [], array $formGroups = [], array $rows = [], string $formKey) : string {

        // Buscar los elementos de destino
        //
        $actions = "
        // Obtener el ID del origen del evento
        var id = $(me).id;
        
        // Obtener el valor del OPTION seleccionado
        var identifier   = $(me).val();
            ";

        foreach($this->TargetElementKeys as $key) {

            if (isset($fields) && isset($fields[$key])) {

                // Obtener el campo dependiente
                $field = $fields[$key];

                // El campo debe ser de tipo SELECT
                if ($field instanceof FormRowFieldSelect) {

                    // Tiene funcion de despliegue de la descripción??
                    $descFunc = null;
                    if (isset($field->SelectDefinition->JSDescriptionFunction)) {
                        if ($field->SelectDefinition->JSDescriptionFunction == '') {
                            $descFunc = str_replace('-', '_', $field->Id) . '_GetDescription';
                        }
                        else {
                            $descFunc = $field->SelectDefinition->JSDescriptionFunction;
                        }
                    }

                    // Agregar el código de refresco de la framework
                    //
                    $refreshService = '';
                    $refreshAction  = '';
                    if (isset($field->SelectDefinition->JSRefreshService) && is_array($field->SelectDefinition->JSRefreshService) and count($field->SelectDefinition->JSRefreshService) >= 2) {
                        $refreshService = $field->SelectDefinition->JSRefreshService[0];
                        $refreshAction  = $field->SelectDefinition->JSRefreshService[1];
                    }

                    $actions .= "
        // Actualizar el SELECT con id=" . $field->Id . "
        //
        ActualizarListaDependiente({
            DependantList: " . $this->GetJquerySelector($field->Id) . ",
            Controller: '" . $field->SelectDefinition->RefreshController . "',
            RefreshService: '" . $refreshService . "',
            RefreshAction: '" . $refreshAction . "',
            Identifier: identifier,
            KeyField: '" . $field->SelectDefinition->Key . "',
            DescriptionField: '" . $field->SelectDefinition->Description . "',
            DescriptionFunction: '" . ((isset($descFunc) && $descFunc != '') ? $descFunc : '') . "'
        });
        ";
                }                
            }
        }

        return $actions;

    }
}