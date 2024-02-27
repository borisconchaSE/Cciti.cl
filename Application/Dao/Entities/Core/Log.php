<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class Log
{
    use LogT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdLog = 0;
	public int $IdTipoLog = 0;
	public int $IdUsuario = 0;
	public int $IdUsuarioAfectado = 0;
	public ?string $Descripcion = null;
	#[EntityField(DataType: 'datetime')]
	public ?string $Fecha = null;

    function __construct()
    {
    }
}