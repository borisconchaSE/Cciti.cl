<?php
namespace Application\BLL\DataTransferObjects\Core;

class TipoConfiguracionVisualizacionDto
{
    use TipoConfiguracionVisualizacionDtoT;

    public function __construct(
		public int $IdTipoConfiguracionVisualizacion = 0,
		public int $IdTipoVisualizacion = 0,
		public string $Descripcion = '',
		public string $Vigente = ''
    ) {

    }
}