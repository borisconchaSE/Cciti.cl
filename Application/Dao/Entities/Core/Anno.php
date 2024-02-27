<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

class Anno
{
    use AnnoT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdAnno = 0;
	public int $Anno = 0;

    function __construct()
    {
    }
}