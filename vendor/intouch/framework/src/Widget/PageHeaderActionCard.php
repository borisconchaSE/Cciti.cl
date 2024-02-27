<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Action\ActionTargetEnum;
use Intouch\Framework\Widget\Definitions\Container\Position;

#[Widget(Template: 'PageHeaderActionCard')]
class PageHeaderActionCard extends GenericWidget {

    public function __construct(
        public string           $Key,
        public string           $Action,
        public string           $Source,
        public string           $Text,
        public string           $Width = 'auto',
        public string           $Height = 'auto',
        public ?string          $Alt = 'Image',
        public ?string          $ActionTarget = ActionTargetEnum::TARGET_BLANK,
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
        public array            $ActionClasses = [],
        public array            $ActionStyles = [],
        public array            $ActionAttributes = [],
        public array            $ActionProperties = [],     
        public array            $CardClasses = [],
        public array            $CardStyles = [],
        public array            $CardAttributes = [],
        public array            $CardProperties = [],
        public array            $ImageClasses = [],
        public array            $ImageStyles = [],
        public array            $ImageAttributes = [],
        public array            $ImageProperties = [],
        public array            $TextClasses = [],
        public array            $TextStyles = [],
        public array            $TextAttributes = [],
        public array            $TextProperties = [],
    )
    {        
        // Classes
        $this->AddClasses($Classes);
        $this->AddClasses($ActionClasses, 'action');
        $this->AddClasses($CardClasses, 'card');
        $this->AddClass('card small text-center', 'card');
        $this->AddClasses($ImageClasses, 'image');
        $this->AddClasses($TextClasses, 'text');
        $this->AddClass('card-text', 'text');

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyles([
            ['margin-top', '-18px'], ['margin-bottom', '-18px']
        ]);
        $this->AddStyles($ActionStyles, 'action');
        $this->AddStyles($CardStyles, 'card');
        $this->AddStyles($ImageStyles, 'image');
        $this->AddStyles($TextStyles, 'text');

        // Attributes
        $this->AddAttributes($Attributes);
        $this->AddAttributes($ActionAttributes, 'action');
        $this->AddAttribute('href', $Action, 'action');
        $this->AddAttribute('target', $ActionTarget, 'action');
        $this->AddAttributes($CardAttributes, 'card');
        $this->AddAttributes($ImageAttributes, 'image');
        $this->AddAttribute('src', $Source, 'image');
        $this->AddAttribute('alt', $Alt, 'image');
        $this->AddAttribute('width', $Width, 'image');
        $this->AddAttribute('height', $Height, 'image');

        $this->AddAttributes($TextAttributes, 'action');

        if ($Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        // Properties
        $this->AddProperties($Properties);
        $this->AddProperties($ActionProperties, 'action');
        $this->AddProperties($CardProperties, 'card');
        $this->AddProperties($ImageProperties, 'image');
        $this->AddProperties($TextProperties, 'text');

        parent::__construct(Replace: [
            'TEXT'                => $Text,
            'CLASSES'             => $this->DrawClasses(),
            'STYLES'              => $this->DrawStyles(),
            'ATTRIBUTES'          => $this->DrawAttributes(),
            'PROPERTIES'          => $this->DrawProperties(),
            'ACTIONCLASSES'       => $this->DrawClasses('action'),
            'ACTIONSTYLES'        => $this->DrawStyles('action'),
            'ACTIONATTRIBUTES'    => $this->DrawAttributes('action'),
            'ACTIONPROPERTIES'    => $this->DrawProperties('action'),
            'CARDCLASSES'         => $this->DrawClasses('card'),
            'CARDSTYLES'          => $this->DrawStyles('card'),
            'CARDATTRIBUTES'      => $this->DrawAttributes('card'),
            'CARDPROPERTIES'      => $this->DrawProperties('card'),
            'IMAGECLASSES'        => $this->DrawClasses('image'),
            'IMAGESTYLES'         => $this->DrawStyles('image'),
            'IMAGEATTRIBUTES'     => $this->DrawAttributes('image'),
            'IMAGEPROPERTIES'     => $this->DrawProperties('image'),
            'TEXTCLASSES'         => $this->DrawClasses('text'),
            'TEXTSTYLES'          => $this->DrawStyles('text'),
            'TEXTATTRIBUTES'      => $this->DrawAttributes('text'),
            'TEXTPROPERTIES'      => $this->DrawProperties('text'),
        ]);
    }
}