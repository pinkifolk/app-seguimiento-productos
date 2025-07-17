<?php
session_start();
header('Content-Type: application/json');
require_once "../database/conn.php";

function conn (){
    return Database::obtenerInstancia()->obtenerConexion();
}

$usuario = $_POST['usuario'];
$clave = $_POST['password'];

// VALIDAR LOS USUARIOS QUE SI TIENEN ACCESO
$usuario_auth =[
    'sebastian.solis',
    'mauricio',
    'admin',
    'lfarias',
    'alvaro.reyes',
    ];


// VALDIDAR CREDENCIALES

function buscar_usuario($conn,$user){
    $query= conn()->prepare("SELECT id,nombre FROM usuarios WHERE clave=? OR email = ?");
    $query->bind_param("ss", $user,$user);
    $query->execute();
    $res = $query->get_result();
    
    if($res->num_rows === 1){
        
        return [
            'success' => true , 
            'data' => $res->fetch_assoc()
            ];
    }else{
        return [
            'success' => false , 
            'data' => null
            ];
    }
    
}

function validar_clave($conn,$user, $pass){
    $query= conn()->prepare("SELECT * FROM usuarios WHERE clave=? AND password = PASSWORD(?)");
    $query->bind_param("ss", $user,$pass);
    $query->execute();
    $res = $query->get_result();
    
    return  $res->num_rows === 1 ? true : false;
}


$result_user = buscar_usuario($conn,$usuario);
$result_pass = validar_clave($conn,$usuario,$clave);
$validado = !in_array($usuario, $usuario_auth);

if(!$result_user['success']){
    echo json_encode(['success' => false, 'tipo'=>'user', 'mensaje' => 'Usuario no encontrado']);
    exit;
}
if(!$result_pass){
    echo json_encode(['success' => false, 'tipo'=>'pass', 'mensaje' => 'Contraseña incorrecta']);
    exit;
    }
if($validado){
    echo json_encode(['success' => false, 'tipo'=>'user', 'mensaje' => 'Usuario no autorizado']) ;
    exit;
}

$_SESSION['id'] = $result_user['data']['id'];
$_SESSION['nombre'] = $result_user['data']['nombre'];

echo json_encode(['success' => true, 'redirect'=>'views/dashboard.php']);
exit;

