<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'Text')]
class Text extends GenericWidget {

    public function __construct(
        public ?string $Content,
        public string  $Key = '',
        public array   $Classes = [],
        public array   $Styles = [],
        public array   $Attributes = [],
        public array   $Properties = []         
    )
    {        
        // Classes
        $this->AddClasses($this->Classes);

        // Styles
        $this->AddStyles($this->Styles);

        // Attributes
        $this->AddAttributes($this->Attributes);

        // Properties
        $this->AddProperties($this->Properties);

        if ($this->Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        parent::__construct(Replace: [
            'CONTENT'       => isset($Content) ? $Content : '&nbsp;',
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}