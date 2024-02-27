<?php
namespace Application\BLL\DataTransferObjects\Core;

class ordenCompraDto
{
    use ordenCompraDtoT;

    public function __construct(
		public int $idO_C = 0,
		public ?string $Fecha_compra = null,
		public ?string $Descripcion = null,
		public ?string $marca = null,
		public ?string $modelo = null,
		public ?int $Orden_compra = null,
		public ?int $Factura_compra = null,
		public int $Precio_U = 0,
		public int $Cantidad = 0,
		public int $Precio_total = 0,
		public ?string $tipo = null,
		public ?int $idProveedor = null,
		public ?int $idEstado_oc = null,
		public ?int $idEstado_FC = null,
		public ?int $IdEmpresa = null
    ) {

    }
}