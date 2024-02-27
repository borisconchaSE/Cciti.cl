<?php
namespace Application\BLL\DataTransferObjects\Core;

class ConfiguracionVisualizacionDto
{
    use ConfiguracionVisualizacionDtoT;

    public function __construct(
		public int $IdConfiguracionVisualizacion = 0,
		public int $IdEmpresa = 0,
		public int $IdTipoConfiguracionVisualizacion = 0
    ) {

    }
}