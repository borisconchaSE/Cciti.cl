<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'ActionMenuMiniSeparator')]
class ActionMenuMiniSeparator extends GenericWidget {

    public function __construct(
        public array $Classes = [],
        public array $Styles = []
    )
    {
        // Classes
        //$this->AddClass('divider');
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyle('display', 'block');        
        $this->AddStyles($Styles);

        parent::__construct(Replace: [
            'CLASSES'   => $this->DrawClasses(),
            'STYLES'    => $this->DrawStyles()
        ]);
    }
}