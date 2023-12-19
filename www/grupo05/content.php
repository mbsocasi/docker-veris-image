<?php session_start() ?>
<?php 
require_once "./constantes.php";
require_once "./class/class.consulta.php";


// Almacenar el valor en una variable de sesión
if (isset($_GET['secion'])) {
    $_SESSION['secion'] = $_GET['secion'];
}

// Acceder al valor almacenado en la variable de sesión
if (isset($_SESSION['secion'])) {
    $valor = $_SESSION['secion'];

    // Separar el valor en dos partes usando explode
    list($usuarioSesion, $RolSesion) = explode('/', $valor);


    // echo "Usuario: $usuarioSecion, ROL: $rol";
} else {
    echo "La variable de sesión no está definida.";
}

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";


$nav = '';

$Consulta = "consulta/1";
$d_Consulta_final = base64_encode($Consulta);

$Consultaunica = "consultaunica/1";
$d_Consultaunica_final = base64_encode($Consultaunica);

$Medicos = "Med/1";
$d_Medicos_final = base64_encode($Medicos);

$Recetas = "res/1";
$d_Recetas_final = base64_encode($Recetas);

$Pacientes = "paci/1";
$d_Pacientes_final = base64_encode($Pacientes);

$PacienteUnico = "paciUnico/1";
$d_PacienteUnico_final = base64_encode($PacienteUnico);

$Medicamento = "medic/1";
$d_Medicamentos_final = base64_encode($Medicamento);

