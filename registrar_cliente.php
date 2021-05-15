<?php
  include_once("etc/opendb.php");
  include_once("etc/register_globals.php");

  //$Flag = $_POST['Flag'];


  function CalcularDistancia($Direccion, $Departamento, $Provincia) {
    $origin      = "Rivadavia, Mendoza, Argentina";
    //echo "esta es la dire recibida: ".$Direccion;
    $destination = "$Direccion, $Departamento, $Provincia, Argentina";
    //echo "Calculamos la distancia hacia: $destination <br>";
    $key = "AIzaSyBovhkAkB7CPxVLUl8Cm27Kvwu21Dl42NY";
    $url = "https://maps.googleapis.com/maps/api/directions/json?origin=".urlencode($origin).",IL&destination=" . urlencode( $destination) . "&sensor=false&key=" . $key;
    $jsonfile = file_get_contents($url);
    //echo $jsonfile;
    $jsondata = json_decode($jsonfile, true);
    return $jsondata["routes"][0]["legs"][0]["distance"]["value"];
  }

  function GetNombre($ID, $Tabla) {
    global $db;
    $sqlqry = "SELECT nombre FROM $Tabla WHERE ID = '$ID'";
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "Error: $sqlqry";
    }
    if (mysqli_num_rows($DBres) != 0) {
      $DBarr = mysqli_fetch_row($DBres);
      return $DBarr[0];
    }
    return "nada";
  }


  if ($Flag == 1) {
    $error = 0;
    // Registramos el cliente
    $sqlqry = "INSERT INTO Usuario(Nombre, Usuario, Clave, Perfil) VALUES ('$nombre', '$usuario', '".md5($clave)."', 10);"; // el perfil 10 corresponde a usuario "cliente"
    mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "Error: $sqlqry";
      $error = 1;
    }
    $IDUsuario = mysqli_insert_id($db);

    $distancia = CalcularDistancia($domicilio, GetNombre($departamento, "Depto"), GetNombre($provincia, "Provincia"));

    $distancia = $distancia / 1000;
    //echo "Distancia: $distancia <br>";
    $sqlqry =  "INSERT INTO Cliente(Nombre, Direccion, Telefono, Email, CodDepartamento, Distancia, IDUsuario) VALUES('$nombre', '$domicilio', '$telefono', '$email', $departamento, $distancia, $IDUsuario);";
    //echo "$domicilio";
    //echo $sqlqry."<br>";
    mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "Error: $sqlqry";
      $error = 1;
    }
  }


