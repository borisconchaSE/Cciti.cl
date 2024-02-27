<?php

namespace Intouch\Framework\View\DisplayEvents;

use Intouch\Framework\View\DisplayDefinitions\FormRowField;

class FormOnKeyPressEvent extends FormEvent {

    public function __construct(
        public array    $Actions = [],
        public string   $AllowedCharsRegex = ''
    ) {

        parent::__construct(
            Actions: $Actions,
            jsEvent: JsEventEnum::KEYPRESS
        );

    }

    public function GetStandardCall(?object $field = null, string $formKey) {

        $inputValidation = "";

        if ($this->AllowedCharsRegex != '') {
            $inputValidation = "
        // Validar input
        //
        var regex = new RegExp('" . $this->AllowedCharsRegex . "');
        var key = String.fromCharCode(
            event.charCode == event.which ? event.which : event.charCode
        );  

        // Si el input no es valido, desactivamos la tecla
        if (!regex.test(key)) {                
            event.preventDefault();
            return false;
        }
        ";
        }

        return $inputValidation . "
        if (typeof " . str_replace('-', '_', $field->Id) . "_On" . $this->jsEvent . " === 'function') {
            " . str_replace('-', '_', $field->Id) . "_On" . $this->jsEvent . " ({
                Element         : me, 
                FormKey         : '" . $formKey . "', 
                FormData        : myForm,
                FormElements    : myElements,
                Event           : event
            }, event.keyCode); 
        }
            ";
    }

}