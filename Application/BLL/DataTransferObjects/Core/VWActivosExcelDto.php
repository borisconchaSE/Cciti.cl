<?php
namespace Application\BLL\DataTransferObjects\Core;

class VWActivosExcelDto
{
    use VWActivosExcelDtoT;

    public function __construct(
		public int $idO_C = 0,
		public ?string $Fecha_Compra = null,
		public string $Empresa = '',
		public ?string $Rut_Proveedor = null,
		public ?string $Proveedor = null,
		public ?string $Nombre_Producto = null,
		public ?int $Orden_Compra = null,
		public ?int $Factura_Compra = null,
		public int $Cantidad = 0,
		public int $Precio_Total = 0,
		public string $Tipo = '',
		public ?string $Estado_Activo = null,
		public string $Estado_OC = '',
		public string $Estado_FC = ''
    ) {

    }
}