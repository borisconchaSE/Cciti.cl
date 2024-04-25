<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class VWGastosExcel
{
    use VWGastosExcelT;
    
	public int $idO_C = 0;
	#[EntityField(DataType: 'datetime')]
	public ?string $Fecha_Compra = null;
	public ?string $Nombre_Producto = null;
	public ?int $Orden_Compra = null;
	public ?int $Factura_Compra = null;
	public int $Precio_Total = 0;
	public string $Tipo = '';
	public ?string $Proveedor = null;
	public string $Estado_OC = '';
	public string $Estado_FC = '';
	public string $Empresa = '';

    function __construct()
    {
    }
}