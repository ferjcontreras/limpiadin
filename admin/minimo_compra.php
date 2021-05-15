<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

  if (!isset($_SESSION['UserIDAdmin'])) {
    include_once("login.php");
  }


  if ($Flag == 1) {
    $sqlqry = "SELECT Minimo.ID, Provincia.nombre , Depto.nombre, Minimo.Monto FROM Provincia, Depto, Minimo WHERE Depto.idProv = Provincia.ID AND Depto.ID = Minimo.IDDepartamento ";
    if ($provincia != 0) {
      $sqlqry .= " AND Provincia.ID = '$provincia' ";
    }
    $sqlqry .= "ORDER BY Provincia.nombre , Depto.nombre;";
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "Error: $sqlqry";
    }
    while($DBarr = mysqli_fetch_row($DBres)) {
      $IDMinimo = $DBarr[0];
      $NombreProvincia = $DBarr[1];
      $NombreDepto = $DBarr[2];
      $Monto = $DBarr[3];
      //$MaxItems++;

      if ($Monto != ${'minimo'.$IDMinimo}) {
        //echo "Cambió el ID $IDMinimo - $NombreProvincia - $NombreDepto";
        $sqlqry = "UPDATE Minimo SET Monto = '${'minimo'.$IDMinimo}' WHERE ID = '$IDMinimo'";
        mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error: $sqlqry";
          $Flag = 0;
        }
      }
    }
  }
  else if ($Flag == 2) {
    if ($provincia != 0) { // Vamos a setear una provincia entera
      $sqlqry = "SELECT ID FROM Depto WHERE idProv = '$provincia';"; // primero seleccionamos los departamentos de la provincia seleccionada
      $DBres = mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error: $sqlqry";
        $Flag = 0;
      }
      while($DBarr = mysqli_fetch_row($DBres)) {
        $sqlqry = "UPDATE Minimo SET Monto = '$minimo_gral' WHERE IDDepartamento = '".$DBarr[0]."'";
        mysqli_query($db, $sqlqry);
        if (mysqli_errno($db)) {
          echo "Error: $sqlqry";
          $Flag = 0;
        }
      }
    } else { // Tenemos que actualizar absolutamente todos los registros con el valor de Mínimo establecido
      $sqlqry = "UPDATE Minimo SET Monto = '$minimo_gral'";
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error: $sqlqry";
        $Flag = 0;
      }
    }
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
      function Guardar(){
        document.forms['Minimos'].Flag.value = 1;
        document.forms['Minimos'].submit();
      }
      function Regresar() {
        document.forms['Minimos'].action = 'index.php';
        document.forms['Minimos'].Flag.value = 0;
        document.forms['Minimos'].submit();
      }
      function Filtrar() {
        var provincia = document.forms['Minimos'].provincia.options[document.forms['Minimos'].provincia.selectedIndex].value;
        //document.forms['Minimos'].submit();
        CargarContenido(provincia);
      }
      function CargarContenido(provincia){
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
        var parameters = "provincia="+provincia;
        xhttp.open("POST", "../ajax/minimo_de_compra.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
        xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
        xhttp.send(parameters);
      }
      function ActualizarGrupo() {
        document.forms['Minimos'].Flag.value = '2';
        document.forms['Minimos'].submit();
      }
    </script>
  </head>
  <body onload="CargarContenido(0);">
    <form name="Minimos" action="minimo_compra.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="Flag" value="">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <?php if ($Flag == 1 || $Flag == 2) { ?><p id="titulo" style="color:green;">¡Cambios Registrados!</p><?php } ?>
          <p id="titulo">Mínimos de Compra Permitida</p>
          <div class="filtro">
            <div class="row">
              <div class="col-sm-4 offset-sm-1">
                <p>Filtrar por Provincia:</p>
              </div>
              <div class="col-sm-4">
                <select class="" name="provincia" onchange="javascript:Filtrar();">
                  <option value="0">-- Seleccionar Provincia --</option>
                  <?php
                    $sqlqry = "SELECT ID, nombre FROM Provincia ORDER BY nombre ";
                    $DBres = mysqli_query($db, $sqlqry);
                    if (mysqli_errno($db)) {
                      echo "Error: $sqlqry";
                    }
                    while($DBarr = mysqli_fetch_row($DBres)) {
                      echo "<option value='".$DBarr[0]."'>".$DBarr[1]."\n";
                    }
                  ?>
                </select>
              </div>
            </div>
          </div>
          <div class="head_tabla">
            <div class="row">
              <div class="col-sm-4">
                <p>Provincia</p>
              </div>
              <div class="col-sm-4">
                <p>Departamento</p>
              </div>
              <div class="col-sm-4">
                <p>Monto Mínimo</p>
              </div>
            </div>
          </div>
          <div id="contenido">

          </div>
          <div class="caja row">
            <div class="col-sm-2 offset-sm-2">
              <input style="margin-top:1em;" type="button" name="" value="Guardar Cambios" class="boton_verde" onclick="javascript:Guardar();">
            </div>
            <div class="col-sm-2">
              <input type="button" style="margin-top:1em;" name="" value="Regresar" class="boton_rojo" onclick="javascript:Regresar();">
            </div>
            <div class="col-sm-6" style="margin-top:1em;">
              Actualizar grupo con: <input type="number" name="minimo_gral" value="" placeholder="Mínimo"> <input type="button" name="" value="Actualizar" class="boton_azul" onclick="javascript:ActualizarGrupo();">
            </div>
          </div>
        </div>
        <?php include_once("../footer.php"); ?>
      </div>

    </form>
  </body>
</html>
