<?php
namespace Application\BLL\DataTransferObjects\Core;

class stockDto
{
    use stockDtoT;

    public function __construct(
		public int $id_stock = 0,
		public ?string $Fecha = null,
		public ?string $Fecha_asignacion = null,
		public string $Descripcion = '',
		public ?string $Usuario_asignado = null,
		public int $Cantidad = 0,
		public ?int $Precio_Unitario = null,
		public ?int $Precio_total = null,
		public string $estado_stock = '',
		public ?string $tipo = null,
		public ?int $idMarca = null,
		public ?int $IdEmpresa = null,
		public ?int $idO_C = null
    ) {

    }
}