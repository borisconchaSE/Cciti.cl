<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\General\UnitEnum;

#[Widget(Template: 'Image')]
class Image extends GenericWidget {

    public function __construct(
        public string  $Source,
        public ?string $Key = null,
        public string  $Title = '',
        public string  $Alt = '',
        public bool    $LazyLoad = false,
        public int     $Sizes = 0,
        public string  $Width = 'auto',
        public string  $Height = 'auto',
        public string  $Unit = UnitEnum::PIXELS,
        public array   $Classes = [],
        public array   $Styles = [],
        public array   $Attributes = [],
        public array   $Properties = []
    )
    {
        if ($Title != '')
            $this->AddAttribute('title', $Title);

        if ($Alt != '')
            $this->AddAttribute('alt', $Alt);

        if ($LazyLoad) {
            $this->AddAttribute('data-src', $Source);
            $this->AddAttribute('src', '');
            $this->AddClass('lazyautosizes');
            $this->AddClass('lazyloaded');
        }
        else {
            $this->AddAttribute('src', $Source);
        }

        if ($Sizes > 0) {
            $this->AddAttribute('data-sizes', 'auto');
            $this->AddAttribute('sizes', $Width . $Unit);
        }
        else {
            if ($this->Width != 'auto') {
                $this->AddAttribute('width', $this->Width . $Unit);
            }
            else {
                $this->AddAttribute('width', $this->Width);
            }
            
            if ($this->Height != 'auto') {
                $this->AddAttribute('height', $this->Height . $Unit);
            }
            else {
                $this->AddAttribute('height', $this->Height);
            }
        }

        $this->AddClasses($Classes);
        $this->AddStyles($Styles);        

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $this->Key);

        // Properties
        $this->AddProperties($Properties);

        parent::__construct(Replace: [
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}