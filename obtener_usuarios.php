<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $conn = conectarDB();
    $sql = "
        SELECT id, avatar_seleccionado, fecha_registro 
        FROM usuarios_avatar";

    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $usuarios = [];
    while ($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }

    echo json_encode($usuarios);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