$Especialidades = "espe/1";
$d_Especialidades_final = base64_encode($Especialidades);


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>
    <!-- <header class="p-4 mb-3 border-bottom bg-dark"> -->
    <header class="p-4 mb-3 border-bottom  ">
        <div class="container hide-link">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="#" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
                    <!--<svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>-->
                    <img src="https://upload.wikimedia.org/wikipedia/commons/3/3a/Logo_ESPEOk.png" alt="" width="40"
                        height="32">
                </a>
                <?php
                $nav = '<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">';

                // Mostrar todos los elementos para RolSesion = 1
                if ($RolSesion == 1) {
                    $nav .= '
                        <li><a href="#" class="nav-link px-4 link-secondary ">VERIS</a></li>
                         <li><a href="./content.php?d=' . $d_Especialidades_final . '" class="nav-link px-3 ">Especialidades</a></li>
                        <li><a href=./content.php?d=' . $d_Medicamentos_final . '" class="nav-link px-3 ">Medicamentos</a></li>
                        <li><a href=./content.php?d=' . $d_Medicos_final . '" class="nav-link px-3 ">Medicos</a></li>
                        <li><a href=./content.php?d=' . $d_Pacientes_final . '" class="nav-link px-3 ">Pacientes</a></li>
                     ';
                }

                // Mostrar solo Consultas y Recetas para RolSesion = 2
                elseif ($RolSesion == 2) {
                    $nav .= '
                        <li><a href="#" class="nav-link px-4 link-secondary ">VERIS</a></li>
                        <li><a href="./content.php?d=' . $d_Consulta_final . '" class="nav-link px-3 active">Consultas</a></li>
                        <li><a href=./content.php?d=' . $d_Recetas_final . '" class="nav-link px-3 ">Recetas</a></li>
                    ';
                }

                // Mostrar solo Consultas y Pacientes para RolSesion = 3
                elseif ($RolSesion == 3) {
                    $nav .= '
                        <li><a href="#" class="nav-link px-4 link-secondary ">VERIS</a></li>
                        <li><a href="./content.php?d=' . $d_Consultaunica_final . '" class="nav-link px-3 active">Consultas</a></li>
                        <li><a href=./content.php?d=' . $d_PacienteUnico_final . '" class="nav-link px-3 ">Paciente</a></li>
                    ';
                }

                // Cierre del menú de navegación
                $nav .= '</ul>';

                // Dropdown
                $nav .= '
                    <div class="dropdown text-end">
                        <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Bienvenido ' . $usuarioSesion . '
                            <img src="https://static.vecteezy.com/system/resources/thumbnails/004/607/791/small/man-face-emotive-icon-smiling-male-character-in-blue-shirt-flat-illustration-isolated-on-white-happy-human-psychological-portrait-positive-emotions-user-avatar-for-app-web-design-vector.jpg" alt="mdo" width="32" height="32" class="rounded-circle">
                        </a>
                        <ul class="dropdown-menu text-small">
                            <li><a class="dropdown-item" href="logout.php">Cerrar Sesion</a></li>
                        </ul>
                    </div>
                ';

                echo $nav;
                ?>



            </div>
        </div>
    </header>
    <section>
        <?php
        
        $cn = conectar();
        $v = new consulta($cn);

        if (isset($_GET['d'])) {
            $dato = base64_decode($_GET['d']);
            // echo $dato;
        
            $tmp = explode("/", $dato);
            $op = $tmp[0];
            $id = $tmp[1];

            switch ($op) {
                //consulta
                case "consulta":
                    echo $v->get_list();
                    break;
                case "consultaunica":
                    echo $v->get_listUnico();
                    break;

                case "det":
                    echo $v->get_detail_consulta($id);
                    break;

                case "act":
                    echo $v->get_form($id);
                    break;

                case "new":
                    echo $v->get_form();
                    break;

                case "del":
                    echo $v->delete_conculta($id); // BORRAR TODOS LOS REGISTROS DE LA BASE DE DATOS
                    break;
                //fin consulta
                // Medicos
                case "Med":
                    echo $v->get_list_medicos();
                    break;
                case "detmed":
                    echo $v->get_detail_medicos($id);
                    break;

                case "actmed":
                    echo $v->get_form_medicos($id);
                    break;

                case "newmed":
                    echo $v->get_form_medicos();
                    break;

                case "delmed":
                    echo $v->delete_medico($id); // BORRAR TODOS LOS REGISTROS DE LA BASE DE DATOS
                    break;
                //fin Medicos
                //Pacientes	
                case "paci":
                    echo $v->get_list_pacientes();
                    break;
                case "paciUnico":
                    echo $v->get_list_pacientesUnico();
                    break;
                case "detpas":
                    echo $v->get_detail_pacientes($id);
                    break;

                case "actpas":
                    echo $v->get_form_pacientes($id);
                    break;

                case "newpas":
                    echo $v->get_form_pacientes();
                    break;

                case "delpas":
                    echo $v->delete_Paciente($id); // BORRAR TODOS LOS REGISTROS DE LA BASE DE DATOS
                    break;
                //fin Pacientes
                //MEdicamentos
        
                case "medic":
                    echo $v->get_list_medicamentos();
                    break;
                case "detmedicamentos":
                    echo $v->get_detail_medicamentos($id);
                    break;

                case "actmedicamentos":
                    echo $v->get_form_medicamentos($id);
                    break;

                case "newmedicamento":
                    echo $v->get_form_medicamentos();
                    break;

                case "delmedicamentos":
                    echo $v->delete_Medicamentos($id); // BORRAR TODOS LOS REGISTROS DE LA BASE DE DATOS
                    break;
                //fin medicamentos
                //inicio recetas
                case "res":
                    echo $v->get_list_recetas();
                    break;
                case "detreceta":
                    echo $v->get_detail_recetas($id);
                    break;
                case "actreceta":
                    echo $v->get_form_recetas($id);
                    break;
                case "newreceta":
                    echo $v->get_form_recetas();
                    break;
                case "delreceta":
                    echo $v->delete_recetas($id); // BORRAR TODOS LOS REGISTROS DE LA BASE DE DATOS
                    break;

                //especialidad
        
                case "espe":
                    echo $v->get_list_especialidades();
                    break;
                case "detespecialidades":
                    echo $v->get_detail_especialidades($id);
                    break;
                case "actespecialidades":
                    echo $v->get_form_especialidad($id);
                    break;
                case "newespe":
                    echo $v->get_form_especialidad();
                    break;
                case "delespecialidades":
                    echo $v->delete_especialidad($id); // BORRAR TODOS LOS REGISTROS DE LA BASE DE DATOS
                    break;

            }
        }

        // PARTE III	
        else {

            // echo "<br>PETICION POST <br>";
            // echo "<pre>";
            // print_r($_POST);
            // echo "</pre>";
            if (isset($_POST['Guardar'])) {

                switch (isset($_POST['Guardar'])) {
                    case $_POST['op'] == "new":
                        $v->save_vehiculo();
                        break;
                    case $_POST['op'] == "update":
                        $v->update_vehiculo();
                        break;
                    case $_POST['op'] == "newMedico":
                        $v->save_medico();
                        break;
                    case $_POST['op'] == "updateMedico":
                        $v->update_medico();
                        break;
                    case $_POST['op'] == "newPaciente":
                        $v->save_pacientes();
                        break;
                    case $_POST['op'] == "updatePaciente":
                        $v->update_pacientes();
                        break;
                    case $_POST['op'] == "newMedicamento":
                        $v->save_Medicamentos();
                        break;
                    case $_POST['op'] == "updateMedicamento":
                        $v->update_Medicamentos();
                        break;
                    case $_POST['op'] == "updateRecetas":
                        $v->update_receta();
                        break;
                    case $_POST['op'] == "newRecetas":
                        $v->save_recetas();
                        break;
                    case $_POST['op'] == "updateespe":
                        $v->update_especialidades();
                        break;
                    case $_POST['op'] == "newespe":
                        $v->save_especialidades();
                        break;


                }
            } else {
                
$html ='
<section class="container">
    <div class="row">
        <div class="col text-center">
            <h1>VEIS</h1>
            <p>Bienvenido a VEIS, su centro de atención médica integral dedicado a proporcionar servicios de salud excepcionales. En nuestra clínica, nos enorgullece ofrecer atención médica de calidad, personalizada y compasiva para satisfacer las necesidades de nuestra comunidad.</p>
        </div>
    </div>
</section>
';
                echo $html; 
            }
        }

        //*******************************************************
        function conectar()
        {
            //echo "<br> CONEXION A LA BASE DE DATOS<br>";
            $c = new mysqli(SERVER, USER, PASS, BD);

            if ($c->connect_errno) {
                die("Error de conexión: " . $c->mysqli_connect_errno() . ", " . $c->connect_error());
            } else {
                //echo "La conexión tuvo éxito .......<br><br>";
            }

            $c->set_charset("utf8");
            return $c;
        }
        //**********************************************************		
        ?>

    </section>
    <!-- <footer
        class="d-flex flex-wrap justify-content-between align-items-center py-3  mt-5  border-top bg-dark text-white  ">
        <div class="container text-center">
            <u>@ Todos los derechos reservados</u>
        </div>
    </footer> -->





    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
        </script>
</body>

</html>