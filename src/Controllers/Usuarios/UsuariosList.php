<?php

namespace Controllers\Usuarios;

use Controllers\PublicController;
use Utilities\Context;
use Dao\Usuarios\Usuarios as DaoUsuarios;
use Views\Renderer;

class UsuariosList extends PublicController{

    private $viewData = [];
    private $usuarios = [];


    public function run():void
    {
        $this->getParamsFromContext();
        $this->getParams();
        $this->usuarios = DaoUsuarios::getUsuarios();
        $this->viewData["usuarios"] = $this->usuarios;
        Renderer::render("usuarios/list", $this->viewData);
    }

    private function getParams(): void
    {
        // Aquí podrías obtener parámetros específicos de la solicitud, si es necesario.
        // Por ejemplo, filtros o paginación.
    }

    private function getParamsFromContext(): void
    {
        // Aquí podrías obtener parámetros del contexto, si es necesario.
        // Por ejemplo, información de sesión o del usuario.
    }
}