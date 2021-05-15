<?php
    include_once("../etc/opendb.php");
    session_start();

    function ObtenerTotalCarrito(){
      if (isset($_SESSION['carrito'])) {
        $arreglocarrito = $_SESSION['carrito'];
        $total = 0;
        for ($i = 0; $i < count($arreglocarrito); $i++) {
          $total = $total + $arreglocarrito[$i]['Cantidad'] * $arreglocarrito[$i]['Precio'];
        }
        return $total;
      }
      else return 0;
    }

    function ObtenerCostoEnvio() {
      global $db;
      // Obtiene el costo de envío deacuerdo a la ubicación del cliente
      if (isset($_SESSION['UserID'])){
        // Obtenemos el valor de la distancia del cliente
        $UserID = $_SESSION['UserID'];
        $sqlqry = "SELECT Distancia FROM Cliente WHERE IDUsuario = '$UserID'";
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($id)) {
          return "Error al consultar costo de envío";
        }
        $DBarr = mysqli_fetch_row($DBres);
        $distancia = $DBarr[0];


        // Ahora obtenemos el costo de envío de acuerdo a la distancia obtenida
        $sqlqry = "SELECT Distancia,Costo from CostoEnvio WHERE Distancia <= $distancia ORDER BY Distancia DESC LIMIT 1;";
        //return $sqlqry;
        $DBres = mysqli_query($db, $sqlqry);
        if (mysqli_errno($id)) {
          return "Error al consultar costo de envío";
        }
        $DBarr = mysqli_fetch_row($DBres);
        return $DBarr[1];
      }
      return "No se pudo obtener información del usuario";
    }



//  vendedor {"id":653163191,"nickname":"TESTG0UV575G","password":"qatest277","site_status":"active","email":"test_user_1521502@testuser.com"}
// comprador {"id":653164775,"nickname":"TESTINMLMYCL","password":"qatest4523","site_status":"active","email":"test_user_4231181@testuser.com"}


    // SDK de Mercado Pago
    require   '../MPLib/vendor/autoload.php';

    // Agrega credenciales
    MercadoPago\SDK::setAccessToken('TEST-6342493677849968-100214-3b39c3b28f80f91d92cccc78645571c3-653163191');

    // Crea un objeto de preferencia
    $preference = new MercadoPago\Preference();
    $preference->back_urls = array(
        "success" => "http://localhost/limpiadin/success.php",
        "failure" => "http://localhost/limpiadin/failure.php",
        "pending" => "http://localhost/limpiadin/pending.php"
      );
    $preference->auto_return = "approved";


    if (isset($_SESSION['carrito'])) $arreglocarrito = $_SESSION['carrito'];
    $datos = array();
    for ($i = 0; $i < count($arreglocarrito); $i++) {
      if ($arreglocarrito[$i]['Cantidad'] > 0) {
        $item = new MercadoPago\Item();
        $item->title = $arreglocarrito[$i]['Nombre'];
        $item->quantity = $arreglocarrito[$i]['Cantidad'];
        $item->unit_price = $arreglocarrito[$i]['Precio'];
        $datos[] = $item;
      }
    }

    // Tenemos que agregar también el costo de envío a MercadoPago
    $costo_de_envio = ObtenerCostoEnvio();
    $item = new MercadoPago\Item();
    $item->title = "Costo de Envío";
    $item->quantity = 1;
    $item->unit_price = $costo_de_envio;
    $datos[] = $item;


    $preference->items = $datos;
    $preference->save();
?>
<div class="total">
  <?php

    $costo_carrito = ObtenerTotalCarrito();
    $total_compra = $costo_carrito + $costo_de_envio;
  ?>
  <p>Subtotal: $<?php echo $costo_carrito ?></p>
  <p>Costo de Envío: $<?php echo $costo_de_envio ?></p>
  <p><b>TOTAL: $<?php echo $total_compra ?> </b></p>
</div>
<?php
  // Determinar el mínimo de compra en el sitio en la base de datos
  $sqlqry = "SELECT CodDepartamento FROM Cliente WHERE IDUsuario = '".$_SESSION['UserID']."';";
  $DBres = mysqli_query($db, $sqlqry);
  if (mysqli_errno($db)) {
    echo "error";
  }
  $DBarr = mysqli_fetch_row($DBres);
  $IDDepto = $DBarr[0];

  $sqlqry = "SELECT Monto, Depto.Nombre FROM Minimo, Depto WHERE Minimo.IdDepartamento = Depto.ID AND IdDepartamento = '$IDDepto'";
  $DBres = mysqli_query($db, $sqlqry);
  if (mysqli_errno($db)) {
    echo "error";
  }
  $DBarr = mysqli_fetch_row($DBres);
  $minimo = $DBarr[0];
  $NombreDepartamento = $DBarr[1];
  if ($costo_carrito > $minimo) {
?>
      <div class="mercado_row">
        <a id="boton_mercado_pago" href="<?php echo $preference->init_point; ?>">Pagar</a>
        <img id="logo_mercado_pago" src="images/mercadopago.png" alt="">
      </div>
      <div class="pedido_row">
        <input type="button" name="" value="Realizar Pedido" onclick="javascript:RealizarPedido();">
        <p style="margin-left:1em;">Realizar el pedido sin efectuar el pago</p>
      </div>
<?php
} else {
?>
      <p>USTED NO PUEDE EFECTUAR ESTA COMPRA</p>
      <p>La totalidad de artículos en el carrito no supera el mínimo de <b>$<?php echo $minimo ?></b> establecido para el Departamento de <b><?php echo $NombreDepartamento ?></b> </p>
<?php } ?>
