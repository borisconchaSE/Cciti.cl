<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'FormGroupSeparator')]
class FormGroupSeparator extends GenericWidget {

    public function __construct(
        public string   $Title,
        public array    $Classes = [],
        public array    $Styles = [],
        public array    $Attributes = [],
        public array    $Properties = []
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('modal-group-title');

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        // Properties
        $this->AddProperties($Properties);

        parent::__construct(Replace: [
            'TITLE'         => $this->Title,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}