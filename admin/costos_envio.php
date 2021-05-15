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
        document.forms['Envios'].action = 'index.php';
        document.forms['Envios'].Flag.value = 0;
        document.forms['Envios'].submit();
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
        xhttp.open("POST", "../ajax/costos_de_envio.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
        xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
        xhttp.send();
      }
      function Agregar() {
        if (document.forms['Envios'].distancia.value != '' && document.forms['Envios'].costo.value != '') {
          var dist = document.forms['Envios'].distancia.value;
          var costo = document.forms['Envios'].costo.value;

          var xhttp = new XMLHttpRequest();
          xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              respuesta = this.responseText;
              //alert(respuesta);
              CargarContenido();
              if (respuesta == "error") {
                alert("Ocurrió un problema al agregar costo de envío");
              }
            } else if (this.readyState == 404) {
              alert("No se encuentra el archivo php");
            }
          };
          //alert(dist);
          var parameters = "distancia="+dist+"&costo="+costo;
          xhttp.open("POST", "../ajax/agregar_costo_envio.php", true);
          xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
          xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
          xhttp.send(parameters);
        }
        else alert("Debe colocar DISNTANCIA y COSTO");
      }
      function EliminarEntrada(id_costo) {
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            respuesta = this.responseText;
            //alert(respuesta);
            CargarContenido();
            if (respuesta == "error") {
              alert("Ocurrió un problema al agregar costo de envío");
            }
          } else if (this.readyState == 404) {
            alert("No se encuentra el archivo php");
          }
        };
        //alert(dist);
        var parameters = "id_costo="+id_costo;
        xhttp.open("POST", "../ajax/eliminar_costo_envio.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
        xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
        xhttp.send(parameters);
      }
    </script>
  </head>
  <body onload="javascript:CargarContenido();">
    <form name="Envios" action="costos_envio.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="Flag" value="">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <?php if ($Flag == 1 || $Flag == 2) { ?><p id="titulo" style="color:green;">¡Cambios Registrados!</p><?php } ?>
          <p id="titulo">Costos de Envío</p>

          <div class="head_tabla">
            <div class="row">
              <div class="col-sm-4">
                <p>Distancia (Km)</p>
              </div>
              <div class="col-sm-4">
                <p>Costo</p>
              </div>
            </div>
          </div>
          <div id="contenido">

          </div>
          <div class="row">
            <div class="col-sm-4">
              Más de <input type="number" name="distancia" value=""> kms.
            </div>
            <div class="col-sm-4">
              Costo: <input type="number" name="costo" value="">
            </div>
            <div class="col-sm-2">
              <input type="button" name="" value="Agregar" class="boton_azul" onclick="javascript:Agregar();">
            </div>
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
