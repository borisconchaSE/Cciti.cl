<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Intouch\Framework\Widget\GenericWidget;

class FormRowFieldContent extends FormRowField {

    public function __construct(
        string       $GroupKey = '',
        string       $GroupClass = '',
        public int              $Colspan = 1,
        public ?GenericWidget   $Content = null
    )
    {
        parent::__construct(
            GroupKey        :   $GroupKey,
            GroupClass      :   $GroupClass,
            Colspan         :   $Colspan,
            Content         :   $Content
        );
    }

}