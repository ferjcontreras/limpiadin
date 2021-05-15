<?php
    include_once("../etc/opendb.php");
    //include_once("../etc/register_globals.php");
    $provincia = $_POST['provincia'];

    $sqlqry = "SELECT Minimo.ID, Provincia.nombre , Depto.nombre, Minimo.Monto FROM Provincia, Depto, Minimo WHERE Depto.idProv = Provincia.ID AND Depto.ID = Minimo.IDDepartamento ";
    if ($provincia != 0) {
      $sqlqry .= " AND Provincia.ID = '$provincia' ";
    }
    $sqlqry .= "ORDER BY Provincia.nombre , Depto.nombre;";
    $DBres = mysqli_query($db, $sqlqry);
    if (mysqli_errno($db)) {
      echo "error";
    }
    $MaxItems = 0;
    while($DBarr = mysqli_fetch_row($DBres)) {
      $IDMinimo[$MaxItems] = $DBarr[0];
      $NombreProvincia[$MaxItems] = $DBarr[1];
      $NombreDepto[$MaxItems] = $DBarr[2];
      $Monto[$MaxItems] = $DBarr[3];
      $MaxItems++;
    }



    for ($i = 0; $i < $MaxItems; $i++) {
?>
      <div class="item_ingreso" >
        <div class="row">
          <div class="col-sm-4">
            <center><p style="margin:1em;width:100%;"><?php echo $NombreProvincia[$i] ?></p></center>
          </div>
          <div class="col-sm-4">
            <center><p  style="margin:1em;width:100%;"><?php echo $NombreDepto[$i] ?></p></center>
          </div>
          <div class="col-sm-4">
            <center><input type="number" name="minimo<?php echo $IDMinimo[$i] ?>" value="<?php echo $Monto[$i] ?>" style="margin:1em;"></center>
          </div>
        </div>
      </div>
<?php
    }
?>