?>
<!DOCTYPE html>
<html lang="es" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Limpiadín - Registro de Cliente</title>
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
      function ClaveOK() {
        if (document.forms['Cliente'].clave.value == document.forms['Cliente'].confirmacion.value) {
          var str = String(document.forms['Cliente'].clave.value);
          if (str.length >= 8) return true;
          else {
            alert("La clave debe tener al menos 8 caracteres");
            return false;
          }
        }
        else {
          alert("¡Verifique que las claves coincidan!");
          return false;
        }

      }
      function Registrar() {
        //VerificarEmail();
        //Disponible();
        error1 = document.forms['Cliente'].error1.value;
        error2 = document.forms['Cliente'].error2.value;
        //alert(error1+" - "+error2);
        if (ClaveOK() && error1 == 0 && error2 == 0) {
          document.forms['Cliente'].Flag.value = 1;
          document.forms['Cliente'].submit();
        }
        else {
          alert('Verifique la disponibilidad de Usuario o Email');
        }
      }
      function VerificarEmail(){
        document.forms['Cliente'].error2.value = 0;
        var xhttp = new XMLHttpRequest();
        var valor = document.forms['Cliente'].email.value;
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            respuesta = this.responseText;
            //alert(respuesta);
            if (respuesta == "1") {
              alert("Ya existe un cliente registrado con el email "+valor+", por favor utilice uno alternativo");
              document.forms['Cliente'].usuario.focus();
              document.forms['Cliente'].error2.value = 1;
            }
          }
        };
        var parameters = "email="+valor;
        xhttp.open("POST", "ajax/check_cliente.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
        xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
        xhttp.send(parameters);
      }
      function Disponible() {
        document.forms['Cliente'].error1.value = 0;
        var xhttp = new XMLHttpRequest();
        var valor = document.forms['Cliente'].usuario.value;
        xhttp.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            respuesta = this.responseText;
            //alert(respuesta);
            if (respuesta == "1") {
              alert("Ya existe un usuario "+valor+", por favor utilice uno alternativo");
              document.forms['Cliente'].usuario.focus();
              document.forms['Cliente'].error1.value = 1;
            }
          }
        };
        var parameters = "usuario="+valor;
        xhttp.open("POST", "ajax/check_user.php", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.setRequestHeader('If-Modified-Since', 'Wed, 1 Jan 2003 00:00:00 GMT');
        xhttp.setRequestHeader( "Cache-Control", "no-store, no-cache, max-age=0, must-revalidate" );
        xhttp.send(parameters);
        //alert("El usuario no se encuentra disponible");
        //document.forms['Cliente'].usuario.focus();
      }
      function CargarDeptos() {
        var opcion = parseInt(document.forms['Cliente'].provincia.options[document.forms['Cliente'].provincia.selectedIndex].value);
        document.forms['Cliente'].departamento.length = 1;
        switch (opcion) {
        //switch (2) {
          <?php
            $sqlqry = "SELECT ID FROM Provincia ORDER BY ID;";
            $DBres = mysqli_query($db, $sqlqry);
            if (mysqli_errno($db)) {
              echo "Error: $sqlqry";
            }
            while ($DBarr = mysqli_fetch_row($DBres)) {
          ?>
            case <?php echo $DBarr[0] ?>:
              //alert("Entro al menos en alguno");
              <?php
                  $sqlqry = "SELECT ID, nombre FROM Depto WHERE idProv = '".$DBarr[0]."' ORDER BY nombre;";
                  $DBres2 = mysqli_query($db, $sqlqry);
                  if (mysqli_errno($db)) {
                    echo "Error: $sqlqry";
                  }
                  echo "u=1\n";
                  while ($DBarr2 = mysqli_fetch_row($DBres2)) {
              ?>
                      document.forms['Cliente'].departamento.options[u] = new Option("<?php echo $DBarr2[1] ?>",'<?php echo $DBarr2[0] ?>');
              <?php
                      echo "u++\n";
                  }
              ?>
              break;
          <?php
            }
          ?>
        }
      }
      function Cancelar() {
        document.forms['Cliente'].action = 'index.php';
        document.forms['Cliente'].submit();
      }
      function MostrarMensajes(){
        <?php
          if($Flag == 1 && $error == 0){
            echo "alert('¡Cliente registrado con éxito!');\n";
        ?>
            document.forms['Cliente'].action = "index.php";
            document.forms['Cliente'].submit();
        <?php
          } else if ($Flag == 1 && $error == 1) {
            echo "alert('Hubo un problema al registrar el cliente');\n";
          }
        ?>
      }
    </script>
  </head>
  <body onload="Javascript:MostrarMensajes();">
    <form name="Cliente" action="registrar_cliente.php" method="post">
      <input type="hidden" name="Flag" value="<?php echo $Flag; ?>">
      <input type="hidden" name="error1" value="0">
      <input type="hidden" name="error2" value="0">
      <?php include_once("header.php"); ?>

      <div class="cuerpo">
        <div class="container">
          <p id="titulo">Registrar Cliente</p>
          <div class="row datos_clientes">
            <div class="col-sm-6">
              <div class="dato_cliente">
                <label for="nombre">
                  Nombre:
                  <input type="text" name="nombre" value="" >
                </label>
              </div>
              <div class="dato_cliente">
                <label for="email">
                  Email:
                  <input type="email" name="email" value="" onchange="Javascript:VerificarEmail();">
                </label>
              </div>
              <div class="dato_cliente">
                <label for="telefono">
                  Teléfono:
                  <input type="number" name="telefono" value="" >
                </label>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="dato_cliente">
                <label for="domicilio">
                  Domicilio:
                  <input type="text" name="domicilio" value="" >
                </label>
              </div>
              <div class="dato_cliente">
                <label for="provincia">
                  Provincia:
                  <select name="provincia" onchange="Javascript:CargarDeptos();">
                    <option value =0>--Seleccione Provincia--</option>
                    <?php
                      $sqlqry = "SELECT ID, nombre FROM Provincia ORDER BY nombre;";
                      $DBres = mysqli_query($db, $sqlqry);
                      if (mysqli_errno($db)) {
                        echo "Error en consulta: $sqlqry";
                      }
                      while($DBarr = mysqli_fetch_row($DBres)) {
                    ?>
                      <option value=<?php echo $DBarr[0] ?>><?php echo $DBarr[1] ?></option>
                    <?php
                      }
                    ?>

                  </select>
                </label>
              </div>
              <div class="dato_cliente">
                <label for="departamento">
                  Departamento:
                  <select name="departamento" >
                    <option value=0 selected>-- Seleccionar Departamento --</option>
                  </select>
                </label>
              </div>
            </div>
            <div class="col-sm-12 separador">
                <hr>
            </div>
            <div class="col-sm-6">
              <div class="dato_cliente">
                <label for="usuario">
                  Nombre de Usuario:
                  <input type="text" name="usuario" value="" onchange="javascript:Disponible();">
                </label>
              </div>
              <div class="dato_cliente">
                <label for="clave">
                  Contraseña:
                  <input type="password" name="clave" value="">
                </label>
              </div>
              <div class="dato_cliente">
                <label for="confirmacion">
                  Repetir Contraseña:
                  <input type="password" name="confirmacion" value="">
                </label>
              </div>
            </div>
          </div>
          <div class="botones_registro" style="margin-top: 1em;">
            <center><input type="button" name="" value="Registrar" class="boton" onclick="javascript:Registrar();">
            <input type="button" name="" value="Cancelar" class="boton" onclick="javascript:Cancelar();"></center>
          </div>
        </div>
      </div>
      <?php include_once("footer.php"); ?>
    </form>
  </body>
</html>
