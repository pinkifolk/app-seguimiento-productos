<?php
require_once "../database/conn.php";

function conn (){
    return Database::obtenerInstancia()->obtenerConexion();
}
function index(){
    $result = conn()->query("SELECT COUNT(*) as total FROM productos WHERE outlet=1");
    $row = $result->fetch_assoc();
    
    $result1 = conn()->query("SELECT SUM(limpieza) limpieza, SUM(pintura) pintura, SUM(banco_pruebas) banco, ( SELECT COUNT(*) FROM app_productos_multimedia WHERE imagen = 1 ) AS fotografia FROM app_servicios_prod_det SPD LEFT JOIN app_productos_multimedia PM ON PM.producto_id=SPD.producto_id AND PM.imagen=1 WHERE SPD.estado=0
    ");
    $rows = $result1->fetch_assoc();
    //CONTAR DE LA TABLA app_servicios_prod_det LOS PRODUCTOS QUE ESTAN EN LIMPIEZA PERO AUN NO ESTEN EN ESTADO 1, LO MISMO PARA PINTURA Y BANCO DE PRUEBA. PARA LA FOTO GRAFIA HAY QUE CONTAR DE LA TABLA app_productos_multimedia LOS ID QUE ESTAN CON 1 EN EL CAMPO IMAGEN
    // CONTAR DE LA TABLA app_servicios_prod_det LOS PRODUCTOS QUE ESTAN EN ESTADO 1
    
    $result2 = conn()->query("SELECT SUM(CASE WHEN estado = 0 THEN 1 ELSE 0 END) preparacion, SUM(CASE WHEN estado = 1 THEN 1 ELSE 0 END) listos FROM app_servicios_prod_det
");
    $rows2 = $result2->fetch_assoc();
    
    $valores_quilicura = [100,0];
    $productos_prep= [ $rows["limpieza"], $rows["pintura"], $rows["fotografia"], $rows["banco"]];
    $listos_venta= [$rows2["preparacion"], $rows2["listos"]];
    
    $response = [
         "traslado" => $valores_quilicura,
         "preparacion" =>  $productos_prep,
         "listos" => $listos_venta,
        ];
    
    return $response;
}