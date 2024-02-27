<?php
namespace Application\Dao\Entities\<<NAMESPACE>>;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '<<SCHEMA>>')]
class <<ENTITY>>
{
    use <<ENTITY>>T;
    
<<FULLFIELDS>>

    function __construct()
    {
    }
}