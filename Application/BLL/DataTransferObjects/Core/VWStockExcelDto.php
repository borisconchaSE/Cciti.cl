<?php
namespace Application\BLL\DataTransferObjects\Core;

class VWStockExcelDto
{
    use VWStockExcelDtoT;

    public function __construct(
		public int $id_stock = 0,
		public ?string $Fecha_Llegada = null,
		public string $Nombre_Producto = '',
		public int $Cantidad = 0,
		public ?int $Precio_Unitario = null,
		public string $Marca = '',
		public string $Modelo = '',
		public ?string $Empresa = null,
		public ?string $Tipo_Tonner = null,
		public string $Estado_Producto = ''
    ) {

    }
}