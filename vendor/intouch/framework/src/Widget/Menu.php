<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\ActionMenu\AnimationTypeEnum;

#[Widget(Template: 'Menu')]
class Menu extends GenericWidget {

    public function __construct(
        public array $ContainerClasses = [],
        public array $MenuClasses = [],
        public ?array $Items = []
    )
    {
        foreach($ContainerClasses as $class) {
            $this->AddClass($class, 'container');
        }

        foreach($MenuClasses as $class) {
            $this->AddClass($class, 'menu');
        }

        $this->AddStyle('cursor', 'pointer');

        $menuItems = '';

        // Agregamos los "menús" del menú
        if (isset($Items)) {
            foreach($Items as $menu) {
                if ($menu instanceof ActionMenu || $menu instanceof MenuItem || $menu instanceof ActionMenuTitleSubtitle) {
                    if ($menuItems != '') {
                        $menuItems .= "\n";
                    }
                    $menuItems .= $menu->Draw(false);
                }
            }
        }

        parent::__construct(Replace: [
            'MENUITEMS'         => $menuItems,
            'CONTAINERCLASSES'  => $this->DrawClasses('container'),
            'MENUCLASSES'       => $this->DrawClasses('menu'),
            'STYLES'            => $this->DrawStyles()
        ]);
    }
}