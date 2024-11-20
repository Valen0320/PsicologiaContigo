<?php
// config.php
define('DB_HOST', 'localhost');     // Normalmente es localhost
define('DB_USER', 'root');          // Tu usuario de MySQL (por defecto es root)
define('DB_PASS', '');              // Tu contraseña de MySQL
define('DB_NAME', 'psicologia_contigo');  // Nombre de la base de datos que creamos

function conectarDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8");
    return $conn;
}
?>