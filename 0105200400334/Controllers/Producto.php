<?php

namespace Controllers\Examen;

use Controllers\PublicController;
use Dao\Examen\Productos as ProductosDAO;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Examen-Productos";
const XSR_KEY = "xsrToken_productos";

class Producto extends PublicController
{
    private array $viewData;
    private array $modes;

    public function __construct()
    {
        $this->modes = [
            "INS" => 'Creando nuevo Producto',
            "UPD" => 'Modificando Producto %s %s',
            "DEL" => 'Eliminando Producto %s %s',
            "DSP" => 'Mostrando Detalle de %s %s'
        ];
        $this->viewData = [
            "id_producto" => 0,
            "nombre" => "",
            "tipo" => "",
            "precio" => "",
            "marca" => "",
            "fecha_lanzamiento" => "",
            "mode" => "",
            "modeDsc" => "",
            "errores" => [],
            "readonly" => "",
            "showAction" => true,
            "xsrToken" => ""
        ];
    }

    public function run(): void
    {
        $this->capturarModoPantalla();
        $this->datosDeDao();

        if ($this->isPostBack()) {
            $this->datosFormulario();
            $this->validarDatos();
            if (count($this->viewData["errores"]) === 0) {
                $this->procesarDatos();
            }
        }

        $this->prepararVista();
        Renderer::render("examen/producto", $this->viewData);
    }

    private function throwError(string $message)
    {
        Site::redirectToWithMsg(LIST_URL, $message);
    }

    private function capturarModoPantalla()
    {
        if (isset($_GET["mode"])) {
            $this->viewData["mode"] = $_GET["mode"];
            if (!isset($this->modes[$this->viewData["mode"]])) {
                $this->throwError("BAD REQUEST: No se puede procesar su solicitud.");
            }
        }
    }

    private function datosDeDao()
    {
        if ($this->viewData["mode"] != "INS") {
            if (isset($_GET["id"])) {
                $this->viewData["id_producto"] = intval($_GET["id"]);
                $producto = ProductosDAO::getProductoById($this->viewData["id_producto"]);
                if ($producto) {
                    $this->viewData["nombre"] = $producto["nombre"];
                    $this->viewData["tipo"] = $producto["tipo"];
                    $this->viewData["precio"] = $producto["precio"];
                    $this->viewData["marca"] = $producto["marca"];
                    $this->viewData["fecha_lanzamiento"] = $producto["fecha_lanzamiento"];
                } else {
                    $this->throwError("BAD REQUEST: No existe registro en la DB");
                }
            } else {
                $this->throwError("BAD REQUEST: No se puede extraer el registro de la DB");
            }
        }
    }

    private function datosFormulario()
    {
        $this->viewData["nombre"] = $_POST["nombre"] ?? "";
        $this->viewData["tipo"] = $_POST["tipo"] ?? "";
        $this->viewData["precio"] = $_POST["precio"] ?? "";
        $this->viewData["marca"] = $_POST["marca"] ?? "";
        $this->viewData["fecha_lanzamiento"] = $_POST["fecha_lanzamiento"] ?? "";
        $this->viewData["xsrToken"] = $_POST["xsrToken"] ?? "";
    }

    private function validarDatos()
    {
        if (Validators::IsEmpty($this->viewData["nombre"])) {
            $this->viewData["errores"]["nombre"] = "El nombre es requerido";
        }
        if (Validators::IsEmpty($this->viewData["tipo"])) {
            $this->viewData["errores"]["tipo"] = "El tipo es requerido";
        }
        if (Validators::IsEmpty($this->viewData["precio"])) {
            $this->viewData["errores"]["precio"] = "El precio es requerido";
        }
        if (Validators::IsEmpty($this->viewData["marca"])) {
            $this->viewData["errores"]["marca"] = "La marca es requerida";
        }
        if (Validators::IsEmpty($this->viewData["fecha_lanzamiento"])) {
            $this->viewData["errores"]["fecha_lanzamiento"] = "La fecha de lanzamiento es requerida";
        }
        $tmpXsrToken = $_SESSION[XSR_KEY] ?? "";
        if ($this->viewData["xsrToken"] !== $tmpXsrToken) {
            error_log("Intento ingresar con un token inválido.");
            $this->throwError("Algo sucedió que impidió procesar la solicitud. ¡Intente de nuevo!");
        }
    }

    private function procesarDatos()
    {
        switch ($this->viewData["mode"]) {
            case "INS":
                if (
                    ProductosDAO::nuevoProducto(
                        $this->viewData["nombre"],
                        $this->viewData["tipo"],
                        floatval($this->viewData["precio"]),
                        $this->viewData["marca"],
                        $this->viewData["fecha_lanzamiento"]
                    ) > 0
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Producto agregado exitosamente.");
                } else {
                    $this->viewData["errores"]["global"] = ["Error al crear nuevo producto."];
                }
                break;
            case "UPD":
                if (
                    ProductosDAO::actualizarProducto(
                        $this->viewData["id_producto"],
                        $this->viewData["nombre"],
                        $this->viewData["tipo"],
                        floatval($this->viewData["precio"]),
                        $this->viewData["marca"],
                        $this->viewData["fecha_lanzamiento"]
                    )
                ) {
                    Site::redirectToWithMsg(LIST_URL, "Producto actualizado exitosamente.");
                } else {
                    $this->viewData["errores"]["global"] = ["Error al actualizar el producto."];
                }
                break;
            case "DEL":
                if (ProductosDAO::eliminarProducto($this->viewData["id_producto"])) {
                    Site::redirectToWithMsg(LIST_URL, "Producto eliminado exitosamente.");
                } else {
                    $this->viewData["errores"]["global"] = ["Error al eliminar el producto."];
                }
                break;
        }
    }

    private function prepararVista()
    {
        $this->viewData["modeDsc"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["nombre"],
            $this->viewData["id_producto"]
        );

        if (count($this->viewData["errores"]) > 0) {
            foreach ($this->viewData["errores"] as $campo => $error) {
                $this->viewData['error_' . $campo] = $error;
            }
        }

        if ($this->viewData["mode"] === "DEL" || $this->viewData["mode"] === "DSP") {
            $this->viewData["readonly"] = "readonly";
        }
        if ($this->viewData["mode"] === "DSP") {
            $this->viewData["showAction"] = false;
        }

        $this->viewData["xsrToken"] = hash("sha256", random_int(0, 1000000) . time() . 'producto' . $this->viewData["mode"]);
        $_SESSION[XSR_KEY] = $this->viewData["xsrToken"];
    }
}
