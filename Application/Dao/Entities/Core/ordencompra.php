<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class ordencompra
{
    use ordencompraT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idO_C = 0;
	#[EntityField(DataType: 'datetime')]
	public ?string $Fecha_compra = null;
	public ?string $Descripcion = null;
	public ?string $marca = null;
	public ?string $modelo = null;
	public ?int $Orden_compra = null;
	public ?int $Factura_compra = null;
	public int $Precio_U = 0;
	public int $Cantidad = 0;
	public int $Precio_total = 0;
	public ?string $tipo = null;
	public ?int $idProveedor = null;
	public ?int $idEstado_oc = null;
	public ?int $idEstado_FC = null;
	public ?int $IdEmpresa = null;
	public ?int $idTipoProducto = null;
	public ?int $IdEstadoActivo = null;

    function __construct()
    {
    }
}