<?php session_start()?>
<?php require_once(__DIR__ . "/Persona.php");
require_once(__DIR__ . "/constantes.php");
include_once(__DIR__ . "/Persona.php");

$usuariosLista = $_SESSION['usuarios'];

if (isset($_POST['op'])) {
    $op = $_POST['op'];
} elseif (isset($_GET['op'])) {
    $op = $_GET['op'];
} else {
    $op = null;
}

// Verificar si $op está definido antes de usarlo
$obj = isset($usuariosLista[$op]) ? $usuariosLista[$op] : null;

echo '<html>
        <head>
            <title>Matriculas Vehículos</title>
            <meta http-equiv="content-type" content="text/html;charset=utf-8" />
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" crossorigin="anonymous" />
        </head>
        <body style="padding-top: 75px;">

            <h1>' . ($obj ? $obj['Nombre'] : '') . '</h1>';

if ($obj) {
    echo '<table border=1 align="center" style="width:100%">
                <tr>
                    <th colspan="3">BIENVENIDO!</th>
                </tr>
                <tr>
                    <th colspan="3">Hola Usuario: ' . $obj['Nombre'] . ' !</th>
                </tr>
            </table>';
}

echo '</body>
    </html>';
?>
