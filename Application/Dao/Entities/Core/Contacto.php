<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\EntityField;
use Intouch\Framework\Annotation\Attributes\Entity;

class Contacto
{
    use ContactoT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdContacto = 0;
	public string $Nombre = '';
	public string $Email = '';
	public string $Cargo = '';
	public ?string $Avatar = null;

    function __construct()
    {
    }
}