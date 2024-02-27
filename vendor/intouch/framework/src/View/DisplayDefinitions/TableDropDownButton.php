<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;
use Intouch\Framework\Widget\FaIcon;
use Intouch\Framework\Widget\FaIconText;
use Intouch\Framework\Widget\GenericWidget;

class TableDropDownButton {

    public function __construct(
        public array            $Children,
        public string           $Key = '',
        public string           $ButtonStyle = ButtonStyleEnum::BUTTON_PRIMARY,
        public string           $BadgeStyle = LabelStyleEnum::LABEL_DANGER,
        public bool             $TogglePopUp = false,
        public string           $ToggleText = '',
        public string           $ToggleHtml = '',
        public string           $TogglePlacement = TogglePlacementEnum::TOP,
        public array            $Events = [],
        public                  $Content = "",
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
        public                  $DisplayFunction = null,
        public                  $EnabledFunction = null,
        public                  $BadgeFunction   = null,
    )
    {
    }    

    function GetGlobalScripts(): array {
        return [];
    }

}