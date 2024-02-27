<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'InputSelectOption')]
class InputSelectOption extends Input {    

    public function __construct(
        public string $Value = '',
        public string $Description = '',
        public bool   $Selected = false,
        public array  $Attributes = []
    )
    {
        $this->AddAttributes($this->Attributes);
        $this->AddAttribute('value', $this->Value);

        parent::__construct(Replace: [
            'DESCRIPTION'   => $this->Description,
            'SELECTED'      => ($Selected) ? ' selected ' : '',
            'ATTRIBUTES'    => $this->DrawAttributes()
        ]);
    }
}