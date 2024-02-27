<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Key;
use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'InputCheck')]
class InputCheck extends Input {    

    public function __construct(
        public string $Key,        
        public string $Label,
        public bool   $Checked = false,
        public string $CheckStyle = 'primary',
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public array  $InputClasses = [],
        public array  $InputStyles = [],
        public array  $InputAttributes = [],
        public array  $InputProperties = [],
        public array  $LabelClasses = [],
        public array  $LabelStyles = [],
        public array  $LabelAttributes = [],
        public array  $LabelProperties = [],
    )
    {
        // Classes
        $this->AddClasses($this->Classes);
        $this->AddClass('checkbox-container');
        $this->AddClasses($this->InputClasses, 'input');
        $this->AddClass('checkbox-incluir', 'input');
        $this->AddClasses($this->LabelClasses, 'label');
        //$this->AddClass('checkbox');
        $this->AddClass('checkbox-' . $this->CheckStyle);

        // Styles
        $this->AddStyles($this->Styles);
        $this->AddStyles($this->InputStyles, 'input');
        $this->AddStyles($this->LabelStyles, 'label');
        
        // Attributes
        $this->AddAttributes($this->Attributes);
        $this->AddAttributes($this->InputAttributes, 'input');
        $this->AddAttributes($this->LabelAttributes, 'label');
        $this->AddAttribute('id', $this->Key, 'input');
        $this->AddAttribute('type', 'checkbox', 'input');

        if ($this->Checked)
            $this->AddProperty('checked', true, 'input');

        $this->AddAttribute('for', $this->Key, 'label');
        $this->AddAttribute('id', $this->Key . '-label', 'label');

        // Properties
        $this->AddProperties($this->Properties);
        $this->AddProperties($this->InputProperties, 'input');
        $this->AddProperties($this->LabelProperties, 'label');

        parent::__construct(Replace: [
            'TITLE'             => $this->Label,
            'CLASSES'           => $this->DrawClasses(),
            'STYLES'            => $this->DrawStyles(),
            'ATTRIBUTES'        => $this->DrawAttributes(),
            'PROPERTIES'        => $this->DrawProperties(),
            'INPUTCLASSES'      => $this->DrawClasses('input'),
            'INPUTSTYLES'       => $this->DrawStyles('input'),
            'INPUTATTRIBUTES'   => $this->DrawAttributes('input'),
            'INPUTPROPERTIES'   => $this->DrawProperties('input'),
            'LABELCLASSES'      => $this->DrawClasses('label'),
            'LABELSTYLES'       => $this->DrawStyles('label'),
            'LABELATTRIBUTES'   => $this->DrawAttributes('label'),
            'LABELPROPERTIES'   => $this->DrawProperties('label'),
        ]);
    }
}