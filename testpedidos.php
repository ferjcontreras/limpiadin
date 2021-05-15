<?php
  session_start();
  $arreglopedidos[] = array(
    'id_comprobante' => 3,
    'tipo' => 'p'
  );
  $arreglopedidos[] = array(
    'id_comprobante' => 3,
    'tipo' => 'p'
  );
  $arreglopedidos[] = array(
    'id_comprobante' => 3,
    'tipo' => 'p'
  );
  $arreglopedidos[] = array(
    'id_comprobante' => 3,
    'tipo' => 'p'
  );
  $arreglopedidos[] = array(
    'id_comprobante' => 22,
    'tipo' => 'c'
  );
  $_SESSION['pedidos'] = $arreglopedidos;
?>
