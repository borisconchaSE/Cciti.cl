<?php
namespace Application\Dao\Entities\Clientes;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class Clientes
{
    use ClientesT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdClientes = 0;
	public ?string $NombreCliente = null;
	public ?string $CorreoCliente = null;
	public ?string $TelefonoPrimario = null;
	public ?string $TelefonoSecundario = null;
	public ?string $TelefonoOpcional = null;
	public ?int $IdCiudad = null;
	public string $Rut = '';
	public ?int $Activo = null;
	#[EntityField(DataType: 'datetime')]
	public ?string $FechaCreacion = null;

    function __construct()
    {
    }
}