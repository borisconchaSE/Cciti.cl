<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'ActionMenuTitle')]
class ActionMenuTitle extends GenericWidget {

    public function __construct(
        public string $Content = '',
        public array $Classes = [],
        public array $Styles = []
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('menu-separator-title');

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyle('display', 'block');        

        parent::__construct(Replace: [
            'CONTENT'   => $Content,
            'CLASSES'   => $this->DrawClasses(),
            'STYLES'    => $this->DrawStyles()
        ]);
    }
}