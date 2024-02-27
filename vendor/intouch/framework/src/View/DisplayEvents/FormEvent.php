<?php

namespace Intouch\Framework\View\DisplayEvents;

use Intouch\Framework\View\DisplayDefinitions\FormRowField;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;

abstract class FormEvent {

    /** Define la llamada a la funcion principal del evento, definido por el usuario en su script de negocio
     * 
     * Ej:  
     *        
     * if ( typeof " . $field->Id . "_OnClick === 'function' ) {
     *      " . $field->Id . "_OnClick();
     *   }
     */
    abstract function GetStandardCall(?object $field = null, string $formKey);

    
    public function GetScript(?object $field = null, ?object $object = null, array $fields = [], array $formGroupRows = [], array $rows = [], string $formKey) {

        // Recorrer acciones
        //
        $scriptContent = "";
        foreach($this->Actions as $action) {

            if ($scriptContent != "") {
                $scriptContent .= "\n\n";
            }

            $scriptContent .= $action->GetActionScript($field, $object, $fields, $formGroupRows, $rows, $formKey);
        }

        return $this->BuildScript($field, $scriptContent, $formKey);        
    }

    public function __construct(
        public string $jsEvent,
        public array $Actions = []
    )
    {
    }

    private function BuildScript(?object $field = null, string $scriptContent, string $formKey) {

        if (isset($field) && isset($field->Id)) {
            $key = $field->Id;
        }
        else if (isset($field) && isset($field->Key)) {
            $key = $field->Key;
        }
        else {
            $key = 'unkownId_' . _guid();
        }

        // Agregar llamado estandar de funcion de evento
        //
        $callScript = $this->GetStandardCall($field, $formKey);

        if (!isset($callScript)) {
            $callScript = "
        // Llamar evento estandar
        if (typeof " . str_replace('-', '_', $key) . "_On" . $this->jsEvent . " === 'function') {
            " . str_replace('-', '_', $key) . "_On" . $this->jsEvent . " ({
                Element         : me, 
                FormKey         : '" . $formKey . "', 
                FormData        : myForm,
                FormElements    : myElements,
                Event           : event
            });
        }
            ";
        }

        // Aquí se debe construir el código javascript
        //
        $script = "
    $('#" . $key . "').on('" . strtolower($this->jsEvent) . "', function(event) {

        var me          = $(this)[0];
        var myForm      = null;
        var myElements  = null;

        if (typeof ReadForm === 'function') {
            myForm = ReadForm('" . $formKey . "');
        }

        if (typeof ReadFormElements === 'function') {
            myElements = ReadFormElements('" . $formKey . "');
        }

            " . $callScript . "
            " . $scriptContent . "
    });
";

        if ($field instanceof FormRowFieldSelect && isset($field->SelectDefinition) && $field->SelectDefinition->TriggerChangeOnload) {
            $script .= "
    $('#" . $key . "').trigger('change');
";
        }




        return $script;
    }

}