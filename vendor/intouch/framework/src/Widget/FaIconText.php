<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'FaIconText')]
class FaIconText extends GenericWidget {

    public function __construct(
        public string $Name,
        public string $Text,
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public array  $TextClasses = [],
        public array  $TextStyles = [],
        public array  $TextAttributes = [],
        public array  $TextProperties = [],
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('fa');
        $this->AddClass($Name);

        $this->AddClasses($TextClasses, 'text');

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyle('margin-right', '8px');
        $this->AddStyles($Styles, 'text');

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttributes($TextAttributes, 'text');

        // Properties
        $this->AddProperties($Properties);
        $this->AddProperties($TextProperties, 'text');

        parent::__construct(Replace: [
            'CLASSES'           => $this->DrawClasses(),
            'STYLES'            => $this->DrawStyles(),
            'ATTRIBUTES'        => $this->DrawAttributes(),
            'PROPERTIES'        => $this->DrawProperties(),
            'TEXTCLASSES'       => $this->DrawClasses('text'),
            'TEXTSTYLES'        => $this->DrawStyles('text'),
            'TEXTATTRIBUTES'    => $this->DrawAttributes('text'),
            'TEXTPROPERTIES'    => $this->DrawProperties('text'),
            'TEXT'              => $Text
        ]);
    }
}