<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

  if (!isset($_SESSION['UserIDAdmin'])) {
    include_once("login.php");
  }


  if ($Flag == 1) {

  }


?>

<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Limpiadín - Admin</title>
    <meta name="author" content="Fernando Contreras">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="../css/style.css">

    <!-- bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">


    <!-- bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script type="text/javascript">
      function Regresar() {
        document.forms['Stock'].action = 'index.php';
        document.forms['Stock'].Flag.value = 0;
        document.forms['Stock'].submit();
      }
      function CargarContenido(){
        //alert("vamos a agregar el producto "+id);
        document.getElementById("contenido").innerHTML = "Cargando...";
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            respuesta = this.responseText;
            //alert(respuesta);
            //alert(respuesta);

            if (respuesta == "error") {
              alert("Ocurrió un problema al cargar el contenido");
            }
            else {
              //alert("Agregamos "+respuesta+"... cant-"+id+"-id");
              html_cant = document.getElementById("contenido");
              html_cant.innerHTML = respuesta;
            }
          } else if (this.readyState == 404) {
            alert("No se encuentra el archivo php");
          }
        };
        xhttp.open("POST", "../ajax/stock_de_articulos.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
        xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
        xhttp.send();
      }
      function VerHistorial(cual) {
        document.forms['Stock'].id_producto.value = cual;
        document.forms['Stock'].action = "historial_stock.php";
        document.forms['Stock'].submit();
      }


    </script>
  </head>
  <body onload="javascript:CargarContenido();">
    <form name="Stock" action="stock_articulos.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="Flag" value="">
      <input type="hidden" name="id_producto" value="">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <?php if ($Flag == 1 || $Flag == 2) { ?><p id="titulo" style="color:green;">¡Cambios Registrados!</p><?php } ?>
          <p id="titulo">Stock de Artículos</p>

          <div class="head_tabla">
            <div class="row">
              <div class="col-sm-4">
                <p>Producto</p>
              </div>
              <div class="col-sm-2">
                <p>Cantidad Disponible</p>
              </div>
              <div class="col-sm-4">
                <p>Último Movimiento</p>
              </div>
            </div>
          </div>
          <div id="contenido">

          </div>
          <div class="row" style="margin-top:1em;">
            <div class="col-sm-2">
              <input type="button" name="" value="Regresar" class="boton_rojo" onclick="javascript:Regresar();">
            </div>

          </div>
        </div>
        <?php include_once("../footer.php"); ?>
      </div>
    </form>
  </body>
</html>
