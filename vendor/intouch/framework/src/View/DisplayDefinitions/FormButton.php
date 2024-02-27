<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\GenericWidget;

class FormButton extends Button {

    public function __construct(
        GenericWidget    $Child,
        string           $Key,
        public string    $FormKey,
        string           $ButtonStyle = ButtonStyleEnum::BUTTON_PRIMARY,
        array            $Events = [],
        array            $Classes = [],
        array            $Styles = [],
        array            $Attributes = [],
        array            $Properties = []
    )
    {
        parent::__construct(
            Child:          $Child,
            Key:            $Key,
            ButtonStyle:    $ButtonStyle,
            Events:         $Events,
            Classes:        $Classes,
            Styles:         $Styles,
            Attributes:     $Attributes,
            Properties:     $Properties
        );
    }    

    function GetGlobalScripts(): array {
        return [];
    }

}