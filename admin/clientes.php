<?php
  include_once("../etc/opendb.php");
  include_once("../etc/register_globals.php");

  session_start();

    if (!isset($_SESSION['UserIDAdmin'])) {
      include_once("login.php");
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
        document.forms['Article'].action = 'index.php';
        document.forms['Article'].submit();
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
          <div class="row">
            <div class="col-sm-12">
              <img src="images/construccion.jpeg" alt="">
            </div>
          </div>
        </div>
        <?php include_once("../footer.php"); ?>
      </div>
    </form>
  </body>
</html>
