<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'InputColor')]
class InputColor extends Input {    

    public function __construct(
        public ?string  $Key = null,
        public ?string  $Type = null,
        public ?string  $Value = null,
        public ?string  $Placeholder = null,
        public bool     $Required = false,
        public bool     $Disabled = false,
        public array    $Classes = [],
        public array    $Styles = [],
        public array    $Attributes = [],
        public array    $Properties = []
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('form-control');

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($this->Attributes);
        $this->AddAttributes([
            ['id', $this->Key],
            ['type', $this->Type],
            ['value', $this->Value],
            ['name', $this->Key],
         
        ]);

        // Properties
        $this->AddProperties($this->Properties);
        $this->AddProperty('required', $this->Required);
        $this->AddProperty('disabled', $this->Disabled);

        parent::__construct(Replace: [
            'ATTRIBUTES'            => $this->DrawAttributes(),
            'PROPERTIES'            => $this->DrawProperties(),
            'CLASSES'               => $this->DrawClasses(),
            'STYLES'                => $this->DrawStyles(),
            'SUBTITLE_PLACEHOLDRE'  => $Placeholder
        ]);
    }
}