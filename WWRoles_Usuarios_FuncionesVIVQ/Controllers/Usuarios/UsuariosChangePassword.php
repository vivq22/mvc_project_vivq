<?php

namespace Controllers\Usuarios;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Usuarios\Usuarios as UsuariosDao;
use Utilities\Site;
use Utilities\Validators;
use Exception;
class UsuariosChangePassword extends PublicController
{

    private $viewData = [];
    private $usuario = [
        "usercod" => "", // User code
        "userpswd" => "", // New password
    ];
    private $readonly = "";

    private $usuario_xss_token = "";

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
            Renderer::render("usuarios/form_password", $this->viewData);
        } catch (\Exception $ex) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_UsuariosList",
                $ex->getMessage()
            );
        }
    }

   private function getData()
    {
        $this->usuario["usercod"] = intval(
            $this->isPostBack()
                ? ($_POST["usercod"] ?? 0)
                : ($_GET["usercod"] ?? 0)
        );

        // Temporal: ver en pantalla
        if ($this->usuario["usercod"] === 0) {
            var_dump($_GET, $_POST);
            throw new \Exception("Usuario no válido", 1);
        }
    }


    private function validateData(): bool
{
    $this->usuario["userpswd"] = $_POST["userpswd"] ?? "";
    $userpswd_confirm = $_POST["userpswd_confirm"] ?? "";

    if (!Validators::IsValidPassword($this->usuario["userpswd"])) {
        throw new Exception("Contraseña debe tener al menos 8 caracteres, 1 número, 1 mayúscula, y 1 símbolo especial");
    }

    if ($this->usuario["userpswd"] !== $userpswd_confirm) {
        throw new Exception("Las contraseñas no coinciden");
    }

    return true;
}


    private function handlePostAction()
    {
        UsuariosDao::updatePassword(
            $this->usuario["usercod"],
            $this->usuario["userpswd"]
        );
        Site::redirectToWithMsg(
            "index.php?page=Usuarios_UsuariosList",
            "Contraseña actualizada correctamente"
        );
    }

    private function setViewData()
    {
        error_log("Render usercod: " . $this->usuario["usercod"]);
        $this->viewData["usuario"] = $this->usuario;
        $this->viewData["readonly"] = $this->readonly;
        $this->viewData["usuario_xss_token"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-usuario_xss_token"] = $this->viewData["usuario_xss_token"];
        $this->viewData["pageTitle"] = "Cambiar Contraseña de Usuario";
    }

}
