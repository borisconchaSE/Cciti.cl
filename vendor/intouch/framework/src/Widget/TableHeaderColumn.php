<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'TableHeaderColumn')]
class TableHeaderColumn extends GenericWidget {

    public function __construct(
        public string $Title,
        public string $Key = '',
        public int    $Rowspan = 1,
        public int    $Colspan = 1,
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public ?int   $WidthPercent = null
    )
    {
        if (isset($this->WidthPercent)) {
            $this->AddStyle('width', $this->WidthPercent . '%');
        }

        if ($this->Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        if ($this->Colspan != 1) {
            $this->AddAttribute('colspan', $this->Colspan);
        }

        if ($this->Rowspan != 1) {
            $this->AddAttribute('rowspan', $this->Rowspan);
        }

        $this->AddClasses($Classes);
        $this->AddStyles($Styles);
        $this->AddAttributes($Attributes);
        $this->AddProperties($Properties);

        parent::__construct(Replace: [
            'CONTENT'       => $this->Title,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}