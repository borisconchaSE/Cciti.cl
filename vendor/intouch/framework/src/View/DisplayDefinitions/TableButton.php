<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;
use Intouch\Framework\Widget\GenericWidget;

class TableButton extends Button {

    public function __construct(
        GenericWidget   $Child,
        string          $Key = '',
        public string   $OnClickClass = '',
        bool            $TogglePopUp = false,
        string          $ToggleText = '',
        array           $Classes = [],
        string          $ToggleHtml = '',
        string          $TogglePlacement = TogglePlacementEnum::TOP,
        string          $ButtonStyle = ButtonStyleEnum::BUTTON_PRIMARY,
        public string   $BadgeStyle = LabelStyleEnum::LABEL_DANGER,
        array           $Events = [],
                        $DisplayFunction = null,
                        $EnabledFunction = null,
                        $BadgeFunction   = null,
                        $TempalteFunction = null
    )
    {
        parent::__construct(
            Child:              $Child,
            Key:                $Key,
            Classes:            $Classes,
            ButtonStyle:        $ButtonStyle,
            BadgeStyle:         $BadgeStyle,
            TogglePopUp:        $TogglePopUp,
            ToggleText:         $ToggleText,
            ToggleHtml:         $ToggleHtml,
            TogglePlacement:    $TogglePlacement,
            Events:             $Events,
            DisplayFunction:    $DisplayFunction,
            EnabledFunction:    $EnabledFunction,
            BadgeFunction:      $BadgeFunction,
            TempalteFunction:   $TempalteFunction
        );
    }

}