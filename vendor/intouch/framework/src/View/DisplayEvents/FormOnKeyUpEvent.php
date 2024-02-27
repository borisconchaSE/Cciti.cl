<?php

namespace Intouch\Framework\View\DisplayEvents;

use Intouch\Framework\View\DisplayDefinitions\FormRowField;

class FormOnKeyUpEvent extends FormEvent {

    public function __construct(
        public array    $Actions = [],
        public string   $AllowedChars = ''
    ) {

        parent::__construct(
            Actions: $Actions,
            jsEvent: JsEventEnum::KEYUP
        );

    }

    public function GetStandardCall(?object $field = null, string $formKey) {

        return "
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