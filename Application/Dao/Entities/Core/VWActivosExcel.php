<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class VWActivosExcel
{
    use VWActivosExcelT;
    
	public int $idO_C = 0;
	#[EntityField(DataType: 'datetime')]
	public ?string $Fecha_Compra = null;
	public string $Empresa = '';
	public ?string $Rut_Proveedor = null;
	public ?string $Proveedor = null;
	public ?string $Nombre_Producto = null;
	public ?int $Orden_Compra = null;
	public ?int $Factura_Compra = null;
	public int $Cantidad = 0;
	public int $Precio_Total = 0;
	public string $Tipo = '';
	public ?string $Estado_Activo = null;
	public string $Estado_OC = '';
	public string $Estado_FC = '';

    function __construct()
    {
    }
}