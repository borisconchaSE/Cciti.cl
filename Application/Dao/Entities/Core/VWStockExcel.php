<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class VWStockExcel
{
    use VWStockExcelT;
    
	public int $id_stock = 0;
	#[EntityField(DataType: 'datetime')]
	public ?string $Fecha_Llegada = null;
	public string $Nombre_Producto = '';
	public int $Cantidad = 0;
	public ?int $Precio_Unitario = null;
	public string $Marca = '';
	public string $Modelo = '';
	public ?string $Empresa = null;
	public ?string $Tipo_Tonner = null;
	public string $Estado_Producto = '';

    function __construct()
    {
    }
}