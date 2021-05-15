<?php
  session_start();
  if (!isset($_SESSION['UserID'])) {
    $error = 1;
    $message =  "Debe iniciar sesión para poder operar en esta sección";
  }
?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Limpiadín - Home</title>
    <meta name="author" content="Fernando Contreras">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="css/style.css">

    <!-- bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <!-- font-awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet"/>


    <!-- bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>

    <!-- Mercado Pago -->
    <script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>


    <script type="text/javascript" src="js/carrito.js"></script>
    <script type="text/javascript">
      function LoadStuffs(){
        document.getElementById("medios_de_pago").innerHTML = "<p>Cargando...</p>";
        CargarCarrito();
        CargarMercadoPago();
      }
      function RealizarPedido(){
        document.forms['Carrito'].action = 'realizar_pedido.php';
        document.forms['Carrito'].submit();
      }
      function CargarCarrito() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            respuesta = this.responseText;
            html_cant = document.getElementById("lista_carrito");
            html_cant.innerHTML = respuesta;
          }
        };
        xhttp.open("POST", "ajax/cargar_carrito.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
        xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
        xhttp.send();
      }
      function CargarMercadoPago() {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            respuesta = this.responseText;
            html_cant = document.getElementById("medios_de_pago");
            html_cant.innerHTML = respuesta;
          }
        };
        xhttp.open("POST", "ajax/cargar_mercadopago.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
        xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
        xhttp.send();
      }
      function AgregaryCargar(producto) {
        document.getElementById("medios_de_pago").innerHTML = "<p>Actualizando...</p>";
        AgregarProducto(producto);
        CargarMercadoPago();
      }
      function RestaryCargar(producto) {
        document.getElementById("medios_de_pago").innerHTML = "<p>Actualizando...</p>";
        RestarProducto(producto);
        CargarMercadoPago();
      }
      function EliminaryCargar(producto) {
        document.getElementById("medios_de_pago").innerHTML = "<p>Actualizando...</p>";
        EliminarItem(producto);
        CargarCarrito();
        CargarMercadoPago();
      }
    </script>
  </head>
  <body onload="javascript:LoadStuffs();">

    <form name="Carrito" action="index.php" method="post">
    </form>
    <?php include_once("header.php"); ?>

        <div class="cuerpo">
          <div class="container">
            <p id="titulo">Carrito de Compras</p>
            <?php if ($error != 1) { ?>
              <div class="row">
                <div class="col-sm-6" id="lista_carrito">
                </div>
                <div class="col-sm-5" id="medios_de_pago">
                </div>
              </div>
            <?php
              } else {
                echo $message;
              }
            ?>

          </div>
        </div>

      <?php include_once("footer.php"); ?>
  </body>
</html>
