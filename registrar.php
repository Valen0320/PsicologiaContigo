<?php
// registrar.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

header('Content-Type: application/json');

function registrarUsuarioReal($datos) {
    try {
        $conn = conectarDB();
        
        // Debug
        error_log("Datos recibidos: " . print_r($datos, true));
        
        // Validar que los campos requeridos no estén vacíos
        if (empty($datos['nombre']) || empty($datos['documento']) || 
            empty($datos['email']) || empty($datos['contraseñaReal'])) {
            return ["success" => false, "message" => "Todos los campos son requeridos"];
        }
        
        // Sanitizar datos
        $nombre = $conn->real_escape_string($datos['nombre']);
        $documento = $conn->real_escape_string($datos['documento']);
        $email = $conn->real_escape_string($datos['email']);
        $telefono = $conn->real_escape_string($datos['telefono']);
        $fecha_nacimiento = $conn->real_escape_string($datos['fechaNacimiento']);
        $contraseña = password_hash($datos['contraseñaReal'], PASSWORD_DEFAULT);
        
        // Debug
        error_log("Datos sanitizados listos para insertar");
        
        $sql = "INSERT INTO usuarios_reales (nombre, documento, email, telefono, fecha_nacimiento, contraseña) 
                VALUES (?, ?, ?, ?, ?, ?)";
                
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        
        $stmt->bind_param("ssssss", $nombre, $documento, $email, $telefono, $fecha_nacimiento, $contraseña);
        
        $resultado = $stmt->execute();
        
        if ($resultado) {
            return ["success" => true, "message" => "Usuario registrado exitosamente"];
        } else {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log("Error en registrarUsuarioReal: " . $e->getMessage());
        return ["success" => false, "message" => "Error: " . $e->getMessage()];
    }
}

// Similar actualización para registrarUsuarioAvatar...
function registrarUsuarioAvatar($datos) {
    try {
        $conn = conectarDB();
        
        // Debug
        error_log("Datos de avatar recibidos: " . print_r($datos, true));
        
        if (empty($datos['avatar']) || empty($datos['contraseñaAvatar'])) {
            return ["success" => false, "message" => "Todos los campos son requeridos"];
        }
        
        $avatar = $conn->real_escape_string($datos['avatar']);
        $contraseña = password_hash($datos['contraseñaAvatar'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO usuarios_avatar (avatar_seleccionado, contraseña) VALUES (?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn->error);
        }
        
        $stmt->bind_param("ss", $avatar, $contraseña);
        
        $resultado = $stmt->execute();
        
        if ($resultado) {
            return ["success" => true, "message" => "Avatar registrado exitosamente"];
        } else {
            throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
        }
    } catch (Exception $e) {
        error_log("Error en registrarUsuarioAvatar: " . $e->getMessage());
        return ["success" => false, "message" => "Error: " . $e->getMessage()];
    }
}

// Procesar la solicitud
try {
    $rawData = file_get_contents('php://input');
    error_log("Datos raw recibidos: " . $rawData);
    
    $datos = json_decode($rawData, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
    }
    
    if (!isset($datos['tipoRegistro'])) {
        throw new Exception("Tipo de registro no especificado");
    }
    
    if ($datos['tipoRegistro'] === 'real') {
        echo json_encode(registrarUsuarioReal($datos));
    } else if ($datos['tipoRegistro'] === 'avatar') {
        echo json_encode(registrarUsuarioAvatar($datos));
    } else {
        throw new Exception("Tipo de registro no válido");
    }
} catch (Exception $e) {
    error_log("Error en el procesamiento principal: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
?>