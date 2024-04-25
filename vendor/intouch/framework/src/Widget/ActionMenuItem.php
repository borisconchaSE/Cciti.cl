<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'ActionMenuItem')]
class ActionMenuItem extends GenericWidget {

    public function __construct(
        public string   $Content,
        public string   $Action,
        public bool     $Active = false,
        public          $Icon = null,
        public array    $Classes = [],
        public array    $Styles = [],
        public array    $Attributes = []
    )
    {
        // Classes        
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyle('display', 'block');        
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);        

        if ($Active) {
            $this->AddClass('active');
        }

        $faicon = '';
        if (isset($Icon)) {
            //$Icon->AddClass('pull-left');
            $faicon = $Icon->Draw(false);
        }

        parent::__construct(Replace: [
            'CONTENT'       => $Content,
            'ACTION'        => $Action,
            'ICON'          => $faicon,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes()
        ]);
    }
}