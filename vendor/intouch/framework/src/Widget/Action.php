<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Action\ActionTargetEnum;
use Intouch\Framework\Widget\Definitions\ActionMenu\AnimationTypeEnum;

#[Widget(Template: 'Action')]
class Action extends GenericWidget {

    public function __construct(
        public string $Key = '',
        public ?GenericWidget $Child = null,
        public array $Classes = [],
        public array $Styles = [],
        public ?string $Action = '',
        public ?string $ActionTarget = ActionTargetEnum::TARGET_SELF
    )
    {
        // Classes
        $this->AddClasses($Classes);

        // Styles
        $this->AddStyles($Styles);
        $this->AddStyle('cursor', 'pointer');

        // Attributes
        $this->AddAttributes([
            ['id', $this->Key],
            ['href', $this->Action],
            ['target', $this->ActionTarget],
            ['name', $this->Key]
        ]);


        parent::__construct(Replace: [
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties(),
            'CLASSES'   => $this->DrawClasses(),
            'STYLES'    => $this->DrawStyles(),
            'CONTENT'   => $this->Child->Draw(false)
        ]);
    }
}