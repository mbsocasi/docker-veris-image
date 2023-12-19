<?php
require_once "Persona.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar si las variables POST existen antes de acceder a ellas
    $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : null;
    $clave = isset($_POST['clave']) ? $_POST['clave'] : null;
    // Validar que las variables no estén vacías
    if ($usuario && $clave) {
        $u = new Persona();
        $u->setNombre($usuario);
        $u->setPassword($clave);

        $validacionData = $u->validarLogin();

        if ($validacionData['validacion']) {
            $rolPersonaId = $validacionData['Rol'];
            //$mv = "Location: ../content.php?secion=" . $usuario . '/' . $rolPersonaId;
            echo "<script>window.location.href = '"."../content.php?secion=".$usuario . '/' . $rolPersonaId."';</script>";
            exit();
        } else {
            // Manejar el error en la autenticación
            header("Location: ErrorAutentificacion.php");
            exit();
        }
    } else {
        // Manejar el caso donde usuario o clave estén vacíos
        echo "Error: Usuario o clave no proporcionados";
    }
} else {
    // Manejar el caso donde el formulario no se envió mediante POST
    echo "Error: El formulario no fue enviado correctamente";
}

?>