<?php 
session_start() 
?>
<?php require_once(__DIR__ . "/Persona.php");
if (isset($_GET['d'])) {
	$dato = ($_GET['d']);
	//	exit;


}
$u = new Persona();
$usuariosLista = $u->getUsuarios();


$_SESSION['nombres'] = $usuariosLista;
if (isset($_SESSION['nombres'])) {
	$usuariosLista = $_SESSION['nombres'];

	$usuariosRol1 = array();
	$usuariosRol2 = array();
	$usuariosRol3 = array();

	foreach ($usuariosLista as $usuario) {
		switch ($usuario['Rol']) {
			case 1:
				$usuariosRol1[] = $usuario;
				break;
			case 2:
				$usuariosRol2[] = $usuario;
				break;
			case 3:
				$usuariosRol3[] = $usuario;
				break;
			default:
				// Opcional: Manejar roles no reconocidos
				break;
		}
	}


} else {
	echo "La sesión no contiene el arreglo de nombres.";
}

// Control de arreglos de sesión  
// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

// echo '<pre>';
// print_r($usuariosRol1);
// print_r($usuariosRol2);
// print_r($usuariosRol3);
// echo '</pre>';
$html =''?>
<!DOCTYPE html>
<html lang="en"></html>
<head>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">

	<title>LOGIN</title>
	<meta content="" name="description">
	<meta content="" name="keywords">

	<!-- Favicons -->
	<link href="../assets/img/favicon.png" rel="icon">
	<link href="../assets/img/apple-touch-icon.png" rel="apple-touch-icon">

	<!-- Google Fonts -->
	<link
		href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i"
		rel="stylesheet">

	<!-- Vendor CSS Files -->
	<link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
	<link href="../assets/vendor/animate.css/animate.min.css" rel="stylesheet">
	<link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
	<link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
	<link href="../assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
	<link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet">
	<link href="../assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

	<!-- Template Main CSS File -->
	<link href="../assets/css/style.css" rel="stylesheet">

	<!-- =======================================================
  * Template Name: Medilab
  * Updated: Sep 18 2023 with Bootstrap v5.3.2
  * Template URL: https://bootstrapmade.com/medilab-free-medical-bootstrap-theme/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

	<!-- ======= Top Bar ======= -->
	<div id="topbar" class="d-flex align-items-center fixed-top">
		<div class="container d-flex justify-content-between">
			<div class="contact-info d-flex align-items-center">
				<i class="bi bi-envelope"></i> <a href="mailto:contact@example.com">VeisClinica@Veris.com</a>
				<i class="bi bi-phone"></i> +593 923 456 789
			</div>

		</div>
	</div>

	<!-- ======= Header ======= -->
	<header id="header" class="fixed-top">
		<div class="container d-flex align-items-center">

			<h1 class="logo me-auto"><a href="#">VERIS LOGIN</a></h1>




		</div>
	</header><!-- End Header -->


	<main id="main">


		<!-- ======= Contact Section ======= -->
		<section id="contact" class="contact">
			<div class="container">

				<div class="section-title">
					<h2>Login</h2>
					<p>Ingresa tus credenciales para poder acceder</p>
				</div>
			</div>

			 
			
			
			<div class="container">
				<div class="row mt-5">
					<div class="col-lg-6 mt-5 mt-lg-0">
<?php echo '<form class="container" action="validar.php" method="POST">';
echo '<div class="row">';
echo '<div class="col-md-6">'; // Colocar el select en la mitad izquierda
echo '<h2>';

switch ($dato) {
    case "ADM":
         echo '<select class="form-select" name="usuario">';
        foreach ($usuariosRol1 as $usuario) {
            echo "<option value=" . $usuario['Nombre'] . ">" . $usuario['Nombre'] . "</option>";
        }
        break;
    case "Medico":
       
        echo '<select class="form-select" name="usuario" required>';
        echo "<option disabled selected>Escoje un usuario de Medico</option>";
        foreach ($usuariosRol2 as $usuario) {
            echo "<option value=" . $usuario['Nombre'] . ">" . $usuario['Nombre'] . "</option>";
        }
        break;
    case "Paciente":
         echo '<select class="form-select" required name="usuario">';
        echo "<option disabled selected>Escoje un usuario de Paciente</option>";
        foreach ($usuariosRol3 as $usuario) {
            echo "<option value=" . $usuario['Nombre'] . ">" . $usuario['Nombre'] . "</option>";
        }
        break;
    default:
        // Opcional: Manejar roles no reconocidos
        break;
}

echo "</select>";
echo '</div>'; // Cierre del primer div (col-md-6)

echo '<div class="col-md-6">'; // Colocar el input de contraseña en la mitad derecha
 
echo '<input class="form-control" type="password" name="clave" placeholder="pasword">';
echo '</div>'; // Cierre del segundo div (col-md-6)

echo '<div class="col-md-12 text-center">'; // Colocar el input de contraseña en la mitad derecha
 
echo '<input type="hidden" name="secion" value="' . $dato . '" >  ';
echo '<input class="btn btn-primary" type="submit" value="LOGIN"> &nbsp;&nbsp; ';
echo '<input class="btn btn-secondary" type="button" value="CANCELAR" onclick="window.location.href=\'../index.php\'">';
echo '</div>';  

echo '</div>'; // Cierre del div row

// Inputs hidden, submit y button en la parte inferior 
echo "</form>";

 	 
 	 

						$html .= '<form action="validar.php" method="POST" role="form" class="php-email-form">
							<div class="row">
								<div class="col-md-6 form-group">
									<input type="text" name="name" class="form-control" id="name"
										placeholder="Your Name" required>
								</div>
								<div class="col-md-6 form-group mt-3 mt-md-0">
									<input type="email" class="form-control" name="email" id="email"
										placeholder="Your Email" required>
								</div>
							</div>


							<div class="my-3">
								<div class="loading">Loading</div>
								<div class="error-message"></div>
								<div class="sent-message">Your message has been sent. Thank you!</div>
							</div>
							<div class="text-center"><button type="submit" value="LOGIN">INICIAR</button></div>
						</form>'?>
					</div>

				</div>

			</div>
		</section><!-- End Contact Section -->

	</main><!-- End #main -->

	<!-- ======= Footer ======= -->
	<footer id="footer">



		<div class="container d-md-flex py-4">

			<div class="me-md-auto text-center text-md-start">
				<div class="copyright">
					&copy; Copyright <strong><span>Veris</span></strong>. Todos los derechos reservados
				</div>
				<div class="credits">
					<!-- All the links in the footer should remain intact. -->
					<!-- You can delete the links only if you purchased the pro version. -->
					<!-- Licensing information: https://bootstrapmade.com/license/ -->
					<!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/medilab-free-medical-bootstrap-theme/ -->
					Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
				</div>
			</div>

		</div>
	</footer><!-- End Footer -->

	<div id="preloader"></div>
	<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
			class="bi bi-arrow-up-short"></i></a>

	<!-- Vendor JS Files -->
	<script src="../assets/vendor/purecounter/purecounter_vanilla.js"></script>
	<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="../assets/vendor/glightbox/js/glightbox.min.js"></script>
	<script src="../assets/vendor/swiper/swiper-bundle.min.js"></script>
	<script src="../assets/vendor/php-email-form/validate.js"></script>

	<!-- Template Main JS File -->
	<script src="../assets/js/main.js"></script>

</body>

</html>