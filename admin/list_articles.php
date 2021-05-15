<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

    if (!isset($_SESSION['UserIDAdmin'])) {
      include_once("login.php");
    }

    // Hacemos la consulta de las categorias para poder colocarlo en los select mas abajo
    $sqlqry = "SELECT ID, Nombre FROM Categoria ORDER BY Nombre;";
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "Error: $sqlqry";
    }
    $MaxCategorias = 0;
    while($DBarr = mysqli_fetch_row($DBres)) {
      $IDCat[$MaxCategorias] = $DBarr[0];
      $NomCat[$MaxCategorias] = $DBarr[1];
      $MaxCategorias++;
    }


    //echo "Porcentaje: $porcentaje_aumento";

    if ($Flag == 1) {
      $sqlqry = "SELECT Producto.ID, Producto.Foto, Producto.Nombre FROM Producto WHERE Producto.ID != 0 ";
      if ($fnombre != "") $sqlqry .= " AND Producto.Nombre LIKE \"%$fnombre%\" ";
      if ($fcategoria != 0) $sqlqry .= " AND Producto.IDCategoria = '$fcategoria' ";
      $sqlqry .= " ORDER BY Producto.Nombre";
      //echo $sqlqry."<br>";
      $DBres = mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo "Error: $sqlqry";
      }
      //$nn = 1;
      while ($DBarr = mysqli_fetch_row($DBres)) {
        $IDProducto = $DBarr[0];
        $FotoProducto = $DBarr[1];
        $NombreProducto = $DBarr[2];


        //echo "$nn - IDPRO:  $IDProducto - $NombreProducto<br>";
        //$nn++;
        if (${'nombre'.$IDProducto} != "") { // Me aseguro de que al menos el nombre no se encuentre vacío para no generar registros vacíos.
          ${'nombre'.$IDProducto}  = str_replace('"', "'", ${'nombre'.$IDProducto} );
          $sqlqry = "UPDATE Producto SET Nombre = \"".${'nombre'.$IDProducto}."\", Detalle = '".${'detalle'.$IDProducto}."', PrecioCosto = ${'precio_costo'.$IDProducto}, PorcentajeGanancia = ${'porcentajeg'.$IDProducto}, IVA = ${'iva'.$IDProducto}, Precio = '".${'precio'.$IDProducto}."'";
          if ($porcentaje_aumento != 0) {
            $mult = $porcentaje_aumento / 100;
            $sqlqry .= " + ${'precio'.$IDProducto}*$mult ";
          }
          $sqlqry .=", IDCategoria = '".${'categoria'.$IDProducto}."' ";
          $archivo = $_FILES['foto'.$IDProducto]['name'];
          //echo "Archivo: $archivo";
          if ($archivo != "") {
              $extension = substr(basename($archivo), strpos(basename($archivo), ".") , strlen(basename($archivo)));
              $subido = "../pictures/productos/foto_".$IDProducto."_".date('Y_m_d_H_m_s')."$extension";

              if (move_uploaded_file($_FILES['foto'.$IDProducto]['tmp_name'], $subido)) {
                $sqlqry .= " , Foto = '".basename($subido)."' ";
                // Si todo está OK, voy a borrar el archivo anterior para no generar tanto espacio
                $ArchivoViejo = "../pictures/productos/$FotoProducto";
                unlink($ArchivoViejo);
              } else {
                echo "Error al subir archivo\n";
              }
          }
          if (${'disponible'.$IDProducto} == 'on') $sqlqry .= " , Disponible = 1 ";
          else $sqlqry .= " , Disponible = 0 ";
          $sqlqry .= " WHERE ID = '$IDProducto' ";
          //echo $sqlqry." - $porcentaje_aumento<br>";
          mysqli_query($db, $sqlqry);
          if (mysqli_errno($db)) {
            echo "Error: $sqlqry<br>";
          }
        }
      }
    }
    else if ($Flag == 3) {
      $error = 0;
      $archivo = $_FILES['new_foto'.$IDProducto]['name'];
      //echo "Archivo: $archivo";
      if ($archivo != "") {
          $extension = substr(basename($archivo), strpos(basename($archivo), ".") , strlen(basename($archivo)));
          $subido = "../pictures/productos/foto_new_".date('Y_m_d_H_m_s')."$extension";

          if (move_uploaded_file($_FILES['new_foto'.$IDProducto]['tmp_name'], $subido)) {
            $sqlqry = "INSERT INTO Producto(Nombre, Detalle, IDCategoria, Precio, Foto, Disponible, PrecioCosto, PorcentajeGanancia, IVA) VALUES ('$new_nombre', '$new_detalle', '$new_categoria', '$new_precio', '".basename($subido)."', 1, $new_costo, $new_porcentajeg, $new_iva);";
          } else {
            echo "Error al subir archivo\n";
            $sqlqry = "INSERT INTO Producto(Nombre, Detalle, IDCategoria, Precio, Disponible, PrecioCosto, PorcentajeGanancia, IVA) VALUES ('$new_nombre', '$new_detalle', '$new_categoria', '$new_precio', 1, $new_costo, $new_porcentajeg, $new_iva);";
          }
      } else $sqlqry = "INSERT INTO Producto(Nombre, Detalle, IDCategoria, Precio, Disponible, PrecioCosto, PorcentajeGanancia, IVA) VALUES ('$new_nombre', '$new_detalle', '$new_categoria', '$new_precio', 1, $new_costo, $new_porcentajeg, $new_iva);";
      // Ejecutamos la consulta
      //echo $sqlqry;
      mysqli_query($db, $sqlqry);
      if (mysqli_errno($db)) {
        echo mysqli_error($db);
        $error = 1;
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
        function Guardar() {
          sessionStorage.setItem("coords", JSON.stringify({
            y: window.scrollY,
            x: window.scrollX
          }));
          document.forms['Article'].Flag.value = 1;
          document.forms['Article'].submit();
        }
        function Regresar() {
          document.forms['Article'].action = 'index.php';
          document.forms['Article'].Flag.value = 0;
          document.forms['Article'].submit();
        }
        function Agregar() {
          document.forms['Article'].Flag.value = 2;
          document.forms['Article'].submit();
        }
        function SetFlagZero() {
          document.forms['Article'].Flag.value = 0;
          document.forms['Article'].submit();
        }
        function Nuevo() {
          document.forms['Article'].Flag.value = 3;
          document.getElementById("botonGuardar").enabled = false;
          document.forms['Article'].submit();
        }
        function SetPosition() {
          sessionStorage.setItem("coords", JSON.stringify({
            y: window.scrollY,
            x: window.scrollX
          }));
        }
        function Filtrar() {
          document.forms['Article'].fcategoria.value = document.forms['Article'].bcategoria.options[document.forms['Article'].bcategoria.selectedIndex].value;
          document.forms['Article'].fnombre.value = document.forms['Article'].bnombre.value;
          document.forms['Article'].submit();
        }
        function CalcularNuevoPrecio() {
          var pganancia = parseFloat(document.forms['Article'].new_porcentajeg.value);
          var costo = parseFloat(document.forms['Article'].new_costo.value);
          var idiva = document.forms['Article'].new_iva.options[document.forms['Article'].new_iva.selectedIndex].value;
          var valoriva = 0;
          if (pganancia != 0 && costo != 0){
            if (idiva == 1) valoriva = 0.105;
            else if (idiva == 2) valoriva = 0.21;
            document.forms['Article'].new_precio.value = costo + costo * pganancia / 100 + costo * valoriva;
          }
          //alert(pganancia+" - "+costo+" - "+idiva);
        }
        function CalcularPrecio(id) {
          var pganancia = eval("parseFloat(document.forms['Article'].porcentajeg"+id+".value)");
          var costo = eval("parseFloat(document.forms['Article'].precio_costo"+id+".value)");
          var idiva = eval("document.forms['Article'].iva"+id+".options[document.forms['Article'].iva"+id+".selectedIndex].value");
          var valoriva = 0;
          if (pganancia != 0 && costo != 0){
            if (idiva == 1) valoriva = 0.105;
            else if (idiva == 2) valoriva = 0.21;
            eval("document.forms['Article'].precio"+id+".value = costo + costo * pganancia / 100 + costo * valoriva;");
          }
          //alert(pganancia+" - "+costo+" - "+idiva);
        }
    </script>
  </head>
  <body <?php if ($Flag == 1) echo "onload='javascript:SetPosition();'" ?>>
    <form name="Article" action="list_articles.php" method="post" enctype="multipart/form-data">
      <input type="hidden" name="Flag" value="">
      <input type="hidden" name="fcategoria" value="<?php echo $fcategoria ?>">
      <input type="hidden" name="fnombre" value="<?php echo $fnombre ?>">
      <?php include_once("header_admin.php"); ?>
      <div class="cuerpo">
        <div class="container">
          <?php if ($Flag == 1 || $Flag == 0 || empty($Flag)) { ?>
                <p id="titulo">Listado de Artículos</p>
                <div class="filtro" style="margin-bottom:1em;">
                  <div class="row">
                    <div class="col-sm-4 offset-sm-1">
                      <p>Filtrar por Categoría:</p>
                    </div>
                    <div class="col-sm-4">
                      <select class="" name="bcategoria" onchange="javascript:Filtrar();" style="width:100%;">
                        <option value="0">-- Seleccionar Categoría --</option>
                        <?php
                          $sqlqry = "SELECT ID, Nombre FROM Categoria ORDER BY Nombre ";
                          $DBres = mysqli_query($db, $sqlqry);
                          if (mysqli_errno($db)) {
                            echo "Error: $sqlqry";
                          }
                          while($DBarr = mysqli_fetch_row($DBres)) {
                            echo "<option value='".$DBarr[0]."' ";
                            if ($DBarr[0] == $fcategoria) echo "selected";
                            echo ">".$DBarr[1]."\n";
                          }
                        ?>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-sm-4 offset-sm-1">
                      <p>Filtrar por Nombre:</p>
                    </div>
                    <div class="col-sm-4">
                      <input type="text" name="bnombre" value="<?php echo $fnombre ?>" onchange="javascript:Filtrar()" style="width:100%;">
                    </div>
                  </div>
          <?php } ?>
          </div>
          <?php
            if ($Flag == 1 || $Flag == 0 || empty($Flag)){
                  $sqlqry = "SELECT Producto.ID, Producto.Nombre, Producto.Detalle, Producto.Precio, Producto.Foto, Producto.IDCategoria, Producto.Disponible, Categoria.Nombre, Producto.PorcentajeGanancia, Producto.PrecioCosto, Producto.IVA, IVA.Valor FROM Producto LEFT JOIN Categoria ON Producto.IDCategoria = Categoria.ID LEFT JOIN IVA ON Producto.IVA = IVA.ID WHERE Producto.ID != 0  ";
                  if ($fnombre != "") $sqlqry .= " AND Producto.Nombre LIKE \"%$fnombre%\" ";
                  if ($fcategoria != 0) $sqlqry .= " AND Producto.IDCategoria = '$fcategoria' ";
                  $sqlqry .= " ORDER BY Producto.Nombre";
                  $DBres = mysqli_query($db, $sqlqry);
                  if (mysqli_errno($db)) {
                    echo "Error: $sqlqry";
                  }
                  while ($DBarr = mysqli_fetch_row($DBres)) {
                    $IDProducto = $DBarr[0];
                    $NombreProducto = $DBarr[1];
                    $DetalleProducto = $DBarr[2];
                    $PrecioProducto = $DBarr[3];
                    $FotoProducto = $DBarr[4];
                    $IDCategoria = $DBarr[5];
                    $Disponible = $DBarr[6];
                    $NombreCategoria = $DBarr[7];
                    $PorcentajeG = $DBarr[8];
                    $PrecioCosto = $DBarr[9];
                    $IDIVA = $DBarr[10];
                    $ValorIVA = $DBarr[11];
                ?>
                    <div class="item_articulo">
                      <div class="row">
                        <div class="col-sm-4">

                          <div class="imagen_articulo">
                            <?php if ($FotoProducto != ""){ ?>
                              <center><img src="pictures/productos/<?php echo $FotoProducto ?>" alt=""></center>
                            <?php } else { ?>
                              <center><img src="images/no_disponible.png" alt=""></center>
                            <?php } ?>

                          </div>
                        </div>
                        <div class="col-sm-8">
                          <div class="detalle_articulo">
                            <div class="renglon" style="justify-content: left;">
                                <input type="checkbox" name="disponible<?php echo $IDProducto; ?>" value="on" <?php if ($Disponible == 1) echo "checked" ?>>&nbsp; Mostrar en Catálogo
                            </div>
                            <input type="text" name="nombre<?php echo $IDProducto; ?>" value="<?php echo $NombreProducto ?>" style="width:100%;">
                            <select class="entrada" name="categoria<?php echo $IDProducto; ?>">
                              <option value="0">-- Seleccionar Categoría --</option>
                              <?php
                                for ($i=0; $i<$MaxCategorias; $i++) {
                                  echo "<option value='".$IDCat[$i]."'";
                                  if ($IDCat[$i] == $IDCategoria) echo " selected ";
                                  echo "   > ".$NomCat[$i]."\n";
                                }
                              ?>
                            </select>
                            <textarea name="detalle<?php echo $IDProducto; ?>" rows="4" cols="80"><?php echo $DetalleProducto ?></textarea>
                            <div class="renglon">
                              Costo $ <input  onchange="Javascript:CalcularPrecio(<?php echo $IDProducto ?>);" style="width:20%" type="number" name="precio_costo<?php echo $IDProducto; ?>" value="<?php echo $PrecioCosto ?>" class="entrada">
                              % de Ganancia <input onchange="Javascript:CalcularPrecio(<?php echo $IDProducto ?>);" style="width:20%" type="number" min="0" max="100" name="porcentajeg<?php echo $IDProducto; ?>" value="<?php if ($PorcentajeG != "") echo $PorcentajeG; else echo "0" ?>" class="entrada">
                            </div>
                            <div class="renglon">
                              IVA <select class="entrada" name="iva<?php echo $IDProducto; ?>" onchange="Javascript:CalcularPrecio(<?php echo $IDProducto ?>);">
                                <option value="0">-- Seleccione IVA --</option>
                                <option value="1" <?php if ($IDIVA == 1) echo "selected" ?>>10.5 %</option>
                                <option value="2" <?php if ($IDIVA == 2) echo "selected" ?>>21 %</option>
                              </select>
                              Efectivo $ <input style="width:20%" type="number" name="precio<?php echo $IDProducto; ?>" value="<?php echo $PrecioProducto ?>" class="entrada">
                            </div>
                            <input type="file" name="foto<?php echo $IDProducto; ?>" value="" style="margin-top:0.5em">
                          </div>
                        </div>
                      </div>
                    </div>
          <?php
                  }
          } else if($Flag == 2) {
          ?>
              <div class="item_nuevo row">
                  <div class="col-sm-12">
                    <p id="titulo">Ingresar Producto</p>
                  </div>
                  <div class="col-sm-6 caja_entrada">
                    <p>Nombre:</p><input type="text" name="new_nombre" value="" placeholder="Nombre">
                  </div>
                  <div class="col-sm-6 caja_entrada">
                    <p>Categoría:</p>
                    <select class="" name="new_categoria">
                      <option value="0">-- Seleccione Categoría --</option>
                      <?php
                        for ($i=0; $i<$MaxCategorias; $i++) {
                          echo "<option value='".$IDCat[$i]."'";
                          if ($IDCat[$i] == $IDCategoria) echo " selected ";
                          echo "   > ".$NomCat[$i]."\n";
                        }
                      ?>
                    </select>
                  </div>

                  <div class="col-sm-12 caja_entrada">
                    <p>Detalle:</p>
                    <textarea style="width:100%;" name="new_detalle" rows="3" cols="80" placeholder="Detalle del Producto"></textarea>
                  </div>

                  <div class="col-sm-6 caja_entrada">
                    <p>Costo:</p>
                    <input type="number" name="new_costo" value="0" placeholder="Costo" onchange="Javascript:CalcularNuevoPrecio();">
                  </div>
                  <div class="col-sm-6 caja_entrada">
                    <p>% Ganancia:</p>
                    <input type="number" name="new_porcentajeg" value="0" placeholder="% Ganancia"  onchange="Javascript:CalcularNuevoPrecio();">
                  </div>
                  <div class="col-sm-6 caja_entrada">
                    <p>IVA:</p>
                    <select class="" name="new_iva"  onchange="Javascript:CalcularNuevoPrecio();">
                      <option value="0">-- Seleccionar IVA --</option>
                      <option value="1">10.5 %</option>
                      <option value="2">21 %</option>
                    </select>
                  </div>
                  <div class="col-sm-6 caja_entrada">
                    <p>Efectivo:</p>
                    <input type="number" name="new_precio" value="0" placeholder="Precio">
                  </div>
                  <div class="col-sm-12 caja_entrada">
                    <p>Foto:</p>
                    <input type="file" name="new_foto" value="" placeholder="Cargar Foto">
                  </div>
                  <div class="col-sm-12 caja_botones">
                    <input type="button" name="" value="Guardar" class="boton_verde" onclick="javascript:Nuevo();" id="botonGuardar">
                    <input type="button" name="" value="Regresar" class="boton_rojo" onclick="javascript:SetFlagZero();">
                  </div>
              </div>

          <?php
          }
          else if ($Flag == 3){
            if ($error == 1) echo "<p style='color:red;font-size:2em;'>Se produjo un error al registrar el nuevo producto</p>";
            else echo "<p style='color:green;font-size:1.5em;'>¡Producto almacenado!</p>";
          ?>

          <input style="margin-top:1em;" type="button" name="" value="Regresar" class="boton_azul" onclick="javascript:SetFlagZero();" id="botonGuardar">

          <?php
          }
          ?>
        </div>

        <?php if ($Flag == 0 || empty($Flag) || $Flag == 1) { ?>

        <div class="botones_flotantes" style="background: white;">
          Aumentar <input type="number" name="porcentaje_aumento" max="100" min="0" value="0"> %
          <input type="button" name="" value="Guardar" class="boton_verde" onclick="javascript:Guardar()">
          <input type="button" name="" value="Agregar" class="boton_azul" onclick="javascript:Agregar()">
          <input type="button" name="" value="Regresar" class="boton_rojo" onclick="javascript:Regresar()">
        </div>
        <?php } ?>
        <?php include_once("../footer.php"); ?>
      </div>
    </form>
  </body>
</html>
