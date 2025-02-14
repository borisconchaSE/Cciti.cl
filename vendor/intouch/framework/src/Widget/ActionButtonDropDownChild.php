<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Action\ActionTargetEnum;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Definitions\ActionMenu\AnimationTypeEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelSizeEnum;
use Intouch\Framework\Widget\Definitions\Label\LabelStyleEnum;

#[Widget(Template: 'ActionButtonDropDownChild')]
class ActionButtonDropDownChild extends GenericWidget {

    public function __construct(
        public string           $Key,
        public ?GenericWidget   $Child,
        public ?string          $Badge = null,
        public string           $BadgeStyle = LabelStyleEnum::LABEL_DANGER,
        public string           $ButtonStyle = ButtonStyleEnum::BUTTON_DEFAULT,
        public string           $FormKey = '',
        public array            $Classes = [],
        public array            $Styles = [],
        public array            $Attributes = [],
        public array            $Properties = [],
    )
    {
        // Classes        
        $this->AddClasses($Classes);
        $this->AddClass('btn-block text-left btn-default');
        $this->AddClass($ButtonStyle);

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyle('position', 'relative');
  
        $this->AddStyle('border', 'none');
        $this->AddStyle('padding','5px 15px 5px 15px');
        $this->AddStyle('cursor', 'pointer');
        $this->AddStyle('white-space', 'nowrap');

        // Attributes
        $this->AddAttributes([
            ['id', $this->Key],
            ['name', $this->Key]
        ]);
        $this->AddAttributes($Attributes);

        // Properties
        $this->AddProperties($Properties);

        $badgeVisible = isset($Badge);
        $badgeClasses = ($badgeVisible) ? [] : ['hide'];
        array_push($badgeClasses, 'badge-element');

        $badge = (new Container(
            Key: $Key . 'Badge',
            Classes: $badgeClasses,
            Styles: [
                ['position', 'absolute'],
                ['right', '2px'],
                ['top', '0px'],
                ['cursor', 'pointer'],
            ],
            Children: [
                new Label(
                    Key: $Key . 'BadgeContent',
                    LabelStyle: $BadgeStyle,
                    LabelSize: LabelSizeEnum::LABEL_SMALL,
                    Content: isset($Badge) ? $Badge : '',
                    Classes: ['badge-text-element'],
                    Styles: [
                        ['border', '1px solid #ffffff'],
                        ['padding', '0px 4px 1px 4px'],
                        ['font-size', '10px'],
                        ['cursor', 'pointer'],
                    ]
                )
            ]
        ))->Draw(false);
            
        
    
        parent::__construct(Replace: [
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties(),
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'CONTENT'       => $this->Child->Draw(false),
            'BADGE'         => $badge
        ]);
    }
}