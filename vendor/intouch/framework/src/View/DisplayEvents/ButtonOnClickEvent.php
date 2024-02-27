<?php

namespace Intouch\Framework\View\DisplayEvents;

class ButtonOnClickEvent extends Event {

    public function __construct(
        array $Actions = []
    ) {

        parent::__construct(
            Actions: $Actions,
            jsEvent: JsEventEnum::CLICK
        );

    }

    function GetStandardCall(?object $element = null) {

        return "
        if ( typeof " . $element->Key . "_OnClick === 'function') {
            " . $element->Key . "_OnClick({Element: this});
        }
        ";
        
    }

}