<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'Script')]
class Script extends GenericWidget {

    public function __construct(
        public array $Scripts
    )
    {
        $events = '';
        $id = (new \DateTime())->format('YmdHis') . random_int(1,5000);

        foreach($Scripts as $script) {
            if ($events != '') {
                $events .= "\n";
            }
            $events .= $script;
        }

        parent::__construct(Replace: [
            'EVENTS'   => $events,
            'IDSCRIPT' => $id
        ]);
    }
}