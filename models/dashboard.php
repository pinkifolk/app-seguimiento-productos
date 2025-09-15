<?php
require_once "../database/conn.php";

function conn (){
    return Database::obtenerInstancia()->obtenerConexion();
}
function index(){
    $q_outlet = conn()->query("SELECT COUNT(*) total,SUM(S.stock) unidades FROM productos P LEFT JOIN stock S ON S.producto_id=P.id WHERE P.seguimiento=1");
    $row_outlet = $q_outlet->fetch_assoc();
    
    $q_fotos = conn()->query("SELECT COUNT(APM.producto_id) AS total_imagen, ((SELECT COUNT(*) AS total_productos FROM productos P WHERE seguimiento = 1) - COUNT(APM.producto_id)) AS productos_pendientes FROM app_productos_multimedia APM WHERE APM.imagen = 1");
    $row_fotos = $q_fotos->fetch_assoc();
    $q_documentacion = conn()->query(" SELECT COUNT(APM.producto_id) AS total_docu, ((SELECT COUNT(*) AS total_productos FROM productos P WHERE seguimiento = 1) - COUNT(APM.producto_id)) AS productos_pendientes FROM app_productos_multimedia APM WHERE APM.ficha = 1 AND descripcion=1");
    $row_documentacion = $q_documentacion->fetch_assoc();
    
    $q_preparacion = conn()->query("SELECT SUM(CASE WHEN limpieza = 1 THEN 1 ELSE 0 END) total_limpieza, SUM(CASE WHEN limpieza = 1 THEN cantidad ELSE 0 END) sku_limpieza, SUM(CASE WHEN pintura = 1 THEN 1 ELSE 0 END) total_pintura, SUM(CASE WHEN pintura = 1 THEN cantidad ELSE 0 END) sku_pintura, SUM(CASE WHEN banco_pruebas = 1 THEN 1 ELSE 0 END) total_prueba, SUM(CASE WHEN banco_pruebas = 1 THEN cantidad ELSE 0 END) sku_prueba FROM app_servicios_prod_det");
    $row_preparacion = $q_preparacion->fetch_assoc();

    $q_listos_ventas = conn()->query("SELECT COUNT(*) total, SUM(cantidad) skus_total FROM app_servicios_prod_det WHERE estado=2");
    $rows_preparados = $q_listos_ventas->fetch_assoc();
    
    
    $venta = conn()->query("SELECT IFNULL(COUNT(*),0) total, IFNULL(SUM(cantidad),0) sku_total FROM app_servicios_prod_det ASP LEFT JOIN app_productos_multimedia APM ON APM.producto_id=ASP.producto_id WHERE estado=2 AND APM.imagen=1 AND APM.ficha=1 AND APM.descripcion=1");
    $row_venta= $venta->fetch_assoc();
    
    $outlet =[$row_outlet["total"],$row_outlet["unidades"]];
    $fotos = [
            $row_fotos["total_imagen"],$row_fotos["productos_pendientes"],
            ];
    $documentacion = [
            $row_documentacion["total_docu"],$row_documentacion["productos_pendientes"],
            ];
            
    $productos_prep= [ 
            [$row_preparacion["total_limpieza"],$row_preparacion["total_pintura"],$row_preparacion["total_prueba"]],
            [$row_preparacion["sku_limpieza"],$row_preparacion["sku_pintura"],$row_preparacion["sku_prueba"]]
        ];
    $preparados= [
            [$rows_preparados["total"],$rows_preparados["skus_total"]] 
        ];
    $venta = [
            $row_venta["total"],$row_venta["sku_total"],
        ];
    
    $response = [
         "outlet" => $outlet,
         "fotos" => $fotos,
         "preparacion" =>  $productos_prep,
         "preparados" => $preparados,
         "documentacion" =>$documentacion,
         "venta" =>$venta,
        ];
    
    return $response;
}