<?php

function multimedia($id,$conn){
    $query = "INSERT INTO app_productos_multimedia (producto_id,imagen,ficha) VALUES (?,1,0) ON DUPLICATE KEY UPDATE imagen= 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    $success = false;
    if ($stmt->execute()) {
        $success = $stmt->affected_rows > 0;
    }

    $stmt->close();
    return $success;
}

function cambio_doc($id,$descripcion,$descuento,$ficha,$conn){
    $tiene_ficha = $ficha ==="true" ? 1 : 0;
    
    $query1 = "INSERT INTO app_productos_multimedia (producto_id,imagen,ficha) VALUES (?,0,?) ON DUPLICATE KEY UPDATE ficha= ?";
    $stmt1 = $conn->prepare($query1);
    $stmt1->bind_param("iii", $id,$tiene_ficha,$tiene_ficha);
    
    $query2 ="UPDATE productos SET especificaciones=?, descuento=? WHERE id=?";
    $stmt2 = $conn->prepare($query2);
    $stmt2->bind_param("ssi", $descripcion,$descuento,$id);
    
    $success = false;
    if ($stmt1->execute() && $stmt2->execute()) {
        $success = $stmt1->affected_rows > 0 || $stmt2->affected_rows > 0;
    }

    $stmt1->close();
    $stmt2->close();
    return $success;
    
    
}
