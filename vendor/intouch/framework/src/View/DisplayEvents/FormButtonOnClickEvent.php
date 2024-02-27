<?php

namespace Intouch\Framework\View\DisplayEvents;

use Intouch\Framework\View\DisplayDefinitions\FormRowField;

class FormButtonOnClickEvent extends FormEvent {

    public function __construct(
        array $Actions = []
    ) {

        parent::__construct(
            Actions: $Actions,
            jsEvent: JsEventEnum::CLICK
        );

    }

    function GetStandardCall(?object $field = null, string $formKey) {

        return null;
    }

}