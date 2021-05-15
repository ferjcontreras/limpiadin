<?php

  require_once 'composer/vendor/autoload.php';
  MercadoPago\SDK::setAccessToken("TEST-6337705085259200-092700-be1f38f8c6dc1098e0cdaab075938f7a-86776160");

  $payment_methods = MercadoPago::get("/v1/payment_methods");

?>
