<?php

namespace Intouch\Framework\View\DisplayEvents;

class FormOnClickEvent extends FormEvent {

    public function __construct(
        array $Actions = []
    ) {

        parent::__construct(
            Actions: $Actions,
            jsEvent: JsEventEnum::CLICK
        );

    }

    public function GetStandardCall(?object $field = null, string $formKey) {
        return "
        if ( typeof " . $field->Id . "_OnClick === 'function' ) {
            " . $field->Id . "_OnClick();
        }
        ";
    }

}