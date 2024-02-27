<?php

namespace Intouch\Framework\Collection;

class MergeDefinition {

    public function __construct(        
        public GenericCollection $Values,
        public $ParentKey = "",
        public $ChildReferenceKey = "",
        public $ParentCollectionName = "",
    )
    {
    }
}