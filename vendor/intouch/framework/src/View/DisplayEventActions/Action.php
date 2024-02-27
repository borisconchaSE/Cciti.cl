<?php

namespace Intouch\Framework\View\DisplayEventActions;

use Intouch\Framework\View\DisplayDefinitions\FormRowField;

abstract class Action {

    /**
     * Obtiene el script de esta acción en particular
     */
    abstract function GetScriptContent(?FormRowField $field, ?object $object, array $fields = [], array $formGroups = [], array $rows = [], string $formKey) : string;

    function GetActionScript(?FormRowField $field, ?object $object, array $fields = [], array $formGroups = [], array $rows = [], string $formKey) : string {

        $script = $this->GetScriptContent($field, $object, $fields, $formGroups, $rows, $formKey);

        // Agregar código general de todas las acciones
        //
        // to-do...


        return $script;
    }

    protected function GetJquerySelector(string $id) {
        return "$('#" . $id . "')";
    }
}