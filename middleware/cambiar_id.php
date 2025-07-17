<?php

define('CLAVE_SECRETA', 'tuclavebiensecreta');
define('IV', '1234567890123456'); 

function encriptarId($id) {
    $encriptado = openssl_encrypt($id, 'aes-256-cbc', CLAVE_SECRETA, OPENSSL_RAW_DATA, IV);
    return rtrim(strtr(base64_encode($encriptado), '+/', '-_'), '=');
}

function desencriptarId($idEncriptado) {
    $encriptado = base64_decode(strtr($idEncriptado, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($idEncriptado)) % 4));
    return openssl_decrypt($encriptado, 'aes-256-cbc', CLAVE_SECRETA, OPENSSL_RAW_DATA, IV);
}
