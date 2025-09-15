<?php
require_once "../database/conn.php";
require_once "../middleware/cambiar_id.php";


function conn (){
    return Database::obtenerInstancia()->obtenerConexion();
}

//SQL_CALC_FOUND_ROWS esta deprecado en la version 8

function index($search_query,$page,$limit){
    $offset = ($page - 1) * $limit;
    $query = "SELECT SQL_CALC_FOUND_ROWS 
                P.id, 
                IFNULL(P.cod_unificado,'No codificado') cod_unificado, 
                P.especificaciones, 
                M.descripcion marca, 
                C.descripcion categoria, 
                IFNULL(S.stock,0) stock, 
                P.consignado, 
                APM.imagen, 
                APM.ficha, 
                APM.descripcion,
                APM.especificacion,
                APM.precio,
                APM.descuento,
                APM.prod_equivalente
            FROM productos P 
            LEFT JOIN marcas M ON M.id=P.marca_id 
            LEFT JOIN categorias C ON C.id=P.categoria_id 
            LEFT JOIN stock S ON S.producto_id=P.id 
            LEFT JOIN app_productos_multimedia APM ON APM.producto_id=P.id
            WHERE P.seguimiento=1 AND P.origen='P'";
    
    if (!empty($search_query)) {
        $query .= " AND (P.cod_unificado LIKE ? OR M.descripcion LIKE ? OR P.id LIKE ? OR P.especificaciones LIKE ?)";
    }
    
    $query .= " ORDER BY P.cod_unificado LIMIT ? OFFSET ?";
    
    $stmt = conn()->prepare($query);
    
    if (!empty($search_query)) {
        $param = '%'. $search_query . '%';
        $stmt->bind_param("ssssii", $param, $param, $param, $param, $limit, $offset);
    } else {
        $stmt->bind_param("ii", $limit, $offset);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $row["id_registro"] = encriptarId($row["id"]);
        $products[] = $row;
    }
    $stmt->close();
    $totalResult = conn()->query("SELECT FOUND_ROWS() as total");
    $total = $totalResult->fetch_assoc()['total'];

    return [
        "products" => $products,
        "total" => $total,
        "page" => $page,
        "limit" => $limit
    ];
}
function multimedia($id,$estado){
    $query = "INSERT INTO app_productos_multimedia (producto_id,imagen,ficha,descripcion, especificacion, precio, descuento, prod_equivalente) VALUES (?,?,0,0,NULL,0,0,0) ON DUPLICATE KEY UPDATE imagen= ?";
    $stmt = conn()->prepare($query);
    $stmt->bind_param("iii", $id,$estado,$estado);
    
    $success = false;
    if ($stmt->execute()) {
        $success = $stmt->affected_rows > 0;
    }

    $stmt->close();
    return $success;
}
function documentation($id,$descuento,$ficha,$descripcion,$precio){
    $tiene_ficha = $ficha ==="true" ? 1 : 0;
    
    $query1 = "INSERT INTO app_productos_multimedia 
    (producto_id, imagen, ficha, descripcion, especificacion, precio, descuento, prod_equivalente) 
    VALUES (?, 0, ?, 1, ?, ?, ?, 0)
    ON DUPLICATE KEY UPDATE 
        ficha = ?, 
        descripcion = 1, 
        especificacion = ?, 
        precio = ?, 
        descuento = ?, 
        prod_equivalente = 0";

    $stmt1 = conn()->prepare($query1);
    
    $stmt1->bind_param(
        "sisssisss", 
        $id,            // producto_id
        $tiene_ficha,   // ficha
        $descripcion,// especificacion
        $precio,        // precio
        $descuento,     // descuento
        $tiene_ficha,   // ficha update
        $descripcion,// especificacion update
        $precio,        // precio update
        $descuento     // descuento update
    );
    
    $query2 ="UPDATE productos SET especificaciones=? WHERE id=?";
    $stmt2 = conn()->prepare($query2);
    $stmt2->bind_param("ss", $descripcion,$id);
    
    $success = false;
    
    if ($stmt1->execute() && $stmt2->execute()) {
        $success = $stmt1->affected_rows > 0 || $stmt2->affected_rows > 0;
    }
    $stmt1->close();
    $stmt2->close();

    return $success;
    
    
    
}