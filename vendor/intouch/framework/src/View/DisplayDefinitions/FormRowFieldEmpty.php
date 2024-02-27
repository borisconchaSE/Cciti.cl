<?php

namespace Intouch\Framework\View\DisplayDefinitions;

class FormRowFieldEmpty extends FormRowField {

    public function __construct(
        public int      $Colspan = 1
    )
    {
        parent::__construct(
            Colspan: $Colspan
        );
    }

}