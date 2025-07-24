<?php

namespace Controllers\Checkout;

use Controllers\PublicController;
use Dao\Cart\Cart;
use Utilities\Security;

class Checkout extends PublicController
{
    public function run(): void
    {
        /*
        1) Mostrar el listado de productos a facturar y los detalles y totales de la proforma.
        2) Al dar click en Pagar
            2.1) Crear una orden de Paypal con los productos de la proforma.
            2.2) Redirigir al usuario a la página de Paypal para que complete el pago.
        
        */
        $viewData = array(
            "carretilla" => Cart::getAuthCart(Security::getUserId())
        );
        if ($this->isPostBack()) {
            $PayPalOrder = new \Utilities\Paypal\PayPalOrder(
                "test" . (time() - 10000000),
                "http://localhost/mvc_project_vivq/index.php?page=Checkout_Error",
                "http://localhost/mvc_project_vivq/index.php?page=Checkout_Accept"
            );

            foreach ($viewData["carretilla"] as $producto) {
                $PayPalOrder->addItem(
                    $producto["productName"],
                    $producto["productDescription"],
                    $producto["productId"],
                    $producto["crrprc"],
                    0,
                    $producto["crrctd"],
                    "DIGITAL_GOODS"
                );
            }

            $PayPalRestApi = new \Utilities\PayPal\PayPalRestApi(
                \Utilities\Context::getContextByKey("PAYPAL_CLIENT_ID"),
                \Utilities\Context::getContextByKey("PAYPAL_CLIENT_SECRET")
            );
            $PayPalRestApi->getAccessToken();
            $response = $PayPalRestApi->createOrder($PayPalOrder);

            $_SESSION["orderid"] = $response->id;
            foreach ($response->links as $link) {
                if ($link->rel == "approve") {
                    \Utilities\Site::redirectTo($link->href);
                }
            }
            die();
        }

        \Views\Renderer::render("paypal/checkout", $viewData);
    }
}