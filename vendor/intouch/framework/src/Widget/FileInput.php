<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'FileInput')]
class FileInput extends Input {    

    public function __construct(
        public ?string  $Key = null,
        public bool     $Disabled = false,
        public bool     $Multiple = true,
        public array    $Classes = [],
        public array    $Styles = [],
        public array    $Attributes = [],
        public array    $Properties = []
    )
    {
        // Classes
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttributes([
            ['id', $Key],
            ['type', 'file'],
            ['name', $Key . (($Multiple) ? '[]' : '')]
        ]);

        // Properties
        $this->AddProperties($this->Properties);
        $this->AddProperty('disabled', $Disabled);

        if ($Multiple)
            $this->AddProperty('multiple', true);

        parent::__construct(Replace: [
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties(),
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles()
        ]);
    }
}