<?php
    include_once("etc/opendb.php");
    include_once("etc/register_globals.php");
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


    <script type="text/javascript">
      function Inicio() {
        document.forms['Compra'].action = "index.php";
        document.forms['Compra'].submit();
      }
    </script>
  </head>
  <body>
    <form name="Compra" action="index.php" method="post">
      <?php include_once("header.php"); ?>
      <div class="cuerpo">
        <div class="container">

          <?php if ($error != 1) { ?>
            <p id="titulo" style="color:red;">¡Compra Rechazada!</p>
            <p>El sitema de Mercado Pago nos ha informado que su pago ha sido rechazado. No se registró la compra.</p>
            <div class="botones_pedido" style="margin-top:1em;">
              <input class="boton_enviar" type="button" name="" value="Ir a Inicio" onclick="javascript:Inicio();">
            </div>
          <?php } else {
              // aqui se llega cuando no hay usuario logueado al sistema
              echo $message;
                ?>
                <div class="botones_pedido" style="margin-top:1em;">
                  <input class="boton_enviar" type="button" name="" value="Ir a Inicio" onclick="javascript:Inicio();">
                </div>
              <?php
                } ?>

        </div> <!-- container -->
      </div> <!-- cuerpo -->
      <?php include_once("footer.php"); ?>
    </form>
  </body>
</html>
