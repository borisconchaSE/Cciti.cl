<?php

namespace Intouch\Framework\Annotation\Attributes;

#[\Attribute]
class EntityField {

    public function __construct(
        public String $ColumnName = '',
        public bool   $PrimaryKey = false,
        public bool   $AutoIncrement = true,
        public bool   $Ignore = false,
        public String $DataType = '',
        public bool   $Nullable = false,
        public String $DefaultWhenNull = ''
    ) {}

}