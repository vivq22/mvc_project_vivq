<?php

namespace Controllers\Roles;

use Controllers\PublicController;
use Utilities\Context;
use Dao\Roles\Roles as DaoRoles;
use Views\Renderer;

class RolesList extends PublicController{

    private $viewData = [];
    private $roles = [];


    public function run():void
    {
        $this->getParamsFromContext();
        $this->getParams();
        $this->roles = DaoRoles::getRoles();
        $this->viewData["roles"] = $this->roles;
        Renderer::render("roles/list", $this->viewData);
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