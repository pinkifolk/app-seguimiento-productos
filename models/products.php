<?php
require_once "../database/conn.php";
require_once "../middleware/cambiar_id.php";


function conn (){
    return Database::obtenerInstancia()->obtenerConexion();
}


function index($search_query){
    $query = "SELECT P.id, IFNULL(P.cod_unificado,'No codificado')cod_unificado, P.especificaciones, M.descripcion marca, C.descripcion categoria, IFNULL(S.stock,0) stock, P.consignado, APM.imagen, APM.ficha, P.descuento
            FROM productos P 
            LEFT JOIN marcas M ON M.id=P.marca_id 
            LEFT JOIN categorias C ON C.id=P.categoria_id 
            LEFT JOIN stock S ON S.producto_id=P.id 
            LEFT JOIN app_productos_multimedia APM ON APM.producto_id=P.id
            WHERE P.outlet=1 AND P.origen='P'";
    
    if (!empty($search_query)) {
        $query .= " AND (P.cod_unificado LIKE ? OR M.descripcion LIKE ? OR P.id LIKE ? OR P.especificaciones LIKE ?)";
    }
    
    $query .= " ORDER BY P.cod_unificado LIMIT 20";
    
    $stmt = conn()->prepare($query);
    
    if (!empty($search_query)) {
        $param = '%'. $search_query . '%';
        $stmt->bind_param("ssss", $param, $param, $param, $param);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $row["id_registro"] = encriptarId($row["id"]);
        $products[] = $row;
    }
    $stmt->close();
    return $products;
}
function multimedia($id){
    $query = "INSERT INTO app_productos_multimedia (producto_id,imagen,ficha) VALUES (?,1,0) ON DUPLICATE KEY UPDATE imagen= 1";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("i", $id);
    
    $success = false;
    if ($stmt->execute()) {
        $success = $stmt->affected_rows > 0;
    }

    $stmt->close();
    return $success;
}
function documentation($id,$descripcion,$descuento,$ficha){
    $tiene_ficha = $ficha ==="true" ? 1 : 0;
    
    $query1 = "INSERT INTO app_productos_multimedia (producto_id,imagen,ficha) VALUES (?,0,?) ON DUPLICATE KEY UPDATE ficha= ?";
    $stmt1 = conn()->prepare($query1);
    $stmt1->bind_param("iii", $id,$tiene_ficha,$tiene_ficha);
    
    $query2 ="UPDATE productos SET especificaciones=?, descuento=? WHERE id=?";
    $stmt2 = conn()->prepare($query2);
    $stmt2->bind_param("sss`", $descripcion,$descuento,$id);
    
    $success = false;
    if ($stmt1->execute() && $stmt2->execute()) {
        $success = $stmt1->affected_rows > 0 || $stmt2->affected_rows > 0;
    }

    $stmt1->close();
    $stmt2->close();
    return $success;
    
    
}