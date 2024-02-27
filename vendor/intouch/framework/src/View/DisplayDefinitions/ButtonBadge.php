<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;

class ButtonBadge {

    public function __construct(
        public string   $Badge = '',
        public string   $BadgeStyle = LabelStyleEnum::LABEL_DANGER,
        public string   $ButtonStyle = ButtonStyleEnum::BUTTON_DEFAULT,
    )
    {
    }
    
}