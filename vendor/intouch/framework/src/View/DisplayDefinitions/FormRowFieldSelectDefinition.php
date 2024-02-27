<?php

namespace Intouch\Framework\View\DisplayDefinitions;

use Intouch\Framework\Collection\GenericCollection;

class FormRowFieldSelectDefinition {

    public function __construct(
        public string   $Key = '',
        public string   $Description = '',
        public          $DescriptionFunction = null,
        public          $JSDescriptionFunction = null,
        public array | GenericCollection    $Values = [],
        public string   $SelectedValue = '',
        public bool     $DisplaySearch = false,
        public bool     $MultipleSelection   = false,
        public string   $LinkToList = '',
        public string   $RefreshController = '',
        public array    $JSRefreshService = [],
        public bool     $TriggerChangeOnload = false,
        public array    $OptionAttributeNames = []
    )
    {        
    }

}