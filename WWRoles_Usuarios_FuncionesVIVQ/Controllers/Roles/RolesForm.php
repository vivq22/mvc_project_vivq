<?php

namespace Controllers\Roles;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Roles\Roles as RolesDao;
use Utilities\Site;
use Utilities\Validators;

class RolesForm extends PublicController
{
    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de Rol %s",
        "INS" => "Nuevo Rol",
        "UPD" => "Editar Rol %s",
        "DEL" => "Eliminar Rol %s"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $rol = [
        "rolescod" => "",
        "rolesdsc" => "",
        "rolesest" => "ACT"
    ];
    private $rol_xss_token = "";

    public function run(): void
    {
        try {
            $this->getData();
            if ($this->isPostBack()) {
                if ($this->validateData()) {
                    $this->handlePostAction();
                }
            }
            $this->setViewData();
            Renderer::render("roles/form", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_RolesList",
                $ex->getMessage()
            );
        }
    }

    private function getData()
    {
        $this->mode = $_GET["mode"] ?? "NOF";
        if (isset($this->modeDescriptions[$this->mode])) {
            $this->readonly = $this->mode === "DEL" ? "readonly" : "";
            $this->showCommitBtn = $this->mode !== "DSP";
            if ($this->mode !== "INS") {
                $this->rol = RolesDao::getRolById(strval($_GET["rolescod"]));
                if (!$this->rol) {
                    throw new \Exception("No se encontró el Rol", 1);
                }
            }
        } else {
            throw new \Exception("Formulario cargado en modalidad inválida", 1);
        }
    }

    private function validateData()
    {
        if ($this->mode === "DEL") {
            $this->rol["rolescod"] = strval($_POST["rolescod"] ?? "");
            return true;
        }

        $errors = [];
        $this->rol_xss_token = $_POST["rol_xss_token"] ?? "";
        $this->rol["rolescod"] = strval($_POST["rolescod"] ?? "");
        $this->rol["rolesdsc"] = strval($_POST["rolesdsc"] ?? "");
        $this->rol["rolesest"] = strval($_POST["rolesest"] ?? "");

        if (Validators::IsEmpty($this->rol["rolescod"])) {
            $errors["rolescod_error"] = "El código del rol es requerido";
        }

        if (Validators::IsEmpty($this->rol["rolesdsc"])) {
            $errors["rolesdsc_error"] = "La descripción del rol es requerida";
        }

        if (!in_array($this->rol["rolesest"], ["ACT", "INA"])) {
            $errors["rolesest_error"] = "El estado del rol es inválido";
        }

        if ($_POST["rol_xss_token"] !==  $_SESSION[$this->name . "-rol_xss_token"] ?? "") {
            $this->throwError(
                "Something went wrong, try again.",
                "Trying to post with inconsistent parameter ROL_XSS_TOKEN value has: " . $_SESSION[$this->name . "-rol_xss_token"] . " recieved: " . $_POST["rol_xss_token"]
            );
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->rol[$key] = $value;
            }
            return false;
        }
        return true;
    }

    private function handlePostAction()
    {
        switch ($this->mode) {
            case "INS":
                $this->handleInsert();
                break;
            case "UPD":
                $this->handleUpdate();
                break;
            case "DEL":
                $this->handleDelete();
                break;
            default:
                throw new \Exception("Modo inválido", 1);
        }
    }

    private function handleInsert()
    {
        $result = RolesDao::insertRol(
            $this->rol["rolescod"],
            $this->rol["rolesdsc"],
            $this->rol["rolesest"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_RolesList",
                "Rol creado exitosamente"
            );
        }
    }

    private function handleUpdate()
    {
        $result = RolesDao::updateRol(
            $this->rol["rolescod"],
            $this->rol["rolesdsc"],
            $this->rol["rolesest"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_RolesList",
                "Rol actualizado exitosamente"
            );
        }
    }

    private function handleDelete()
    {
        $result = RolesDao::deleteRol($this->rol["rolescod"]);
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_RolesList",
                "Rol eliminado exitosamente"
            );
        }
    }

    private function setViewData(): void
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["rol_xss_token"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-rol_xss_token"] = $this->viewData["rol_xss_token"];
        $this->viewData["FormTitle"] = sprintf(
            $this->modeDescriptions[$this->mode],
            $this->rol["rolescod"]
        );
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["readonly"] = $this->readonly;

        $rolesestKey = "rolesest_" . strtolower($this->rol["rolesest"]);
        $this->rol[$rolesestKey] = "selected";

        $this->viewData["rol"] = $this->rol;
    }
}