<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
    $conn = conectarDB();
    $sql = "
        SELECT id, nombre_paciente, nombre_profesional, fecha_cita, hora_cita
        FROM citas";

    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception("Error en la consulta: " . $conn->error);
    }

    $citas = [];
    while ($row = $result->fetch_assoc()) {
        $citas[] = $row;
    }

    echo json_encode($citas);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
