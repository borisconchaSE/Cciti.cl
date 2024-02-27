<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'MenuItem')]
class MenuItem extends GenericWidget {

    public function __construct(
        public string $Action,
        public array $Classes = [],
        public array $TitleClasses = [],
        public array $Styles = [],
        public string $Title = '',
        public ?FaIcon $Icon = null,
        public bool $Active = false,
        
    )
    {
        $this->AddStyle('display', 'block');
        $this->AddStyle('cursor', 'pointer');

        if (!isset($Icon) || $Title == '') {
            $this->AddClass('action-menu-title', 'title');
        }
        else {            
            $this->AddClass('action-menu-title-withicon', 'title');
        }

        $this->AddClasses($Classes);
        $this->AddClasses($TitleClasses, 'title');
        $this->AddStyles($Styles);

        $faicon = '';
        if (isset($Icon)) {
            $faicon = $Icon->Draw(false);
        }

        if ($Active) {
            $this->AddClass('active');
        }

        parent::__construct(Replace: [
            'MENUTITLE'     => $Title,
            'ACTION'        => $Action,
            'ICON'          => $faicon,
            'CLASSES'       => $this->DrawClasses(),
            'TITLECLASSES'  => $this->DrawClasses('title'),
            'STYLES'        => $this->DrawStyles()
        ]);
    }
}