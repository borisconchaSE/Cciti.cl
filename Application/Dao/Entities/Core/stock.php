<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class stock
{
    use stockT;
    
	#[EntityField(PrimaryKey: true)]
	public int $id_stock = 0;
	#[EntityField(DataType: 'datetime')]
	public ?string $Fecha = null;
	#[EntityField(DataType: 'datetime')]
	public ?string $Fecha_asignacion = null;
	public string $Descripcion = '';
	public ?string $Empresa_asignado = null;
	public ?string $Departamento = null;
	public ?string $Ubicacion = null;
	public int $Cantidad = 0;
	public ?int $Precio_Unitario = null;
	public ?int $Precio_total = null;
	public string $estado_stock = '';
	public ?string $tipo = null;
	public ?int $idMarca = null;
	public ?int $IdEmpresa = null;
	public ?int $idModelo = null;
	public ?int $idO_C = null;

    function __construct()
    {
    }
}