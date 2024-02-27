<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'FaIcon')]
class FaIcon extends GenericWidget {

    public function __construct(
        public string $Name,
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = []
    )
    {
        // Classes
        $this->AddClasses($this->Classes);
        $this->AddClass('fa');
        $this->AddClass($this->Name);

        // Styles
        $this->AddStyles($this->Styles);

        // Attributes
        $this->AddAttributes($this->Attributes);

        // Properties
        $this->AddProperties($this->Properties);

        parent::__construct(Replace: [
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}