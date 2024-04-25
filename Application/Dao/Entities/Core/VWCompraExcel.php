<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class VWCompraExcel
{
    use VWCompraExcelT;
    
	public int $idO_C = 0;
	#[EntityField(DataType: 'datetime')]
	public ?string $Fecha_Compra = null;
	public ?string $Nombre_Producto = null;
	public ?string $Marca = null;
	public ?string $Modelo = null;
	public ?int $Orden_Compra = null;
	public ?int $Factura_Compra = null;
	public int $Precio_Unitario = 0;
	public int $Cantidad = 0;
	public int $Precio_Total = 0;
	public ?string $Tipo = null;
	public ?string $Proveedor = null;
	public string $Estado_OC = '';
	public string $Estado_FC = '';
	public string $Empresa = '';

    function __construct()
    {
    }
}