<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class VWEntregadoExcel
{
    use VWEntregadoExcelT;
    
	public int $id_stock = 0;
	#[EntityField(DataType: 'datetime')]
	public ?string $Fecha_Asignacion = null;
	public string $Nombre_Producto = '';
	public int $Cantidad = 0;
	public ?int $Precio_Producto = null;
	public string $Marca = '';
	public string $Modelo = '';
	public ?string $Empresa = null;
	public ?string $Empresa_asignado = null;
	public ?string $Departamento = null;
	public ?string $Ubicacion = null;
	public string $Centro = '';
	public ?string $Tipo_Tonner = null;

    function __construct()
    {
    }
}