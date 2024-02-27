<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Key;
use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldTypeEnum;
use PhpOffice\PhpSpreadsheet\Chart\Properties;

#[Widget(Template: 'InputDateRange')]
class InputDateRange extends Input {    

    public function __construct(
        public ?string  $Key                = null,
        public ?string  $Type               = null,
        public ?string  $Value              = null,
        public ?string  $Placeholder        = null,
        public ?bool    $Required           = false,
        public ?FaIcon  $Icon               = null,
        public array    $Classes            = [],
        public array    $Styles             = [],
        public array    $Attributes         = [],
        public array    $DateClasses        = [],
        public array    $DateStyles         = [],
        public array    $DateAttributes     = [],
        public array    $DateProperties     = [],
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClass('input-group');
        $this->AddClass('date');
        $this->AddClass('new');

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($this->Attributes);
        array_push($DateAttributes, ['id', $this->Key]);

        // Properties
        array_push($DateProperties, ['required', $this->Required]);

        // Icon
        $inputAddon = '';
        if (isset($Icon)) {
            $inputAddon = (new InputAddon(
                Icon: $this->Icon
            ))->Draw(false);
        }

        $input = (new InputText(
            Key: $this->Key,
            Type: FormRowFieldTypeEnum::INPUT_TEXT,
            Classes: $DateClasses,
            Required: $Required,
            Styles: $DateStyles,
            Value: $this->Value,
            Placeholder: $this->Placeholder,
            Attributes: $DateAttributes,
            Properties: $DateProperties
        ))->Draw(false);

        parent::__construct(Replace: [
            'INPUTADDON'    => $inputAddon,
            'INPUT'         => $input,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}