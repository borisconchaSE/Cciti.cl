<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\ActionMenu\AnimationTypeEnum;

#[Widget(Template: 'ActionMenuTree')]
class ActionMenuTree extends GenericWidget {

    public function __construct(
        public array $Classes = [],
        public array $Styles = [],
        public bool $Animated = true,
        public string $AnimationType = AnimationTypeEnum::FLIP_IN_X,
        public array $Items = [],
        public array $Attributes = []
    )
    {
        // Classes
        $this->AddClasses($Classes);
  
        if ($Animated) {
            $this->AddClass('animated');
            $this->AddClass($AnimationType);
        }

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        $menuItems = '';

        foreach($Items as $item) {
            if ($item instanceof ActionMenuItem || $item instanceof ActionMenuSeparator || $item instanceof ActionMenuMiniSeparator || $item instanceof ActionMenuTitle) {
                if ($menuItems != '') {
                    $menuItems .= "\n";
                }

                $menuItems .= $item->Draw(false);
            }
        }

        parent::__construct(Replace: [
            'CLASSES'   => $this->DrawClasses(),
            'STYLES'    => $this->DrawStyles(),
            'MENUITEMS' => $menuItems,
            'ATTRIBUTES'=> $this->DrawAttributes()
        ]);
    }
}