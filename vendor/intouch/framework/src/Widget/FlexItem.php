<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Container\Edge;
use Intouch\Framework\Widget\Definitions\Container\Position;

#[Widget(Template: 'FlexGroup')]
class FlexItem extends GenericWidget {

    public function __construct(        
        public GenericWidget    $Child,
        public ?int             $Grow = null,
        public string           $Key = '',
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
    )
    {
        // Classes
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        if ($Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        if (isset($Grow)) {
            $this->AddStyle('flex-grow', $Grow);
        }

        // Properties
        $this->AddProperties($Properties);        

        parent::__construct(Replace: [
            'ITEMS'         => $Child->Draw(false),
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}