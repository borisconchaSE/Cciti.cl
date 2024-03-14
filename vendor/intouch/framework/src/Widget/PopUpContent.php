<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\Definitions\PopUpModal\ColorLineStyleEnum;
use Intouch\Framework\Widget\Definitions\PopUpModal\ModalSizeEnum;

#[Widget(Template: 'PopUpContent')]
class PopUpContent extends GenericWidget {

    public function __construct(
        public string           $Key,
        public string           $ColorLineStyle = ColorLineStyleEnum::LINE_PRIMARY,
        public string           $Title = '',
        public string           $SubTitle = '',
        public ?string          $DismissButtonText = 'Cancelar',
        public ?string          $DismissButtonStyle = ButtonStyleEnum::BUTTON_SOFT_SECONDARY,
        public ?GenericWidget   $Content = null,
        public array            $Buttons = [],
    )
    {
        // Classes
        $this->AddClass('color-line ' . $ColorLineStyle);

        $buttons = '';
        foreach($Buttons as $action) {
            if ($action instanceof ActionButton) {
                if ($buttons != '') {
                    $buttons .= "\n";
                }
                $buttons .= $action->Draw(false);
            }
        }

        parent::__construct(Replace: [
            'DISMISSBUTTONKEY'   => 'btnDismiss' . $Key,
            'DISMISSBUTTONTEXT'  => $DismissButtonText,
            'DISMISSBUTTONSTYLE' => $DismissButtonStyle,
            'CLASSES'            => $this->DrawClasses(),
            'TITLE'              => $Title,
            'SUBTITLE'           => $SubTitle,
            'CONTENT'            => $Content->Draw(false),
            'BUTTONS'            => $buttons,
        ]);
    }
}