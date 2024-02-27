<?php

namespace Intouch\Framework\Widget;

use Application\BLL\BusinessEnumerations\TipoLayoutFilaEnum;
use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'FormGroupColumnLabel')]
class FormGroupColumnLabel extends GenericWidget {

    public function __construct(
        public string $Key,
        public string $Content,
        public string $Label,
        public ?string $HiddenValue = null,
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public array  $LabelClasses = [],
        public array  $LabelStyles = [],
        public array  $LabelAttributes = [],
        public array  $LabelProperties = [],
        public array  $ContentClasses = [],
        public array  $ContentStyles = [],
        public array  $ContentAttributes = [],
        public array  $ContentProperties = [],
        public array  $HiddenClasses = [],
        public array  $HiddenStyles = [],
        public array  $HiddenAttributes = [],
        public array  $HiddenProperties = [],
        public int    $Colspan = 12,
        public int    $Layout = TipoLayoutFilaEnum::BOOTSTRAP
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

        $this->AddClasses($LabelClasses, 'label');
        $this->AddClass('control-label', 'label');
        $this->AddClasses($ContentClasses, 'content');
        $this->AddClasses($HiddenClasses, 'hidden');

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyles($LabelStyles, 'label');
        $this->AddStyle('display', 'block', 'label');
        $this->AddStyles($ContentStyles, 'content');
        $this->AddStyles($HiddenStyles, 'hidden');

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttributes($LabelAttributes, 'label');
        $this->AddAttributes($ContentAttributes, 'content');
        $this->AddAttribute('id', $Key . '-content', 'content');
        $this->AddAttributes($HiddenAttributes, 'hidden');
        $this->AddAttribute('id', $Key, 'hidden');

        // Properties
        $this->AddProperties($Properties);
        $this->AddProperties($LabelProperties, 'label');
        $this->AddProperties($ContentProperties, 'content');
        $this->AddProperties($HiddenProperties, 'hidden');

        if (isset($HiddenValue)) {
            $input = (new InputText(
                Key: $Key,
                Type: 'hidden',
                Value: $HiddenValue,
                Classes: $HiddenClasses,
                Styles: $HiddenStyles,
                Attributes: $HiddenAttributes,
                Properties: $HiddenProperties
            ))->Draw(false);
        }
        else {
            $input = '';
        }

        parent::__construct(Replace: [
            'LABELCONTENT'      => $this->Content,
            'LABELDESC'         => $this->Label,
            'INPUT'             => $input,
            'CLASSES'           => $this->DrawClasses(),
            'STYLES'            => $this->DrawStyles(),
            'ATTRIBUTES'        => $this->DrawAttributes(),
            'PROPERTIES'        => $this->DrawProperties(),
            'LABELCLASSES'      => $this->DrawClasses('label'),
            'LABELSTYLES'       => $this->DrawStyles('label'),
            'LABELATTRIBUTES'   => $this->DrawAttributes('label'),
            'LABELPROPERTIES'   => $this->DrawProperties('label'),
            'CONTENTCLASSES'    => $this->DrawClasses('content'),
            'CONTENTSTYLES'     => $this->DrawStyles('content'),
            'CONTENTATTRIBUTES' => $this->DrawAttributes('content'),
            'CONTENTPROPERTIES' => $this->DrawProperties('content'),
        ]);
    }
}