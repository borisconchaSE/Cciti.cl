<?php

namespace Intouch\Framework\View\DisplayEvents;

use Intouch\Framework\View\DisplayDefinitions\FormRowField;

class FormOnChangeEvent extends FormEvent {

    public function __construct(
        public array $Actions = []
    ) {

        parent::__construct(
            Actions: $Actions,
            jsEvent: JsEventEnum::CHANGE
        );

    }

    public function GetStandardCall(?object $field = null, string $formKey) {
        // return "
        // if ( typeof " . $field->Id . "_OnChange === 'function' ) {
        //     " . $field->Id . "_OnChange();
        // }
        // ";
    }

}