<?php

namespace Intouch\Framework\Widget;

use Application\BLL\BusinessEnumerations\TipoLayoutFilaEnum;
use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'FormGroupColumn')]
class FormGroupColumn extends GenericWidget {

    public function __construct(
        public Input  $Input,
        public string $Label,
        public string $Key = '',
        public array  $Classes = [],
        public array  $LabelClasses = [],
        public array  $Styles = [],
        public array  $LabelStyles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public bool   $Required = false,
        public bool   $Disabled = true,
        public int    $Colspan = 12,
        public ?int   $Layout = TipoLayoutFilaEnum::BOOTSTRAP
    )
    {
        // Classes
        $this->AddClasses($Classes);

        if ($Layout == TipoLayoutFilaEnum::BOOTSTRAP) {
            $this->AddClass('col-sm-' . $Colspan);
        }
        else if ($Layout == TipoLayoutFilaEnum::FLEXBOX) {
            $this->AddStyle('flex-grow', $Colspan);
            $this->AddStyle('margin-left', '5px');
            $this->AddStyle('margin-right', '5px');
        }
        
        if ($this->Required) {
            $this->AddClass('required');
        }

        $this->AddClasses($LabelClasses, 'label');
        $this->AddClass('control-label', 'label');

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyles($LabelStyles, 'label');

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $this->Key);

        // Properties
        $this->AddProperties($Properties);

        if ($this->Required == true) {
            $this->Label .= '<span class="req"> *</span>';
        }

        parent::__construct(Replace: [
            'CLASSES'       => $this->DrawClasses(),
            'LABELDESC'     => $this->Label,
            'LABELCLASSES'  => $this->DrawClasses('label'),
            'STYLES'        => $this->DrawStyles(),
            'LABELSTYLES'   => $this->DrawStyles('label'),
            'INPUT'         => $this->Input->Draw(false),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}