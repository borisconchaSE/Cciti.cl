<?php
namespace Application\BLL\DataTransferObjects\Core;

class VWExcelEntregadoDto
{
    use VWExcelEntregadoDtoT;

    public function __construct(
		public int $id_stock = 0,
		public ?string $Fecha_Asignacion = null,
		public string $Nombre_Producto = '',
		public int $Cantidad = 0,
		public ?int $Precio_Producto = null,
		public string $Marca = '',
		public string $Modelo = '',
		public ?string $Empresa = null,
		public ?string $Empresa_asignado = null,
		public ?string $Departamento = null,
		public ?string $Ubicacion = null,
		public ?string $Tipo_Tonner = null
    ) {

    }
}