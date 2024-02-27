<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\ActionMenu\AnimationTypeEnum;

#[Widget(Template: 'ActionMenu')]
class ActionMenu extends GenericWidget {

    public function __construct(
        public string $Key = '',
        public array $Classes = [],
        public array $TitleClasses = [],
        PUBLIC ARRAY $MenuTreeClasses = [],
        public array $Styles = [],
        public string $Title = '',
        public ?FaIcon $Icon = null,
        public bool $Animated = true,
        public string $AnimationType = AnimationTypeEnum::FLIP_IN_X,
        public ?array $Items = [],
        public ?string $Action = '',
        public bool   $Active = false,
        public array $Attributes = [],
        public array $MenuTreeAttributes = []
    )
    {
        // Classes
        $this->AddClasses($Classes);
        $this->AddClasses($TitleClasses, 'title');
  

        // Styles
        $this->AddStyles($Styles);
 

        // Attributes
        $this->AddAttributes($Attributes);        

        if ($Active) {
            $this->AddClass('active');
        }

        if (isset($Items)) {
            $menuTree = (new ActionMenuTree(
                Animated: $Animated,
                AnimationType: $AnimationType,
                Items: $Items,
                Classes: $MenuTreeClasses,
                Attributes: $MenuTreeAttributes
            ))->Draw(false);
        }

        $faicon = '';
        if (isset($Icon)) {            
            $faicon = $Icon->Draw(false);            
        }

        parent::__construct(Replace: [
            'MENUTITLE'     => $Title,
            'MENUTREE'      => $menuTree,
            'ICON'          => $faicon,
            'CLASSES'       => $this->DrawClasses(),
            'TITLECLASSES'  => $this->DrawClasses('title'),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes()
        ]);
    }
}