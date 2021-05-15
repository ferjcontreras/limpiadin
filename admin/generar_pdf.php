<?php

    include_once("../etc/register_globals.php");

    session_start();

    $arreglopedidos[] = array(
      'id_comprobante' => $id_pedido,
      'tipo' => $tipo
    );
    $_SESSION['pedidos'] = $arreglopedidos;


    //Incluimos la librería
    //require_once 'html2pdf_v4.03/html2pdf.class.php';
    require_once 'HTML2PDF/vendor/autoload.php';

    //Recogemos el contenido de la vista
    ob_start();
    require_once 'listado.php';
    $html=ob_get_clean();

    //Pasamos esa vista a PDF

    //Le indicamos el tipo de hoja y la codificación de caracteres
    $mipdf=new \Spipu\Html2Pdf\Html2Pdf('P','A4','es','true','UTF-8');

    //Escribimos el contenido en el PDF
    $mipdf->writeHTML($html);

    //Generamos el PDF
    $mipdf->Output('PdfGeneradoPHP.pdf');


?>
