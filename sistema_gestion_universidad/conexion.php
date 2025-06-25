// conexion.php
<?php
$host = 'localhost';
$db   = 'universidad';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
    exit;
}
?>


// estudiantes.php
<?php
require 'conexion.php';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $stmt = $pdo->prepare("SELECT * FROM estudiante WHERE id_estudiante = ?");
            $stmt->execute([$_GET['id']]);
            echo json_encode($stmt->fetch());
        } else {
            $stmt = $pdo->query("SELECT * FROM estudiante");
            echo json_encode($stmt->fetchAll());
        }
        break;

    case 'POST':
        $stmt = $pdo->prepare("INSERT INTO estudiante (nombre, apellido, correo, fecha_nacimiento) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nombre'],
            $_POST['apellido'],
            $_POST['correo'],
            $_POST['fecha_nacimiento']
        ]);
        echo json_encode(['id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        parse_str(file_get_contents("php://input"), $_PUT);
        $stmt = $pdo->prepare("UPDATE estudiante SET nombre = ?, apellido = ?, correo = ?, fecha_nacimiento = ? WHERE id_estudiante = ?");
        $stmt->execute([
            $_PUT['nombre'],
            $_PUT['apellido'],
            $_PUT['correo'],
            $_PUT['fecha_nacimiento'],
            $_GET['id']
        ]);
        echo json_encode(['status' => 'actualizado']);
        break;

    case 'DELETE':
        $stmt = $pdo->prepare("DELETE FROM estudiante WHERE id_estudiante = ?");
        $stmt->execute([$_GET['id']]);
        echo json_encode(['status' => 'eliminado']);
        break;
}
?>
