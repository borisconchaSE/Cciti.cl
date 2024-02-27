<?php

namespace Intouch\Framework\Widget;

use Application\BLL\BusinessEnumerations\TipoLayoutFilaEnum;
use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'FormGroupColumnContent')]
class FormGroupColumnContent extends GenericWidget {

    public function __construct(
        public GenericWidget    $Child,
        public string           $Key = '',
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
        public int              $Colspan = 12,
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

        // Styles
        $this->AddStyles($Styles);

        // Attributes
        $this->AddAttributes($Attributes);

        // Properties
        $this->AddProperties($Properties);

        if ($this->Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        parent::__construct(Replace: [
            'CONTENT'       => $this->Child->Draw(false),
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}