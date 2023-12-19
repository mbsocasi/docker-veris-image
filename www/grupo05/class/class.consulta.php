<?php require_once(__DIR__ . '/../content.php');
$valor = $_SESSION['secion'];
// Separar el valor en dos partes usando explode
list($usuarioSecion, $CrudRoles) = explode('/', $valor);

define("USUARIOSECSION", $usuarioSecion);

// $html .= "Usuario: $usuarioSecion, ROl: $rol";

class consulta
{
	private $ConsultaID;
	private $PacienteID;
	private $MedicoID;
	private $FechaConsulta;
	private $Diagnostico;
	private $Foto;
	private $Genero;
	private $Especialidad;
	private $con;
	private $NombreMedico;
	private $NombrePaciente;
	private $Edad;
	private $Tipo;
	private $NombreMedicamento;
	private $MedicamentosID;
	private $Cantidad;
	private $RecetasID;
	private $NombreESP;
	private $Dias;
	private $HoraInicio;
	private $HoraFinal;
	private $Hora;
	private $UsuarioId;
	private $Mail;
	private $Cedula;
	private $Usuario;
	private $Password;
	private $rol;
	private $estatura;
	private $peso;




	function __construct($cn)
	{
		$this->con = $cn;
	}
	//************************************* Consulta 
	//****************************************************** Procesos para validar formulario
	//**************************************************** 
	//*********************** 3.1 METODO update_vehiculo() **************************************************	
	public function update_vehiculo()
	{
		$this->ConsultaID = $_POST['id'];
		$sql_up = "SELECT c.IdConsulta, p.Nombre as paciente, m.Nombre as medico, c.FechaConsulta, p.Genero, m.Especialidad, c.Diagnostico, c.HI, c.HF, e.Descripcion as Especialidad, c.IdMedico, c.IdPaciente
				FROM consultas c
				JOIN medicos m ON c.IdMedico = m.IdMedico
				JOIN pacientes p ON c.IdPaciente = p.IdPaciente
				JOIN especialidades e ON m.Especialidad = e.IdEsp
				WHERE c.IdConsulta = $this->ConsultaID";
		$res = $this->con->query($sql_up);
		$row = $res->fetch_assoc();

		$this->Diagnostico = $_POST['Diagnostico'];
		$this->MedicoID = $row['IdMedico'];
		$this->PacienteID = $row['IdPaciente'];
		$this->FechaConsulta = $row['FechaConsulta'];
		$this->Genero = $row['Genero'];
		$this->Especialidad = $row['Especialidad'];
		$this->HoraInicio = $row['HI'];
		$this->HoraFinal = $row['HF'];

		$sql = "UPDATE consultas SET 
							IdMedico='$this->MedicoID',

							IdPaciente='$this->PacienteID',
							
							FechaConsulta='$this->FechaConsulta',
							
							HI='$this->HoraInicio',
							HF='$this->HoraFinal',
							Diagnostico='$this->Diagnostico'
						WHERE IdConsulta=$this->ConsultaID";

		if ($this->con->query($sql)) {
			echo $this->_message_ok("Guardado exitoso");
		} else {
			echo $this->_message_error("Error al guardar: " . $this->con->error);
		}
	}




	//*********************** 3.2 METODO save_vehiculo() **************************************************	

	public function save_vehiculo()
	{
		$this->PacienteID = $_POST['Nombre'];
		$this->FechaConsulta = $_POST['Fecha'];
		$this->Especialidad = $_POST['Descripcion'];
		$this->Diagnostico = $_POST['Diagnostico'];
		$this->Hora = $_POST['turno'];
		$this->Genero = $_POST['Genero'];

		$this->MedicoID = $this->set_medico($this->Especialidad);

		$simbolica = 'PT1H';
		$auxiliar = $this->get_horario($this->Hora, $this->FechaConsulta, $this->Especialidad, $this->MedicoID, $simbolica);

		$this->HoraInicio = $auxiliar[0];
		$this->HoraFinal = $auxiliar[1];

		if ($auxiliar[0] === null && $auxiliar[1] === null) {
			$html = '<div class="container mt-5">
								<!-- Alerta de éxito -->
								<div class="alert alert-danger" role="alert">
									¡Error! Los turnos están llenos.
								</div>
							</div>';
			echo $html;
			echo $this->_message_error_turnos("turno");
		} else {

			$sql = "INSERT INTO consultas (IdMedico, IdPaciente,  FechaConsulta,  HI, HF,Diagnostico)  
							VALUES( '$this->MedicoID',
									'$this->PacienteID',
									'$this->FechaConsulta',	
									'$this->HoraInicio',
									'$this->HoraFinal',
									'$this->Diagnostico'
								)";

			if ($this->con->query($sql)) {
				echo $this->_message_ok("Guardado exitoso");
			} else {
				echo $this->_message_error("Error al guardar: " . $this->con->error);
			}
		}
	}


	//*********************** 3.3 METODO _get_name_File() **************************************************	


	private function _get_name_file($nombre_original, $tamanio)
	{
		$tmp = explode(".", $nombre_original); //Divido el nombre por el punto y guardo en un arreglo
		$numElm = count($tmp); //cuento el número de elemetos del arreglo
		$ext = $tmp[$numElm - 1]; //Extraer la última posición del arreglo.
		$cadena = "";
		for ($i = 1; $i <= $tamanio; $i++) {
			$c = rand(65, 122);
			if (($c >= 91) && ($c <= 96)) {
				$c = NULL;
				$i--;
			} else {
				$cadena .= chr($c);
			}
		}
		return $cadena . "." . $ext;
	}



	//Ya esta
	public function get_form($ConsultaID = NULL)
	{
		if ($ConsultaID == NULL) {
			$this->PacienteID = NULL;
			$this->MedicoID = NULL;
			$this->FechaConsulta = NULL;
			$this->Diagnostico = NULL;
			$this->Genero = NULL;
			$this->Especialidad = NULL;
			$this->Foto = NULL;
			$this->NombreMedico = NULL;
			$this->NombrePaciente = NULL;

			$flag = NULL;
			$op = "new";
			$control = 'enable';
		} else {
			$sql = "SELECT c.IdConsulta, p.Nombre as paciente, m.Nombre as medico, c.FechaConsulta, p.Genero, m.Especialidad as EspID, c.Diagnostico, c.HI, c.HF, e.Descripcion as Especialidad, c.IdMedico, c.IdPaciente
						FROM consultas c
						JOIN medicos m ON c.IdMedico = m.IdMedico
						JOIN pacientes p ON c.IdPaciente = p.IdPaciente
						JOIN especialidades e ON m.Especialidad = e.IdEsp
						WHERE c.IdConsulta = $ConsultaID;";

			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();
			$num = $res->num_rows;

			if ($num == 0) {
				$mensaje = "Intento de actualizar la consulta con ID = " . $ConsultaID;
				echo $this->_message_error($mensaje);
			} else {
				$this->NombreMedico = $row['medico'];
				$this->NombrePaciente = $row['paciente'];
				$this->MedicoID = $row['IdMedico'];
				$this->PacienteID = $row['IdPaciente'];
				$this->FechaConsulta = $row['FechaConsulta'];
				$this->Diagnostico = $row['Diagnostico'];
				$this->Genero = $row['Genero'];
				$this->Especialidad = $row['Especialidad'];
				$this->HoraInicio = $row['HI'];
				$this->HoraFinal = $row['HF'];

				$flag = "enable";
				$op = "update";
				$control = 'disabled';
			}
		}

		$html = '
						<div class="container mt-4 text-center">
							<div class="row justify-content-center">
								<div class="col-md-6">
									<form name="vehiculo" method="POST" action="content.php" enctype="multipart/form-data">
										<input type="hidden" name="id" value="' . $ConsultaID . '">
										<input type="hidden" name="op" value="' . $op . '">
										<input type="hidden" name="final" value="' . $this->HoraFinal . '">
										<input type="hidden" name="inicial" value="' . $this->HoraInicio . '">
										
										<table class="table table-bordered" ">
											<tr>
												<th class="table-dark" colspan="2">DATOS DE LA CONSULTA</th>
											</tr>
											<tr>
												<td>Nombre del paciente:</td>
												<td>' . $this->_get_combo_db("pacientes", "IdPaciente", "Nombre", "Nombre", $this->PacienteID, $control) . '</td>
											</tr>
											<input type="hidden" name="Genero" value="' . $this->Genero . '">
											<tr>
												<td>Especialidad:</td>
												<td>' . $this->_get_combo_db("especialidades", "IdEsp", "Descripcion", "Descripcion", $this->Especialidad, $control) . '</td>
											</tr>
											<tr>
												<td><label for="turno">Selecciona un turno:</label></td>
												<td>
													<select class="form-control" id="turno" name="turno" ' . $control . '>
														<option value="Diurna" ' . ($this->HoraInicio == 'Diurna' ? 'selected' : '') . '>Diurna</option>
														<option value="Vespertina" ' . ($this->HoraInicio == 'Vespertina' ? 'selected' : '') . '>Vespertina</option>
													</select>
												</td>
											</tr>
											<tr>
												<td>Diagnostico:</td>
												<td><input type="text" class="form-control" name="Diagnostico" value="' . $this->Diagnostico . '" required></td>
											</tr>
											<tr>
												<td>Fecha:</td>
												<td><input type="date" id="Fecha" name="Fecha" value="' . $this->FechaConsulta . '" required ' . $control . '></td>
											</tr>
											<tr>
												<th class="table-dark" colspan="2" class="text-center">
													<input class="btn btn-success" onclick="habilitarElementos()" type="submit" name="Guardar" value="GUARDAR">
												</th>
											</tr>
										</table>
									</form>
									<a href="./content.php"><button class="btn btn-primary  mx-auto">REGRESAR</button></a>
								</div>
							</div>
						</div>';

		return $html;
	}
	//Ya esta
	public function get_listUnico()
	{
		require_once("./content.php");
		$valor = $_SESSION['secion'];

		// Separar el valor en dos partes usando explode
		list($usuarioSecion, $CrudRoles) = explode('/', $valor);
		$d_new = "new/0";
		$d_new_final = base64_encode($d_new);
		$pat = PATH;
		$html = '
					<div class="text-center">
					<table class="table"  align="center">
						<tr >
							<th class="table-dark"  colspan="8">Lista de Consultas del paciente ' . $usuarioSecion . '</th>
						</tr>
						 
						<tr class="table-dark">
							<th>Paciente</th>
							<th>Medico</th>
							<th>Fecha</th>
							<th colspan="3">Acciones</th>
						</tr>';
		$sql = "SELECT c.IdConsulta, p.Nombre as paciente, m.Nombre as medico, c.FechaConsulta, p.Genero, m.Especialidad as EspID, c.Diagnostico
		FROM consultas c
		JOIN medicos m ON c.IdMedico = m.IdMedico
		JOIN pacientes p ON c.IdPaciente = p.IdPaciente
		JOIN especialidades e ON m.Especialidad = e.IdEsp
		JOIN usuarios u ON p.IdUsuario = u.IdUsuario
		WHERE u.Nombre = '$usuarioSecion';";


 		$res = $this->con->query($sql);
 		$num = $res->num_rows;
		 
		if ($num  == 0) {
			 
			$html .= ' 
			<div class="container alert alert-success" role="alert">
			Paciente '.$usuarioSecion.' Usted no tiene consultas <br><br>
			<a href="./logout.php"><button class="btn btn-primary" >HOME</button></a></div>
			 ';
		} else {
			while ($row = $res->fetch_assoc()) {
				$d_del = "del/" . $row['IdConsulta'];
				$d_del_final = base64_encode($d_del);
				$d_act = "act/" . $row['IdConsulta'];
				$d_act_final = base64_encode($d_act);
				$d_det = "det/" . $row['IdConsulta'];
				$d_det_final = base64_encode($d_det);
				$html .= '
								<tr>
							
									<td>' . $row['paciente'] . '</td>
									<td>' . $row['medico'] . '</td>
									<td>' . $row['FechaConsulta'] . '</td>
	
								
											 <td><a href="./content.php?d=' . $d_det_final . '"><button class="btn btn-info"> Detalle</button></a></td>
										</tr>';
			}
			$html .= '  
								</table>
	
	
	
								<a href="./logout.php"><button class="btn btn-primary" >HOME</button></a>
								<div>
								
								';
		}
		// Sin codificar <td><a href="content.php?op=del&id=' . $row['id'] . '">Borrar</a></td>
		

		return $html;

	}

	public function get_list()
	{
		$d_new = "new/0";
		$d_new_final = base64_encode($d_new);
		$pat = PATH;
		$html = '
					<div class="text-center mb-5 ">
					<table class="table""   align="center">
						<tr class="table-dark" >
							<th colspan="8">Lista de Consultas</th>
						</tr>
						<tr class="table-dark">
							<th colspan="8"><a class="btn btn-primary" href="./content.php?d=' . $d_new_final . '">Nuevo</a></th>
						</tr>
						<tr class="table-dark">
							<th>Paciente</th>
							<th>Medico</th>
							<th>Fecha</th>
							<th colspan="3">Acciones</th>
						</tr>';
		$sql = "SELECT c.IdConsulta, p.Nombre as paciente, m.Nombre as medico, c.FechaConsulta, p.Genero, m.Especialidad as EspID, c.Diagnostico
						FROM consultas c
						JOIN medicos m ON c.IdMedico = m.IdMedico
						JOIN pacientes p ON c.IdPaciente = p.IdPaciente
						JOIN especialidades e ON m.Especialidad = e.IdEsp;";


		$res = $this->con->query($sql);



		// Sin codificar <td><a href="content.php?op=del&id=' . $row['id'] . '">Borrar</a></td>
		while ($row = $res->fetch_assoc()) {
			$d_del = "del/" . $row['IdConsulta'];
			$d_del_final = base64_encode($d_del);
			$d_act = "act/" . $row['IdConsulta'];
			$d_act_final = base64_encode($d_act);
			$d_det = "det/" . $row['IdConsulta'];
			$d_det_final = base64_encode($d_det);
			$html .= '
							<tr>
						
								<td>' . $row['paciente'] . '</td>
								<td>' . $row['medico'] . '</td>
								<td>' . $row['FechaConsulta'] . '</td>

							
									<td><a href="./content.php?d=' . $d_del_final . '"><button class="btn btn-danger">Borrar</button></a></td>
										<td><a href="./content.php?d=' . $d_act_final . '"><button class="btn btn-success">Actualizar</button></a></td>
										<td><a href="./content.php?d=' . $d_det_final . '"><button class="btn btn-info">Detalle</button></a></td>
									</tr>';
		}
		$html .= '  
							


		<tr class="table-dark">
		 
		<th colspan="6"><a href="./logout.php"><button class="btn btn-primary" >HOME</button></a></th>
	</tr> 

							</table>
							<div>
							
							';

		return $html;

	}

	//ya estas
	public function get_detail_consulta($id)
	{
		$sql = "SELECT c.IdConsulta, p.Nombre as paciente, m.Nombre as medico, c.FechaConsulta, p.Genero, m.Especialidad as EspID, c.Diagnostico, c.HI, c.HF, e.Descripcion as Especialidad
					FROM consultas c
					JOIN medicos m ON c.IdMedico = m.IdMedico
					JOIN pacientes p ON c.IdPaciente = p.IdPaciente
					JOIN especialidades e ON m.Especialidad = e.IdEsp
					WHERE c.IdConsulta = $id;";

		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		$num = $res->num_rows;

		// Si no hay registros, muestra un mensaje de error
		if ($num == 0) {
			$mensaje = "Intento de editar una consulta con ID = " . $id;
			echo $this->_message_error($mensaje);
		} else {
			$html = '
							<div class="container mt-4 text-center">
								<div class="row justify-content-center">
									<div class="col-md-6">
										<table class="table table-bordered" " align="center">
											<tr class="table-dark">
												<th colspan="2">DATOS DE LA CONSULTA</th>
											</tr>
											<tr>
												<td>Paciente: </td>
												<td>' . $row['paciente'] . '</td>
											</tr>
											<tr>
												<td>Genero: </td>
												<td>' . $row['Genero'] . '</td>
											</tr>
											<tr>
												<td>Medico: </td>
												<td>' . $row['medico'] . '</td>
											</tr>
											<tr>
												<td>Especialidad: </td>
												<td>' . $row['Especialidad'] . '</td>
											</tr>
											<tr>
												<td>Fecha: </td>
												<td>' . $row['FechaConsulta'] . '</td>
											</tr>
											<tr>
												<td>Diagnostico: </td>
												<td>' . $row['Diagnostico'] . '</td>
											</tr>
											<tr>
												<td>Hora: </td>
												<td>' . $row['HI'] . ' - ' . $row['HF'] . '</td>
											</tr>
											<tr></tr>
												<th colspan="2" class="table table-dark">
													<a href="./content.php"><button class="btn btn-primary mx-auto">Regresar</button></a>
												</th>
											</tr>
										</table>
									</div>
								</div>
							</div>';

			return $html;
		}
	}


	public function delete_conculta($id)
	{

		//Eliminar el medico que se uso
		$sql = "DELETE FROM consultas WHERE IdConsulta=$id;";
		echo $sql;
		if ($this->con->query($sql)) {
			$html = '<div class="container mt-5">
							<!-- Alerta de éxito -->
							<div class="alert alert-success" role="alert">
							¡Operación exitosa!
							</div>
							</div>';
			echo $html;
			echo $this->_message_ok("ELIMINÓ");
		} else {
			$html = '<div class="container mt-5">
						<!-- Alerta de éxito -->
						<div class="alert alert-danger" role="alert">
						¡Error! La consulta tiene recetas activas.
					  </div>
						</div>';
			echo $html;
			echo $this->_message_error("eliminar");
		}
	}

	public function delete_aux_consultas($id)
	{



		$sql_delete_consultas = "DELETE FROM recetas WHERE ConsultaID = $id";
		$this->con->query($sql_delete_consultas);

		//Eliminar el medico que se uso
		$sql_consulta = "DELETE FROM consultas WHERE ConsultaID=$id;";
		echo $sql_consulta;
		$this->con->query($sql_consulta);

	}


	private function _message_error($tipo)
	{
		$html = '
		<div class="container text-center">
			<div class="alert alert-danger" role="alert">
			Error al ' . $tipo . '. Favor contactar a .................... 
			<br>
			<br>
 			<a class="btn btn-info" href="content.php">Regresar</a> 
			</div>
			
		</div> 
					 ';
		return $html;
	}
	private function _message_error_turnos($tipo)
	{
		$html = '
		<div class="container text-center">
			<div class="alert alert-danger" role="alert">
			Busque un ' . $tipo . '. en otra fecha:
			<br>
			<br>
 			<a class="btn btn-info" href="content.php">Regresar</a> 
			</div>
			
		</div> 
					 ';
		return $html;
	}
	private function _message_ok($tipo)
	{
		$html = '
		<div class="container text-center">
			<div class="alert alert-success" role="alert">
			El registro se  ' . $tipo . ' correctamente
			<br>
			<br>
 			<a class="btn btn-info" href="content.php">Regresar</a> 
			</div>
			
		</div> ';
		return $html;
	}


	//*************************************************************************


	//*************************************Medicos	
	//ya esta
	public function get_detail_medicos($id)
	{
		$Medicos = "Med/1";
		$d_Medicos_final = base64_encode($Medicos);
		$sql = "SELECT m.IdMedico,m.Nombre,e.Descripcion,e.Dias,e.Franja_HI,e.Franja_HF,u.Foto
			FROM medicos m,especialidades e,usuarios u WHERE IdMedico=$id AND m.Especialidad=e.IdEsp AND u.IdUsuario =m.IdUsuario;
			";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		$pat = PATH;
		$num = $res->num_rows;

		//Si es que no existiese ningun registro debe desplegar un mensaje 
		//$mensaje = "tratar de eliminar el vehiculo con id= ".$id;
		//echo $this->_message_error($mensaje);
		//y no debe desplegarse la tablas

		if ($num == 0) {
			$mensaje = "tratar de editar el vehiculo con id= " . $id;
			echo $this->_message_error($mensaje);
		} else {
			$html = '
					<div class="container mt-4">
			<div class="row justify-content-center">
			<div class="col-md-6">
				<table class="table table-bordered"   align="center">
							<tr>
								<th class="table table-dark text-center"  colspan="2">DATOS DE MEDICO</th>
							</tr>
							<tr>
								<td>Nombre: </td>
								<td>' . $row['Nombre'] . '</td>
							</tr>
							<tr>
								<td>Especialidad: </td>
								<td>' . $row['Descripcion'] . '</td>
							</tr>
							<tr>
								<td>Dias de trabajo: </td>
								<td>' . $row['Dias'] . '</td>
							</tr>
							<tr>
								<td>Hora Inicio: </td>
								<td>' . $row['Franja_HI'] . '</td>
							</tr>
							<tr>
								<td>Hora Final: </td>
								<td>' . $row['Franja_HF'] . '</td>
							</tr>
							<tr>
							<th colspan="2"><img src="' . $pat . $row['Foto'] . '" width="300px"/></th>
							</tr>
						<th colspan="2" class="text-center table table-dark"><a href="./content.php?d=' . $d_Medicos_final . '"><button class="btn btn-primary mx-auto" >Regresar</button></a>
						<div></th>
					</tr>																		</table>
					</div>
			</div>
			</div>';

			return $html;
		}
	}
	//Ya esta
	public function get_list_medicos()
	{
		$d_new = "newmed/0";
		$d_newmed_final = base64_encode($d_new);
		$pat = PATH;
		$html = '
		<div class="text-center">
			<table class="table " " align="center">
				<tr>
					<th class="table table-dark " colspan="8">Lista de Medicos</th>
				</tr>
				<tr>
					<th class="table table-dark " colspan="8"><a class="btn btn-primary " href="./content.php?d=' . $d_newmed_final . '">Nuevo</a></th>
				</tr>
				<tr  class="table table-dark ">
					<th>Nombre</th>
					<th>Especialidad</th>
					<th colspan="3">Acciones</th>
				</tr>';

		$sql = "SELECT m.IdMedico, m.Nombre, e.Descripcion
				FROM medicos m
				INNER JOIN especialidades e ON m.Especialidad = e.IdEsp";
		$res = $this->con->query($sql);

		// Sin codificar <td><a href="content.php?op=del&id=' . $row['id'] . '">Borrar</a></td>
		while ($row = $res->fetch_assoc()) {
			$d_del = "delmed/" . $row['IdMedico'];
			$d_delmed_final = base64_encode($d_del);
			$d_act = "actmed/" . $row['IdMedico'];
			$d_actmed_final = base64_encode($d_act);
			$d_det = "detmed/" . $row['IdMedico'];
			$d_detmed_final = base64_encode($d_det);
			$html .= '
				<tr>
					<td>' . $row['Nombre'] . '</td>
					<td>' . $row['Descripcion'] . '</td>
					<td><a href="./content.php?d=' . $d_delmed_final . '"><button class="btn btn-danger">Borrar</button></a></td>
					<td><a href="./content.php?d=' . $d_actmed_final . '"><button class="btn btn-success">Actualizar</button></a></td>
					<td><a href="./content.php?d=' . $d_detmed_final . '"><button class="btn btn-info">Detalle</button></a></td>
				</tr>';
		}

		$html .= '
		
		<tr  class="table table-dark "> 
					<th colspan="5">
					<a href="./logout.php"><button class="btn btn-primary" >HOME</button></a>
					</th>
				</tr>
		</table>
 				  </div>';

		return $html;
	}
	public function get_form_medicos($MedicoID = NULL)
	{
		$Medicos = "Med/1";
		$d_Medicos_final = base64_encode($Medicos);
		if ($MedicoID == NULL) {
			$this->Especialidad = NULL;
			$this->NombreMedico = NULL;
			$this->Usuario = NULL;
			$flag = NULL;
			$op = "newMedico";
			$control = 'enable';

		} else {


			$sql = "SELECT m.IdMedico,m.Nombre,e.Descripcion,u.Nombre as USUARIO,u.Foto,m.Especialidad
			FROM medicos m,especialidades e,usuarios u WHERE m.IdMedico=$MedicoID AND u.IdUsuario =m.IdUsuario AND m.Especialidad=e.IdEsp;
			";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();

			$num = $res->num_rows;
			if ($num == 0) {
				$mensaje = "tratar de actualizar el vehiculo con id= " . $MedicoID;
				echo $this->_message_error_Medico($mensaje);
			} else {

				// ***** TUPLA ENCONTRADA *****
				// echo "<br>TUPLA <br>";
				// echo "<pre>";
				// 	print_r($row);
				// echo "</pre>";
				$this->NombreMedico = $row['Nombre'];
				$this->MedicoID = $row['IdMedico'];
				$this->Especialidad = $row['Especialidad'];
				$this->Foto = $row['Foto'];
				$this->Usuario = $row['USUARIO'];



				$flag = "enable";
				$op = "updateMedico";
				$control = 'disabled';
			}
		}

		$pat = PATH;

		$html = '
		<div class="container mt-4">
			<div class="row justify-content-center">
				<div class="col-md-6">
					<form name="vehiculo" method="POST" action="content.php" enctype="multipart/form-data"">
					
					<input type="hidden" name="id" value="' . $MedicoID . '">
					<input type="hidden" name="op" value="' . $op . '">
					
					
					<table class="table table-bordered" >
							<tr>
								<th class="table table-dark text-center" colspan="2">DATOS MEDICO</th>
							</tr>
							<tr>
							<tr>
							<td>Nombre:</td>
							<td><input type="text" class="form-control" name="Medico" value="' . $this->NombreMedico . '" required ' . $control . '></td>
						</tr>
						<tr>
							<td>Especialidad:</td>
							<td>' . $this->_get_combo_db("especialidades", "IdEsp", "Descripcion", "Especialidad", $this->Especialidad, $control) . '</td>
							</tr>
							<tr>
							<td>Usuario:</td>
							<td><input type="text" class="form-control" name="User" value="' . $this->Usuario . '" required ' . $control . '></td>
						</tr>
						<tr>
								<td>Foto:</td>
								<td><input type="file" name="foto" value=" ' . $this->Foto . '"' . $flag . '></td>
							</tr>
							<tr>
							<th colspan="2" class="text-center table table-dark"><input type="submit" class="btn btn-success" name="Guardar" value="GUARDAR"></th>
							</tr>												
						</table>
						</form>
						<a href="./content.php?d=' . $d_Medicos_final . '"><button class="btn btn-primary  mx-auto" >REGRESAR</button></a>
						</div>
						</div>
					</div>
				
					
					
					
					';
		return $html;
	}
	public function save_medico()
	{
		$this->NombreMedico = $_POST['Medico'];
		$this->Especialidad = $_POST['Especialidad'];
		$this->Foto = $_FILES['foto']['name'];
		$this->Usuario = $_POST['User'];
		$path = PATH . $this->Foto;


		if (!move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}

		$sqlVerificarPersona = "SELECT IdUsuario FROM usuarios WHERE  Nombre= '$this->Usuario'";
		$resultadoVerificarPersona = $this->con->query($sqlVerificarPersona);

		if ($resultadoVerificarPersona->num_rows > 0) {
			// La persona ya existe, obtener su ID
			$filaPersona = $resultadoVerificarPersona->fetch_assoc();
			$idUsuario = $filaPersona['IdUsuario'];
		} else {
			$sqlInsertarPersona = "INSERT INTO usuarios (Nombre,Password,Rol,Foto)
						VALUES ('$this->Usuario', '123', '2','$this->Foto')";

			if ($this->con->query($sqlInsertarPersona)) {
				// Obtener el ID de la persona recién insertada
				$idUsuario = $this->con->insert_id;

			}
		}
		echo $idUsuario;
		//$this->MedicoID = $this->set_medico($this->Especialidad);
		$sql = "INSERT INTO medicos (Nombre, Especialidad,IdUsuario)  
													VALUES('$this->NombreMedico',
													'$this->Especialidad',
													'$idUsuario'
														)";
		//echo $sql;
		//exit;
		/*	if($this->con->query($sql)){
													   echo $this->_message_ok("guardó");
												   }else{
													   echo $this->_message_error("guardar");
												   }								
																		   */
		if ($this->con->query($sql)) {
			echo $this->_message_ok_Medico("Guardado exitoso");
		} else {
			echo $this->_message_error_Medico("Error al guardar: " . $this->con->error);
		}
	}
	public function update_medico()
	{
		$this->MedicoID = $_POST['id'];
		$sql_med = "SELECT m.IdMedico,m.Nombre,e.Descripcion,e.Dias,u.IdUsuario as USUARIO,u.Foto,m.Especialidad,u.Nombre,u.Password,u.Rol
		FROM medicos m,especialidades e,usuarios u WHERE IdMedico=$this->MedicoID AND m.Especialidad=e.IdEsp AND u.IdUsuario =m.IdUsuario;
		";
		$res = $this->con->query($sql_med);
		$row = $res->fetch_assoc();


		$this->NombreMedico = $row['Nombre'];
		$this->Especialidad = $row['Especialidad'];
		$this->UsuarioId = $row['USUARIO'];
		$this->Usuario = $row['Nombre'];
		$this->Password = $row['Password'];
		$this->rol = $row['Rol'];
		$this->Foto = $_FILES['foto']['name'];

		$path = PATH . $this->Foto;


		if (!move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}




		//$this->MedicoID = $this->set_medico($this->Especialidad);

		$sql = "UPDATE medicos SET 
					Nombre='$this->NombreMedico',
					Especialidad='$this->Especialidad',
					IdUsuario='$this->UsuarioId'
					WHERE IdMedico=$this->MedicoID";

		$sql_user = "UPDATE usuarios SET 
					Nombre='$this->Usuario',
					Password='$this->Password',
					Rol='$this->rol',
					Foto='$this->Foto'
					WHERE IdUsuario=$this->UsuarioId";
		$row = $this->con->query($sql_user);
		//echo $sql;
		//exit;
		/*	if($this->con->query($sql)){
													   echo $this->_message_ok("modificó");
												   }else{
													   echo $this->_message_error("al modificar");
												   }		*/
		if ($this->con->query($sql)) {

			echo $this->_message_ok_Medico("Guardado exitoso");
		} else {

			echo $this->_message_error_Medico("Error al guardar: " . $this->con->error);

		}

	}

	//*****************************************************************************************	
	public function delete_medico($id)
	{
		//Eliminar el medico que se uso
		$sqluser = "SELECT IdUsuario FROM medicos WHERE IdMedico=$id;";
		$res = $this->con->query($sqluser);
		$row = $res->fetch_assoc();
		$User = $row['IdUsuario'];

		$sqldelete = "DELETE FROM usuarios WHERE IdUsuario=$User";
		$this->con->query($sqldelete);
		$sql = "DELETE FROM medicos WHERE IdMedico=$id;";
		echo $sql;
		if ($this->con->query($sql)) {
			$html = '<div class="container mt-5">
				<!-- Alerta de éxito -->
				<div class="alert alert-success" role="alert">
				  ¡Operación exitosa!
				</div>
				</div>';
			echo $html;
			echo $this->_message_ok_Medico("ELIMINÓ");
		} else {
			$html = '<div class="container mt-5">
				<!-- Alerta de éxito -->
				<div class="alert alert-danger" role="alert">
				¡Error! El Medico tiene consultas o recetas activas.
			  </div>
				</div>';
			echo $html;
			echo $this->_message_error_Medico("eliminar");
		}
	}


	//*************************************************************************	

	private function _message_error_Medico($tipo)
	{
		$Medicos = "Med/1";
		$d_Medicos_final = base64_encode($Medicos);
		$html = '

		<div class="container text-center">
		<div class="alert alert-danger" role="alert">
		Error al ' . $tipo . '. Favor contactar a .................... 		<br>
		 <a class="btn btn-info" href="./content.php?d=' . $d_Medicos_final . '">Regresar</a>
		</div>
		
	</div>
			 ';
		return $html;
	}


	private function _message_ok_Medico($tipo)
	{
		$Medicos = "Med/1";
		$d_Medicos_final = base64_encode($Medicos);
		$html = '
		<div class="container text-center">
		<div class="alert alert-success" role="alert">
		El registro se  ' . $tipo . ' correctamente
				 <a class="btn btn-info" href="./content.php?d=' . $d_Medicos_final . '">Regresar</a>
		</div>
		
	</div>
		';
		return $html;
	}

	//****************************************************************************	


	//*************************************Pacientes	
	public function get_list_pacientesUnico()
	{
		$valor = $_SESSION['secion'];
		// Separar el valor en dos partes usando explode
		list($usuarioSecion, $CrudRoles) = explode('/', $valor);

		$d_new = "newpas/0";
		$d_newpas_final = base64_encode($d_new);
		$pat = PATH;
		$html = '
			<div class="text-center">
			<table class="table table-bordered""   align="center">
				<tr class="table-dark">
					<th colspan="8">Datos del paciente ' . $usuarioSecion . ' </th>
				</tr>
				 
				<tr class="table-dark">
					<th>Nombre</th>
					<th>Genero</th>
					<th>Edad</th>
					<th colspan="2">Acciones</th> 

					
				 
				</tr>';
		$sql = "SELECT IdPaciente,Nombre,Cedula,Genero,Edad,Peso,Estatura
		FROM pacientes
		WHERE IdUsuario = (SELECT IdUsuario FROM usuarios WHERE Nombre = '$usuarioSecion');
				";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		$d_del = "delpas/" . $row['IdPaciente'];
		$d_delpas_final = base64_encode($d_del);
		$d_act = "actpas/" . $row['IdPaciente'];
		$d_actpas_final = base64_encode($d_act);
		$d_det = "detpas/" . $row['IdPaciente'];
		$d_detpas_final = base64_encode($d_det);
		$html .= '
				<tr>
					
					<td>' . $row['Nombre'] . '</td>
					<td>' . $row['Genero'] . '</td>
					<td>' . $row['Edad'] . '</td>
					
				
 							<td><a href="./content.php?d=' . $d_actpas_final . '"><button class="btn btn-success">Actualizar</button></a></td>
							<td><a href="./content.php?d=' . $d_detpas_final . '"><button class="btn btn-info">Detalle</button></a></td>
						</tr>';
		// Sin codificar <td><a href="content.php?op=del&id=' . $row['id'] . '">Borrar</a></td>

		$html .= '  
					</table>



					<a href="./logout.php"><button class="btn btn-primary" >HOME</button></a>
					<div>';

		return $html;

	}

	public function get_list_pacientes()
	{


		$d_new = "newpas/0";
		$d_newpas_final = base64_encode($d_new);
		$pat = PATH;
		$html = '
			<div class="text-center">
			<table class="table""   align="center">
				<tr>
					<th colspan="8"class="table-dark">Lista de Pacientes</th>
				</tr>
				<tr>
					<th colspan="8" class="table-dark"><a class="btn btn-primary" href="./content.php?d=' . $d_newpas_final . '">Nuevo</a></th>
				</tr>
				<tr class="table-dark">
					<th>Nombre</th>
					<th>Genero</th>
					<th>Peso</th>
					
			 
					<th colspan="3">Acciones</th>
				</tr>';
		$sql = "SELECT IdPaciente,Nombre,Cedula,Genero,Edad,Peso,Estatura
				FROM pacientes
				";
		$res = $this->con->query($sql);


		// Sin codificar <td><a href="content.php?op=del&id=' . $row['id'] . '">Borrar</a></td>
		while ($row = $res->fetch_assoc()) {
			$d_del = "delpas/" . $row['IdPaciente'];
			$d_delpas_final = base64_encode($d_del);
			$d_act = "actpas/" . $row['IdPaciente'];
			$d_actpas_final = base64_encode($d_act);
			$d_det = "detpas/" . $row['IdPaciente'];
			$d_detpas_final = base64_encode($d_det);
			$html .= '
					<tr>
						
						<td>' . $row['Nombre'] . '</td>
						<td>' . $row['Genero'] . '</td>
						<td>' . $row['Edad'] . '</td>
						
					
							<td><a href="./content.php?d=' . $d_delpas_final . '"><button class="btn btn-danger">Borrar</button></a></td>
								<td><a href="./content.php?d=' . $d_actpas_final . '"><button class="btn btn-success">Actualizar</button></a></td>
								<td><a href="./content.php?d=' . $d_detpas_final . '"><button class="btn btn-info">Detalle</button></a></td>
							</tr>';
		}
		$html .= '  
		<tr class="table-dark"> 
					
			 
					<th colspan="6"><a href="./logout.php"><button class="btn btn-primary" >HOME</button></a>
					</th>
				</tr>
					</table>


				
 					<div>';

		return $html;

	}
	public function get_detail_pacientes($id)
	{
		$valor = $_SESSION['secion'];
		// Separar el valor en dos partes usando explode
		list($usuarioSecion, $CrudRoles) = explode('/', $valor);



		$PacienteUnico = "paciUnico/1";
		$d_PacienteUnico_final = base64_encode($PacienteUnico);
		$Paciente = "paci/1";
		$d_Paciente_final = base64_encode($Paciente);
		$sql = "SELECT p.IdPaciente, p.Nombre, p.Genero, p.Edad, u.Nombre AS NombreUsuario, u.Foto, p.Cedula, p.Estatura AS estatura, p.Peso AS peso
		FROM pacientes p
		JOIN usuarios u ON p.IdUsuario = u.IdUsuario
		WHERE p.IdPaciente = $id;";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		$pat = PATH;
		$num = $res->num_rows;

		//Si es que no existiese ningun registro debe desplegar un mensaje 
		//$mensaje = "tratar de eliminar el vehiculo con id= ".$id;
		//echo $this->_message_error($mensaje);
		//y no debe desplegarse la tablas

		if ($num == 0) {
			$mensaje = "tratar de editar el vehiculo con id= " . $id;
			echo $this->_message_error($mensaje);
		} else {
			$html = '
					<div class="container mt-4">
			<div class="row justify-content-center">
			<div class="col-md-6">
				<table class="table table-bordered"   align="center">
							<tr>
								<th class="table table-dark text-center" colspan="2">DATOS DEL PACIENTE   </th>
							</tr>
							<tr>
								<td>Cedula: </td>
								<td>' . $row['Cedula'] . '</td>
							</tr>
							<tr>
								<td>Nombre: </td>
								<td>' . $row['Nombre'] . '</td>
							</tr>
							<tr>
								<td>Edad: </td>
								<td>' . $row['Edad'] . ' años</td>
							</tr>
							<tr>
								<td>Genero: </td>
								<td>' . $row['Genero'] . '</td>
							</tr>
							<tr>
								<td>Peso: </td>
								<td>' . $row['peso'] . '</td>
							</tr>
							<tr>
								<td>Estatura: </td>
								<td>' . $row['estatura'] . '</td>
							</tr>
							<tr>
							<th colspan="2"><img src="' . $pat . $row['Foto'] . '" width="300px"/></th>
							</tr>';


			if ($usuarioSecion == $row['NombreUsuario']) {

				$html .= '<th colspan="2" class="text-center table table-dark">
										<a href="./content.php?d=' . $d_PacienteUnico_final . '">
											<button class="btn btn-primary mx-auto">Regresar</button>
										</a>
									</th>
								</tr>
							</table>
							</div>
							</div>
							</div>';
			} else {
				$html .= '<th colspan="2" class="text-center table table-dark">
										<a href="./content.php?d=' . $d_Paciente_final . '">
											<button class="btn btn-primary mx-auto">Regresar</button>
										</a>
									</th>
								</tr>
							</table>
							</div>
							</div>
							</div>';
			}

			return $html;
		}
	}
	public function get_form_pacientes($PacienteID = NULL)
	{
		$valor = $_SESSION['secion'];
		// Separar el valor en dos partes usando explode
		list($usuarioSecion, $CrudRoles) = explode('/', $valor);



		$PacienteUnico = "paciUnico/1";
		$d_PacienteUnico_final = base64_encode($PacienteUnico);

		$Paciente = "paci/1";
		$d_Paciente_final = base64_encode($Paciente);
		
		if ($PacienteID == NULL) {
			$this->NombrePaciente = NULL;
			$this->Edad = NULL;
			$this->Genero = NULL;
			$this->peso = NULL;
			$this->estatura = NULL;
			$this->Usuario = NULL;
			$flag = NULL;
			$op = "newPaciente";
			$Norchange = "enable";
			//$control=1;

		} else {


			$sql = "SELECT p.IdPaciente,p.Nombre,p.Genero,p.Edad,u.Foto,p.Cedula,p.Peso,p.Estatura,u.Nombre as Usser
				FROM pacientes p,usuarios u WHERE IdPaciente=$PacienteID AND p.IdUsuario =u.IdUsuario;
				";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();

			$num = $res->num_rows;
			if ($num == 0) {
				$mensaje = "tratar de actualizar el vehiculo con id= " . $PacienteID;
				echo $this->_message_error_Paciente($mensaje);
			} else {

				// ***** TUPLA ENCONTRADA *****
				// echo "<br>TUPLA <br>";
				// echo "<pre>";
				// 	print_r($row);
				// echo "</pre>";
				$this->NombrePaciente = $row['Nombre'];
				$this->PacienteID = $row['IdPaciente'];
				$this->Edad = $row['Edad'];
				$this->Genero = $row['Genero'];
				$this->peso = $row['Peso'];
				$this->Cedula = $row['Cedula'];
				$this->estatura = $row['Estatura'];
				$this->Usuario = $row['Usser'];



				$flag = "enable";
				$op = "updatePaciente";
				$Norchange = "enable";
				//$control=0;
			}
		}
		$pat = PATH;
		$Generos = ["Masculino",
			"Femenino"
		];

		$html = '
			<div class="container mt-4 ">
				<div class="row justify-content-center">
					<div class="col-md-6 ">
						<form name="vehiculo" method="POST" action="content.php" enctype="multipart/form-data"">
						
						<input type="hidden" name="id" value="' . $PacienteID . '">
						<input type="hidden" name="op" value="' . $op . '">
						
						
						<table class="table table-bordered ">
								<tr>
									<th class="text-center table table-dark" colspan="2">DATOS PACIENTE</th>
								</tr>
								<tr>
								<tr>
								<td>Nombre:</td>
								<td><input type="text" class="form-control" name="Paciente" value="' . $this->NombrePaciente . '" required ' . $Norchange . '></td>
							</tr>
								<tr>
								<td>Edad:</td>
            					<td><input type="number" class="form-control" name="Edad" value="' . $this->Edad . '" required ' . $Norchange . '></td>
							</tr>
							<tr>
								<td>Estatura:</td>
            					<td><input type="number" class="form-control" name="estatura" value="' . $this->estatura . '" required ' . $Norchange . '></td>
							</tr>
							<tr>
								<td>Peso:</td>
            					<td><input type="number" class="form-control" name="peso" value="' . $this->peso . '" required ' . $Norchange . '></td>
							</tr>
							<tr>
								<td>Genero:</td>
								<td>' . $this->_get_radio($Generos, "Genero", $this->Genero, $Norchange) . '</td>
							</tr>
							<tr>
							<td>Cedula:</td>
						 	<td> <input type="text" class="form-control" id="cedula" name="cedula" pattern="[0-9]{10}" title="La cédula debe tener 10 dígitos numéricos" value="' . $this->Cedula . '"required ' . $Norchange . '></d>
							</tr>
							<tr>
							<td>Usuario:</td>
							<td><input type="text" class="form-control" name="User" value="' . $this->Usuario . '" required ' . $Norchange . '></td>
							</tr>
							<tr>
								<td>Foto:</td>
								<td><input type="file" name="foto" value=" ' . $this->Foto . '"' . $flag . '></td>
							</tr>
									
								<tr>
								<th colspan="2" class="text-center table-dark"><input type="submit" name="Guardar" class="btn btn-success"value="GUARDAR"></th>
								</tr>
								
								
							</table>
							</form>

							
							 ';
		if ($usuarioSecion == $this->Usuario) {

			$html .= '
			
 			
			<a href="./content.php?d=' . $d_PacienteUnico_final . '"><button class="btn btn-primary  mx-auto" >REGRESAR</button></a>
							</div>
							</div>
						</div>';
		} else {
			$html .= '<a href="./content.php?d=' . $d_Paciente_final . '"><button class="btn btn-primary  mx-auto" >REGRESAR</button></a>
							</div>
							</div>
						</div>';
		}


		return $html;
	}
	public function save_pacientes()
	{

		$this->NombrePaciente = $_POST['Paciente'];
		$this->Genero = $_POST['Genero'];
		$this->Edad = $_POST['Edad'];
		$this->peso = $_POST['peso'];
		$this->Cedula = $_POST['cedula'];
		$this->estatura = $_POST['estatura'];
		$this->Foto = $_FILES['foto']['name'];
		$path = PATH . $this->Foto;
		$this->Usuario = $_POST['User'];





		if (!move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}

		$sqlVerificarPersona = "SELECT IdUsuario FROM usuarios WHERE  Nombre= '$this->Usuario'";
		$resultadoVerificarPersona = $this->con->query($sqlVerificarPersona);

		if ($resultadoVerificarPersona->num_rows > 0) {
			// La persona ya existe, obtener su ID
			$filaPersona = $resultadoVerificarPersona->fetch_assoc();
			$idUsuario = $filaPersona['IdUsuario'];
		} else {
			$sqlInsertarPersona = "INSERT INTO usuarios (Nombre,Password,Rol,Foto)
						VALUES ('$this->Usuario', '123', '3','$this->Foto')";

			if ($this->con->query($sqlInsertarPersona)) {
				// Obtener el ID de la persona recién insertada
				$idUsuario = $this->con->insert_id;

			}
		}
		echo $idUsuario;

		$sql = "INSERT INTO pacientes (IdUsuario,Nombre,Cedula,Edad,Genero,Estatura,Peso)  
											VALUES('$idUsuario',
												'$this->NombrePaciente',
												'$this->Cedula',
												'$this->Edad',
												'$this->Genero',
												'$this->estatura',
												'$this->peso'
												)";
		//echo $sql;
		//exit;
		/*	if($this->con->query($sql)){
												 echo $this->_message_ok("guardó");
											 }else{
												 echo $this->_message_error("guardar");
											 }								
																	 */
		if ($this->con->query($sql)) {
			echo $this->_message_ok_Paciente("Guardado exitoso");
		} else {
			echo $this->_message_error_Paciente("Error al guardar: " . $this->con->error);
		}
	}
	public function update_pacientes()
	{
		$this->PacienteID = $_POST['id'];

		$sql_pas = "SELECT p.IdPaciente,p.Nombre,p.Genero,p.Edad,u.Foto,p.Cedula,p.Peso,p.Estatura,u.IdUsuario,u.Nombre as USUARIO,u.Password,u.Rol,u.IdUsuario
				FROM pacientes p,usuarios u WHERE IdPaciente=$this->PacienteID AND p.IdUsuario=u.IdUsuario;
				";
		$res = $this->con->query($sql_pas);
		$row = $res->fetch_assoc();


		$this->NombrePaciente = $row['Nombre'];
		$this->PacienteID = $row['IdPaciente'];
		$this->Edad = $row['Edad'];
		$this->Genero = $row['Genero'];
		$this->peso = $row['Peso'];
		$this->Cedula = $row['Cedula'];
		$this->estatura = $row['Estatura'];
		$this->Usuario = $row['USUARIO'];
		$this->UsuarioId = $row['IdUsuario'];
		$this->Password = $row['Password'];
		$this->rol = $row['Rol'];
		$this->Foto = $_FILES['foto']['name'];

		$path = PATH . $this->Foto;


		if (!move_uploaded_file($_FILES['foto']['tmp_name'], $path)) {
			$mensaje = "Cargar la imagen";
			echo $this->_message_error($mensaje);
			exit;
		}



		//$this->MedicoID = $this->set_medico($this->Especialidad);

		$sql = "UPDATE pacientes SET
			IdUsuario='$this->UsuarioId',
			Nombre='$this->NombrePaciente',
			Cedula='$this->Cedula',
			Edad='$this->Edad',
			Genero='$this->Genero',
			Estatura='$this->estatura',
			Peso='$this->peso'
			WHERE IdPaciente=$this->PacienteID";

		$sql_user = "UPDATE usuarios SET 
			Nombre='$this->Usuario',
			Password='$this->Password',
			Rol='$this->rol',
			Foto='$this->Foto'
			WHERE IdUsuario=$this->UsuarioId";
		$row = $this->con->query($sql_user);

		//echo $sql;
		//exit;
		/*	if($this->con->query($sql)){
												 echo $this->_message_ok("modificó");
											 }else{
												 echo $this->_message_error("al modificar");
											 }		*/
		if ($this->con->query($sql)) {

			echo $this->_message_ok_Paciente("Guardado exitoso");
		} else {

			echo $this->_message_error_Paciente("Error al guardar: " . $this->con->error);

		}

	}

	private function _message_error_Paciente($tipo)
	{
		$Paciente = "paci/1";
		$d_Paciente_final = base64_encode($Paciente);
		$html = '
		<div class="container text-center">
		<div class="alert alert-danger" role="alert">
		Error al ' . $tipo . '. Favor contactar a .................... 
		<br>
		<br>
		 <a class="btn btn-info" href="./content.php ">Regresar</a>
		</div>
		
	</div>

				 ';
		return $html;
	}


	private function _message_ok_Paciente($tipo)
	{
		$Paciente = "paci/1";
		$d_Paciente_final = base64_encode($Paciente);
		$html = '
		<div class="container text-center">
		<div class="alert alert-succes" role="alert">
		El registro se  ' . $tipo . ' correctamente		<br>
		<br>
		 <a class="btn btn-info" href="./content.php ">Regresar</a>
		</div>
		
	</div>
				';
		return $html;
	}
	public function delete_Paciente($id)
	{
		$sqluser = "SELECT IdUsuario FROM pacientes WHERE IdPaciente=$id;";
		$res = $this->con->query($sqluser);
		$row = $res->fetch_assoc();
		$User = $row['IdUsuario'];

		$sqldelete = "DELETE FROM usuarios WHERE IdUsuario=$User";
		$this->con->query($sqldelete);
		//Eliminar el paciente indicado
		$sql = "DELETE FROM pacientes WHERE IdPaciente=$id;";
		if ($this->con->query($sql)) {
			$html = '<div class="container mt-5">
				<!-- Alerta de éxito -->
				<div class="alert alert-success" role="alert">
				  ¡Operación exitosa!
				</div>
				</div>';
			echo $html;
			echo $this->_message_ok_Paciente("ELIMINÓ");
		} else {
			$html = '<div class="container mt-5">
				<!-- Alerta de éxito -->
				<div class="alert alert-danger" role="alert">
				¡Error! El paciente tiene consultas.
			  </div>
				</div>';
			echo $html;
			echo $this->_message_error_Paciente("eliminar");
		}
	}





	//****************************************************************************	



	//*************************************Medicina	
	public function get_list_medicamentos()
	{
		$d_new = "newmedicamento/0";
		$d_newmedicamento_final = base64_encode($d_new);
		$pat = PATH;
		$html = '
			<div class="text-center">
			<table class="table "  >
				<tr>
					<th class="table table-dark" colspan="8">Lista de Medicamentos</th>
				</tr>
				<tr>
					<th class="table table-dark" colspan="8"><a class="btn btn-primary" href="./content.php?d=' . $d_newmedicamento_final . '">Nuevo</a></th>
				</tr>
				<tr class="table table-dark">
					<th>Nombre</th>
					<th>Tipo</th>
					<th colspan="3">Acciones</th>
				</tr>';
		$sql = "SELECT IdMedicamento,Nombre,Tipo
				FROM medicamentos";
		$res = $this->con->query($sql);


		// Sin codificar <td><a href="content.php?op=del&id=' . $row['id'] . '">Borrar</a></td>
		while ($row = $res->fetch_assoc()) {
			$d_delmedicamento = "delmedicamentos/" . $row['IdMedicamento'];
			$d_delmedicamento_final = base64_encode($d_delmedicamento);
			$d_actmedicamento = "actmedicamentos/" . $row['IdMedicamento'];
			$d_actmedicamento_final = base64_encode($d_actmedicamento);
			$d_detmedicamento = "detmedicamentos/" . $row['IdMedicamento'];
			$d_detmedicamento_final = base64_encode($d_detmedicamento);
			$html .= '
					<tr>
						
						<td>' . $row['Nombre'] . '</td>
						<td>' . $row['Tipo'] . '</td>
						<td><a href="./content.php?d=' . $d_delmedicamento_final . '"><button class="btn btn-danger">Borrar</button></a></td>
						<td><a href="./content.php?d=' . $d_actmedicamento_final . '"><button class="btn btn-success">Actualizar</button></a></td>
						<td><a href="./content.php?d=' . $d_detmedicamento_final . '"><button class="btn btn-info">Detalle</button></a></td>
							</tr>';
		}
		$html .= '  
		<tr class="table table-dark"> 
					<th colspan="5">
					<a href="./logout.php"><button class="btn btn-primary" >HOME</button></a>

					</th>
				</tr>
					</table> 
 					<div>';

		return $html;

	}
	public function get_detail_medicamentos($id)
	{
		$Medicamento = "medic/1";
		$d_Medicamento_final = base64_encode($Medicamento);
		$sql = "SELECT  IdMedicamento,Nombre,Tipo
			FROM medicamentos WHERE  IdMedicamento=$id
			";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		$pat = PATH;
		$num = $res->num_rows;

		//Si es que no existiese ningun registro debe desplegar un mensaje 
		//$mensaje = "tratar de eliminar el vehiculo con id= ".$id;
		//echo $this->_message_error($mensaje);
		//y no debe desplegarse la tablas

		if ($num == 0) {
			$mensaje = "tratar de editar el vehiculo con id= " . $id;
			echo $this->_message_error($mensaje);
		} else {
			$html = '
					<div class="container mt-4">
			<div class="row justify-content-center">
			<div class="col-md-6">
				<table class="table table-bordered"   align="center">
							<tr>
								<th class="table table-dark text-center" colspan="2">DETSLLE DEL MEDICAMENTO</th>
							</tr>
							<tr>
								<td>ID: </td>
								<td>' . $row['IdMedicamento'] . '</td>
							</tr>
							<tr>
								<td>Nombre: </td>
								<td>' . $row['Nombre'] . '</td>
							</tr>
							<tr>
								<td>Efecto: </td>
								<td>' . $row['Tipo'] . '</td>
							</tr>
						<th colspan="2" class="text-center table table-dark"><a href="./content.php?d=' . $d_Medicamento_final . '"><button class="btn btn-primary mx-auto" >Regresar</button></a>
						<div></th>
					</tr>																		</table>
					</div>
			</div>
			</div>';

			return $html;
		}
	}
	public function get_form_medicamentos($MedicamentosID = NULL)
	{
		$Medicamento = "medic/1";
		$d_Medicamento_final = base64_encode($Medicamento);
		if ($MedicamentosID == NULL) {
			$this->NombreMedicamento = NULL;
			$this->Tipo = NULL;
			$flag = NULL;
			$op = "newMedicamento";
			//$control=1;

		} else {


			$sql = "SELECT IdMedicamento,Nombre,Tipo
				FROM medicamentos
				WHERE  IdMedicamento = $MedicamentosID;
				";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();

			$num = $res->num_rows;
			if ($num == 0) {
				$mensaje = "tratar de actualizar el vehiculo con id= " . $MedicamentosID;
				echo $this->_message_error_Medico($mensaje);
			} else {

				// ***** TUPLA ENCONTRADA *****
				// echo "<br>TUPLA <br>";
				// echo "<pre>";
				// 	print_r($row);
				// echo "</pre>";
				$this->NombreMedicamento = $row['Nombre'];
				$this->Tipo = $row['Tipo'];




				$flag = "enable";
				$op = "updateMedicamento";
				//$control=0;
			}
		}



		$html = '
			<div class="container mt-4">
				<div class="row justify-content-center">
					<div class="col-md-6">
						<form name="vehiculo" method="POST" action="content.php" enctype="multipart/form-data"">
						
						<input type="hidden" name="id" value="' . $MedicamentosID . '">
						<input type="hidden" name="op" value="' . $op . '">
						
						
						<table class="table table-bordered" ">
								<tr>
									<th class="table table-dark text-center" colspan="2">DATOS MEDICO</th>
								</tr>
								<tr>
								<tr>
								<td>Nombre:</td>
								<td><input type="text" class="form-control" name="Medicamento" value="' . $this->NombreMedicamento . '" required></td>
							</tr>
								<tr>
								<td>Efecto:</td>
								<td><input type="text" class="form-control" name="Tipo" value="' . $this->Tipo . '" required></td>
							</tr>
									
								<tr>
								<th colspan="2" class="text-center table table-dark"><input type="submit" class="btn btn-success" name="Guardar" value="GUARDAR"></th>
								</tr>												
							</table>
							</form>
							<a href="./content.php?d=' . $d_Medicamento_final . '"><button class="btn btn-primary  mx-auto" >REGRESAR</button></a>
							</div>
							</div>
						</div>
					
						
						
						
						';
		return $html;
	}

	public function save_Medicamentos()
	{
		$this->NombreMedicamento = $_POST['Medicamento'];
		$this->Tipo = $_POST['Tipo'];


		/*	$this->Foto = $_FILES['foto']['name'];
												   
												   $path = PATH . $this->Foto;
												   /*
														   echo "<br> FILES <br>";
														   echo "<pre>";
															   print_r($_FILES);
														   echo "</pre>";
													   
												   
												   exit;
												   if(!move_uploaded_file($_FILES['foto']['tmp_name'],$path)){
													   $mensaje = "Cargar la imagen";
													   echo $this->_message_error($mensaje);
													   exit;
												   }
							   */
		//$this->MedicoID = $this->set_medico($this->Especialidad);


		$sql = "INSERT INTO medicamentos (Nombre,tipo)  
											VALUES('$this->NombreMedicamento',
												'$this->Tipo'
												)";
		//echo $sql;
		//exit;
		/*	if($this->con->query($sql)){
												 echo $this->_message_ok("guardó");
											 }else{
												 echo $this->_message_error("guardar");
											 }								
																	 */
		if ($this->con->query($sql)) {
			echo $this->_message_ok_Medicamentos("Guardado exitoso");
		} else {
			echo $this->_message_error_Medicamentos("Error al guardar: " . $this->con->error);
		}
	}
	public function update_Medicamentos()
	{

		$this->NombreMedicamento = $_POST['Medicamento'];
		$this->Tipo = $_POST['Tipo'];
		$this->MedicamentosID = $_POST['id'];




		//$this->MedicoID = $this->set_medico($this->Especialidad);

		$sql = "UPDATE medicamentos SET 
			Nombre='$this->NombreMedicamento',
			Tipo='$this->Tipo'
			WHERE  IdMedicamento=$this->MedicamentosID";
		//echo $sql;
		//exit;
		/*	if($this->con->query($sql)){
												 echo $this->_message_ok("modificó");
											 }else{
												 echo $this->_message_error("al modificar");
											 }		*/
		if ($this->con->query($sql)) {

			echo $this->_message_ok_Medicamentos("Guardado exitoso");
		} else {

			echo $this->_message_error_Medicamentos("Error al guardar: " . $this->con->error);

		}

	}



	private function _message_error_Medicamentos($tipo)
	{
		$Medicamento = "medic/1";
		$d_Medicamento_final = base64_encode($Medicamento);
		$html = '
		<div class="container text-center">
		<div class="alert alert-danger" role="alert">
		Error al ' . $tipo . '. Favor contactar a .................... 
		<br>
		<br>
		 <a class="btn btn-info" href="./content.php?d=' . $d_Medicamento_final . '">Regresar</a> 
		</div>
		
	</div>

				 ';
		return $html;
	}


	private function _message_ok_Medicamentos($tipo)
	{
		$Medicamento = "medic/1";
		$d_Medicamento_final = base64_encode($Medicamento);
		$html = '

		<div class="container text-center">
		<div class="alert alert-succes" role="alert">
		El registro se  ' . $tipo . ' correctamente		<br>
		<br>
		 <a class="btn btn-info" href="./content.php?d=' . $d_Medicamento_final . '">Regresar</a> 
		</div>
		
	</div> ';
		return $html;
	}
	public function delete_Medicamentos($id)
	{


		//Eliminar el medico que se uso
		$sql = "DELETE FROM medicamentos WHERE IdMedicamento=$id;";
		if ($this->con->query($sql)) {
			echo $this->_message_ok_Medicamentos("ELIMINÓ");
			$html = '<div class="container mt-5">
					<!-- Alerta de éxito -->
					<div class="alert alert-success" role="alert">
					¡Operación exitosa!
					</div>
					</div>';
			echo $html;
		} else {
			$html = '<div class="container mt-5">
					<!-- Alerta de éxito -->
					<div class="alert alert-danger" role="alert">
					¡Error! La medicina tiene recetas que la nececitan actualmente.
				</div>
					</div>';
			echo $html;
			echo $this->_message_error_Medicamentos("eliminar");
		}
	}


	//****************************************************************************	


	//*************************************Recetas
	public function get_list_recetas()
	{
		$d_new = "newreceta/0";
		$d_newreceta_final = base64_encode($d_new);
		$pat = PATH;
		$html = '
			<div class="text-center">
			<table class="table""   align="center">
				<tr class="table-dark"> 
					<th colspan="8">Recetas </th>
				</tr>
				<tr class="table-dark">
					<th colspan="8"><a class="btn btn-primary" href="./content.php?d=' . $d_newreceta_final . '">Nuevo</a></th>
				</tr>
				<tr class="table-dark">
					<th>Doctor</th>
					<th>Descripcion</th>
					<th>Medicamento</th>
					<th>Cantidad</th>
					<th colspan="3">Acciones</th>
				</tr>';
		$sql = "SELECT m.Nombre as medico, c.Diagnostico, e.Nombre as medicina, r.Cantidad,r.IdReceta
						FROM consultas c, medicos m, medicamentos e, recetas r
						WHERE r.IdConsulta= c.IdConsulta AND r.IdMedicamento = e.IdMedicamento  AND c.IdMedico = m.IdMedico  ;";
		$res = $this->con->query($sql);


		// Sin codificar <td><a href="content.php?op=del&id=' . $row['id'] . '">Borrar</a></td>
		while ($row = $res->fetch_assoc()) {
			$d_delreceta = "delreceta/" . $row['IdReceta'];
			$d_delreceta_final = base64_encode($d_delreceta);
			$d_actreceta = "actreceta/" . $row['IdReceta'];
			$d_actreceta_final = base64_encode($d_actreceta);
			$d_detreceta = "detreceta/" . $row['IdReceta'];
			$d_detreceta_final = base64_encode($d_detreceta);
			$html .= '
					<tr>
						
						<td>' . $row['medico'] . '</td>
						<td>' . $row['Diagnostico'] . '</td>
						<td>' . $row['medicina'] . '</td>
						<td>' . $row['Cantidad'] . '</td>
						<td><a href="./content.php?d=' . $d_delreceta_final . '"><button class="btn btn-danger">Borrar</button></a></td>
						<td><a href="./content.php?d=' . $d_actreceta_final . '"><button class="btn btn-success">Actualizar</button></a></td>
						<td><a href="./content.php?d=' . $d_detreceta_final . '"><button class="btn btn-info">Detalle</button></a></td>
							</tr>';
		}
		$html .= '  

		<tr class="table-dark">
					 
					<th colspan="7"><a href="./logout.php"><button class="btn btn-primary" >HOME</button></a>
					</th>
				</tr>
					</table>



 					<div>';

		return $html;

	}
	public function get_detail_recetas($id)
	{
		$recetas = "res/1";
		$d_recetas_final = base64_encode($recetas);

		$sql = "SELECT c.IdConsulta, m.Nombre as medico, c.Diagnostico,p.Nombre as paciente ,e.Nombre as medicina, r.Cantidad ,r.IdReceta, m.Especialidad,r.IdReceta
			FROM consultas c, medicos m, medicamentos e, recetas r, pacientes p
			WHERE r.IdConsulta= c.IdConsulta AND r.IdMedicamento = e.IdMedicamento AND c.IdMedico = m.IdMedico AND c.IdPaciente = p.IdPaciente AND r.IdReceta=$id;";
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		$pat = PATH;
		$num = $res->num_rows;

		//Si es que no existiese ningun registro debe desplegar un mensaje 
		//$mensaje = "tratar de eliminar el vehiculo con id= ".$id;
		//echo $this->_message_error($mensaje);
		//y no debe desplegarse la tablas

		if ($num == 0) {
			$mensaje = "tratar de editar el vehiculo con id= " . $id;
			echo $this->_message_error($mensaje);
		} else {
			$html = '
					<div class="container mt-4 text-center">
			<div class="row justify-content-center">
			<div class="col-md-6">
				<table class="table table-bordered"   align="center">
							<tr>
								<th class="table table-dark" colspan="2">Detalle de la receta</th>
							</tr>
							<tr>
								<td>Receta Numero: </td>
								<td>' . $row['IdReceta'] . '</td>
							</tr>
							<tr>
								<td>Doctor: </td>
								<td>' . $row['medico'] . '</td>
							</tr>
							<tr>
								<td>Especialidad: </td>
								<td>' . $row['Especialidad'] . '</td>
							</tr>
							<tr>
								<td>Paciente: </td>
								<td>' . $row['paciente'] . '</td>
							</tr>
							<tr>
								<td>Diagnostico: </td>
								<td>' . $row['Diagnostico'] . '</td>
							</tr>
							<tr>
								<td>Medicamento: </td>
								<td>' . $row['medicina'] . '</td>
							</tr>
							<tr>
								<td>Paciente </td>
								<td>' . $row['paciente'] . '</td>
							</tr>	
							<tr>
								<td>Cantidad </td>
								<td>' . $row['Cantidad'] . '</td>
							</tr>			
							<tr>
						 <th colspan="2" class="table table-dark"><a href="./content.php?d=' . $d_recetas_final . '"><button class="btn btn-primary mx-auto" >Regresar</button></a>
						<div>
						</th>
					</tr>																		</table>
					</div>
			</div>
			</div>';

			return $html;
		}
	}
	public function get_form_recetas($RecetasID = NULL)
	{

		$recetas = "res/1";
		$d_recetas_final = base64_encode($recetas);
		if ($RecetasID == NULL) {

			$this->ConsultaID = NULL;
			$this->MedicamentosID = NULL;
			$this->Cantidad = NULL;
			$flag = NULL;
			$op = "newRecetas";
			$control = 1;

		} else {


			$sql = "SELECT c.IdConsulta, m.Nombre as medico, c.Diagnostico,p.Nombre as paciente ,e.Nombre as medicina, r.Cantidad ,r.IdReceta, m.Especialidad,r.IdReceta,e.IdMedicamento
			FROM consultas c, medicos m, medicamentos e, recetas r, pacientes p
			WHERE r.IdConsulta= c.IdConsulta AND r.IdMedicamento = e.IdMedicamento AND c.IdMedico = m.IdMedico AND c.IdPaciente = p.IdPaciente AND r.IdReceta=$RecetasID;";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();

			$num = $res->num_rows;
			if ($num == 0) {
				$mensaje = "tratar de actualizar el vehiculo con id= " . $RecetasID;
				echo $this->_message_error($mensaje);
			} else {

				// ***** TUPLA ENCONTRADA *****
				// echo "<br>TUPLA <br>";
				// echo "<pre>";
				// 	print_r($row);
				// echo "</pre>";
				$this->ConsultaID = $row['IdConsulta'];
				$this->MedicamentosID = $row['IdMedicamento'];
				$this->Cantidad = $row['Cantidad'];


				$flag = "enable";
				$op = "updateRecetas";
				$control = 0;
			}
		}



		$html = '
			<div class="container mt-4">
				<div class="row justify-content-center text-center">
					<div class="col-md-6">
						<form name="vehiculo" method="POST" action="content.php" enctype="multipart/form-data"">
						
						<input type="hidden" name="id" value="' . $RecetasID . '">
						<input type="hidden" name="op" value="' . $op . '">
						
						
						<table class="table table-bordered" ">
								<tr>
									<th class=" text-center table table-dark" colspan="2">RECETAS</th>
								</tr>
								<tr>
							<td>Consulta:</td>
						
							<td>' . $this->_get_combo_db("consultas", "IdConsulta", "FechaConsulta", "IdConsulta", $this->ConsultaID, $flag) . '</td>
						</tr>
								<tr>
									<td>Medicamento:</td>
									<td>' . $this->_get_combo_db("medicamentos", "IdMedicamento", "Nombre", "Nombremed", $this->MedicamentosID, $flag) . '</td>
								</tr>
								
								<tr>
								<td>Cantidad:</td>
								<td><input type="number" class="form-control" name="Cantidad" value="' . $this->Cantidad . '" required></td>
							</tr>
								<tr>
								<th colspan="2" class="text-center table table-dark"><input type="submit" class="btn btn-success" name="Guardar" value="GUARDAR"></th>
								</tr>												
							</table>
							</form>
							<a href="./content.php?d=' . $d_recetas_final . '"><button class="btn btn-primary  mx-auto" >REGRESAR</button></a>
							</div>
							</div>
						</div>
					
						
						
						
						';
		return $html;




	}

	public function update_receta()
	{
		$this->ConsultaID = $_POST['IdConsulta'];

		$this->MedicamentosID = $_POST['Nombremed'];
		$this->Cantidad = $_POST['Cantidad'];
		$this->RecetasID = $_POST['id'];

		$sql = "UPDATE recetas SET 
			IdConsulta='$this->ConsultaID',
			IdMedicamento='$this->MedicamentosID',
			Cantidad='$this->Cantidad'
			WHERE IdReceta=$this->RecetasID";
		//echo $sql;
		//exit;
		/*	if($this->con->query($sql)){
												 echo $this->_message_ok("modificó");
											 }else{
												 echo $this->_message_error("al modificar");
											 }		*/
		if ($this->con->query($sql)) {

			echo $this->_message_ok_recetas("Guardado exitoso");
		} else {

			echo $this->_message_error_recetas("Error al guardar: " . $this->con->error);

		}

	}
	public function save_recetas()
	{
		$this->ConsultaID = $_POST['IdConsulta'];

		$this->MedicamentosID = $_POST['Nombremed'];
		$this->Cantidad = $_POST['Cantidad'];
		$sql = "INSERT INTO recetas (IdConsulta,IdMedicamento,Cantidad)  
											VALUES('$this->ConsultaID',
												'$this->MedicamentosID',
												'$this->Cantidad '
												)";
		//echo $sql;
		//exit;
		/*	if($this->con->query($sql)){
												 echo $this->_message_ok("guardó");
											 }else{
												 echo $this->_message_error("guardar");
											 }								
																	 */
		if ($this->con->query($sql)) {
			echo $this->_message_ok_recetas("Guardado exitoso");
		} else {
			echo $this->_message_error_recetas("Error al guardar: " . $this->con->error);
		}
	}

	private function _message_error_recetas($tipo)
	{
		$recetas = "res/1";
		$d_recetas_final = base64_encode($recetas);
		$html = '

		<div class="container text-center">
			<div class="alert alert-danger" role="alert">
			 Error al ' . $tipo . '. Favor contactar a .................... 
			<br>
			<br>
 			<a class="btn btn-info" href="./content.php?d=' . $d_recetas_final . '">Regresar</a> 
			</div>
			
		</div>
 ';
		return $html;
	}


	private function _message_ok_recetas($tipo)
	{
		$recetas = "res/1";
		$d_recetas_final = base64_encode($recetas);
		$html = '
		<div class="container text-center">
		<div class="alert alert-success" role="alert">
		El registro se  ' . $tipo . ' correctamente
		<br>
		<br>
		 <a class="btn btn-info" href="./content.php?d=' . $d_recetas_final . '">Regresar</a>
		</div>
		
	</div>
 					 ';
		return $html;
	}
	public function delete_recetas($id)
	{
		$sql = "DELETE FROM recetas WHERE IdReceta=$id;";
		if ($this->con->query($sql)) {
			$html = '<div class="container mt-5">
				<!-- Alerta de éxito -->
				<div class="alert alert-success" role="alert">
				  ¡Operación exitosa!
				</div>
				</div>';
			echo $html;
			echo $this->_message_ok_recetas("ELIMINÓ");
		} else {
			echo $this->_message_error_recetas("eliminar");
		}
	}
	public function delete_aux_recetas($id)
	{

		$sql_delete_consultas = "DELETE FROM recetas WHERE MedicamentoID = $id";
		$this->con->query($sql_delete_consultas);

		//Eliminar el medico que se uso


	}



	//****************************************************************************	

	//********************************************Usuarios */



	//***************************************************** */



	//**************************************************** */


	//***********************************Especialidades
	public function save_especialidades()
	{
		$this->NombreESP = $_POST['especialidad'];
		$this->Dias = $_POST['dias'];
		$this->HoraInicio = $_POST['horainicio'];
		$this->HoraFinal = $_POST['horafinal'];
		$sql = "INSERT INTO especialidades (Descripcion,Dias,Franja_HI,franja_HF)  
											VALUES('$this->NombreESP',
												'$this->Dias',
												'$this->HoraInicio',
												'$this->HoraFinal'
												)";
		//echo $sql;
		//exit;
		/*	if($this->con->query($sql)){
												 echo $this->_message_ok("guardó");
											 }else{
												 echo $this->_message_error("guardar");
											 }								
																	 */
		if ($this->con->query($sql)) {
			echo $this->_message_ok_especialidades("Guardado exitoso");
		} else {
			echo $this->_message_error_especialidades("Error al guardar: " . $this->con->error);
		}
	}
	public function update_especialidades()
	{

		$this->NombreESP = $_POST['especialidad'];
		$this->Dias = $_POST['dias'];
		$this->HoraInicio = $_POST['horainicio'];
		$this->HoraFinal = $_POST['horafinal'];
		$this->Especialidad = $_POST['id'];

		$sql = "UPDATE especialidades SET 
			Descripcion='$this->NombreESP',
			Dias='$this->Dias',
			Franja_HI='$this->HoraInicio',
			Franja_HF='$this->HoraFinal'
			WHERE IdEsp=$this->Especialidad";
		//echo $sql;
		//exit;
		/*	if($this->con->query($sql)){
												 echo $this->_message_ok("modificó");
											 }else{
												 echo $this->_message_error("al modificar");
											 }		*/
		if ($this->con->query($sql)) {
			echo $this->_message_ok_especialidades("Guardado exitoso");
		} else {

			echo $this->_message_error_especialidades("Error al guardar: " . $this->con->error);

		}

	}




	public function get_list_especialidades()
	{
		$d_new = "newespe/0";
		$d_newespe_final = base64_encode($d_new);
		$pat = PATH;
		$html = '
					<div class="text-center  ">
					<table class="table  "   align="center">
						<tr class="table table-dark">
							<th   colspan="8">Lista de Especialidades</th>
						</tr>
						<tr class="table table-dark">
							<th colspan="8"><a class="btn btn-primary" href="./content.php?d=' . $d_newespe_final . '">Nuevo</a></th>
						</tr>
						<tr class="table table-dark">
							<th>Especialidad</th>
							<th>Fechas</th>
							<th>Hora Inicio</th>
							<th>Hora Final</th>
							<th colspan="3">Acciones</th>
						</tr>';
		$sql = "SELECT Descripcion,Dias,Franja_HI,Franja_HF,IdEsp
						FROM especialidades";
		$res = $this->con->query($sql);


		// Sin codificar <td><a href="content.php?op=del&id=' . $row['id'] . '">Borrar</a></td>
		while ($row = $res->fetch_assoc()) {
			$d_delespecialidades = "delespecialidades/" . $row['IdEsp'];
			$d_delespecialidades_final = base64_encode($d_delespecialidades);
			$d_actespecialidades = "actespecialidades/" . $row['IdEsp'];
			$d_actespecialidades_final = base64_encode($d_actespecialidades);
			$d_detespecialidades = "detespecialidades/" . $row['IdEsp'];
			$d_detespecialidades_final = base64_encode($d_detespecialidades);
			$html .= '
							<tr>
								
								<td>' . $row['Descripcion'] . '</td>
								<td>' . $row['Dias'] . '</td>

								<td><a href="./content.php?d=' . $d_delespecialidades_final . '"><button class="btn btn-danger">Borrar</button></a></td>
								<td><a href="./content.php?d=' . $d_actespecialidades_final . '"><button class="btn btn-success">Actualizar</button></a></td>
								<td><a href="./content.php?d=' . $d_detespecialidades_final . '"><button class="btn btn-info">Detalle</button></a></td>
									</tr>';
		}
		$html .= '  
		<tr class="table table-dark"> 
							<th colspan="5">
							<a href="./logout.php"><button class="btn btn-primary" >HOME</button></a>
							</th>
						</tr>
							</table>



							 ';

		return $html;

	}


	public function get_detail_especialidades($id)
	{
		$Especialidad = "espe/1";
		$d_Especialidad_final = base64_encode($Especialidad);
		$sql = "SELECT Descripcion,Dias,Franja_HI,Franja_HF
							FROM especialidades
							WHERE IdEsp=$id;"
		;
		$res = $this->con->query($sql);
		$row = $res->fetch_assoc();
		$pat = PATH;
		$num = $res->num_rows;

		//Si es que no existiese ningun registro debe desplegar un mensaje 
		//$mensaje = "tratar de eliminar el vehiculo con id= ".$id;
		//echo $this->_message_error($mensaje);
		//y no debe desplegarse la tablas

		if ($num == 0) {
			$mensaje = "tratar de editar el vehiculo con id= " . $id;
			echo $this->_message_error($mensaje);
		} else {
			$html = '
							<div class="container mt-4">
					<div class="row justify-content-center">
					<div class="col-md-6">
						<table class="table table-bordered"   align="center">
									<tr>
										<th class="table table-dark text-center" colspan="2">DATOS DE LA ESPECIALIDAD</th>
									</tr>
									<tr>
										<td>Descripcion: </td>
										<td>' . $row['Descripcion'] . '</td>
									</tr>
									<tr>
										<td>Dias de atencion: </td>
										<td>' . $row['Dias'] . '</td>
									</tr>
									<tr>
										<td>Franja horaria: </td>
										<td>' . $row['Franja_HI'] . '-' . $row['Franja_HF'] . '</td>
									</tr>
								<th colspan="2" class="text-center table table-dark"><a href="./content.php?d=' . $d_Especialidad_final . '"><button class="btn btn-primary mx-auto" >Regresar</button></a>
								<div></th>
							</tr>																		</table>
							</div>
					</div>
					</div>';

			return $html;
		}
	}
	public function get_form_especialidad($IdEsp = NULL)
	{
		$Especialidad = "espe/1";
		$d_Especialidad_final = base64_encode($Especialidad);
		if ($IdEsp == NULL) {
			$this->NombreMedicamento = NULL;
			$this->Tipo = NULL;
			$this->HoraInicio = NULL;
			$this->HoraFinal = NULL;
			$flag = NULL;
			$op = "newespe";
			//$control=1;

		} else {

			$sql = "SELECT Descripcion,Dias,Franja_HI,Franja_HF
				FROM especialidades
				WHERE IdEsp= $IdEsp;
				";
			$res = $this->con->query($sql);
			$row = $res->fetch_assoc();

			$num = $res->num_rows;
			if ($num == 0) {
				$mensaje = "tratar de actualizar el vehiculo con id= " . $IdEsp;
				echo $this->_message_error_Medico($mensaje);
			} else {

				// ***** TUPLA ENCONTRADA *****
				// echo "<br>TUPLA <br>";
				// echo "<pre>";
				// 	print_r($row);
				// echo "</pre>";
				$this->NombreESP = $row['Descripcion'];
				$this->Dias = $row['Dias'];
				$this->HoraInicio = $row['Franja_HI'];
				$this->HoraFinal = $row['Franja_HF'];




				$flag = "enable";
				$op = "updateespe";
				//$control=0;
			}
		}


		$html = '
			<div class="container mt-4">
				<div class="row justify-content-center">
					<div class="col-md-6">
						<form name="vehiculo" method="POST" action="content.php" enctype="multipart/form-data"">
						
						<input type="hidden" name="id" value="' . $IdEsp . '">
						<input type="hidden" name="op" value="' . $op . '">
						
						
						<table class="table table-bordered" ">
								<tr>
									<th class="table table-dark text-center" colspan="2">DADOTS ESPECIALIDAD</th>
								</tr>
								<tr>
								<tr>
								<td>Nombre:</td>
								<td><input type="text" class="form-control" name="especialidad" value="' . $this->NombreESP . '" required></td>
							</tr>
								<tr>
								<td>Dias:</td>
								<td><input type="text" class="form-control" name="dias" value="' . $this->Dias . '" required></td>
							</tr>
							<tr>
								<td>Inicio:</td>
								<td>' . $this->_get_combo_horas("horainicio", 8, $this->HoraInicio, 12) . '</td>
							</tr>
							<tr>
								<td>Final:</td>
								<td>' . $this->_get_combo_horas("horafinal", 12, $this->HoraFinal, 18) . '</td>
							</tr>
									
								<tr >
								<th colspan="2" class="text-center table table-dark"><input type="submit" class="btn btn-success" name="Guardar" value="GUARDAR"></th>
								</tr>												
							</table>
							</form>
							<a href="./content.php?d=' . $d_Especialidad_final . '"><button class="btn btn-primary  mx-auto" >REGRESAR</button></a>
							</div>
							</div>
						</div>
					
						
						
						
						';
		return $html;
	}
	public function delete_especialidad($id)
	{
		$sql = "DELETE FROM especialidades WHERE IdEsp=$id;";
		if ($this->con->query($sql)) {
			$html = '<div class="container mt-5">
				<!-- Alerta de éxito -->
				<div class="alert alert-success" role="alert">
				  ¡Operación exitosa!
				</div>
				</div>';
			echo $html;
			echo $this->_message_ok_especialidades("ELIMINÓ");
		} else {
			echo $this->_message_error_especialidades("eliminar");
		}
	}

	private function _message_ok_especialidades($tipo)
	{
		$Especialidades = "espe/1";
		$d_especialidad_final = base64_encode($Especialidades);
		$html = '

		<div class="container text-center">
			<div class="alert alert-success" role="alert">
			El registro se  ' . $tipo . ' correctamente			<br>
			<br>
 			<a class="btn btn-info" href="./content.php?d=' . $d_especialidad_final . '">Regresar</a>
			</div>
			
		</div> ';
		return $html;
	}

	private function _message_error_especialidades($tipo)
	{
		$Especialidades = "espe/1";
		$d_especialidad_final = base64_encode($Especialidades);
		$html = '
		<div class="container text-center">
			<div class="alert alert-danger" role="alert">
			 Error al ' . $tipo . '. Favor contactar 
			<br>
			<br>
 			<a class="btn btn-info" href="./content.php?d=' . $d_especialidad_final . '">Regresar</a> 
			</div>
			
		</div>
						 ';
		return $html;
	}



	//*********************************************************//


	// Uso comun//
	private function _get_combo_db($tabla, $valor, $etiqueta, $nombre, $defecto = NULL, $control)
	{
		$html = '<select name="' . $nombre . '" class="form-control"' . $control . '>';
		if ($tabla == "paciente") {
			if ($this->con->connect_error) {
				die("Error de conexión a la base de datos: " . $this->con->connect_error);
			}
			$sql = "SELECT $valor, $etiqueta, Genero FROM $tabla;"; // Asumiendo que el género está en la tabla
		} else {
			$sql = "SELECT $valor, $etiqueta FROM $tabla;";
		}


		$res = $this->con->query($sql);
		if (!$res) {
			die("Error en la consulta: " . $this->con->error);
		}
		while ($row = $res->fetch_assoc()) {
			$html .= ($defecto == $row[$valor]) ? '<option value="' . $row[$valor] . '" selected>' . $row[$etiqueta] . '</option>' : '<option value="' . $row[$valor] . '">' . $row[$etiqueta] . '</option>';
		}


		$html .= '</select>';
		return $html;
	}

	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_combo_anio($nombre, $anio_inicial, $defecto)
	{
		$html = '<select name="' . $nombre . '">';
		$anio_actual = date('Y');
		for ($i = $anio_inicial; $i <= $anio_actual; $i++) {
			$html .= ($i == $defecto) ? '<option value="' . $i . '" selected>' . $i . '</option>' . "\n" : '<option value="' . $i . '">' . $i . '</option>' . "\n";
		}
		$html .= '</select>';
		return $html;
	}
	private function _get_combo_horas($nombre, $HoraInicial, $defecto, $HoraFinal)
	{
		$html = '<select name="' . $nombre . '">';

		$intervaloMinutos = 60;
		// Convierte las horas a minutos
		$inicioEnMinutos = $HoraInicial * 60;
		$finEnMinutos = $HoraFinal * 60;
		$defectomin=strtotime($defecto) / 60;
		for ($i = $inicioEnMinutos; $i <= $finEnMinutos; $i += $intervaloMinutos) {
			$horaFormateada = sprintf('%02d:%02d:00', floor($i / 60), $i % 60);
			$html .= ($i == $defectomin)
				? '<option value="' . $horaFormateada . '" selected>' . $horaFormateada . '</option>' . "\n"
				: '<option value="' . $horaFormateada . '">' . $horaFormateada . '</option>' . "\n";
		}

		$html .= '</select>';
		return $html;
	}
	/*Aquí se agregó el parámetro:  $defecto*/
	private function _get_radio($arreglo, $nombre, $defecto, $flag)
	{

		$html = '
			<table border=0 align="left">';

		//CODIGO NECESARIO EN CASO QUE EL USUARIO NO SE ESCOJA UNA OPCION

		foreach ($arreglo as $etiqueta) {
			$html .= '
				<tr>
					<td>' . $etiqueta . '</td>
					<td>';

			if ($defecto == NULL) {
				// OPCION PARA GRABAR UN NUEVO VEHICULO (id=0)
				$html .= '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/ ' . $flag . '></td>';

			} else {
				// OPCION PARA MODIFICAR UN VEHICULO EXISTENTE
				$html .= ($defecto == $etiqueta) ? '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" checked/></td>' : '<input type="radio" value="' . $etiqueta . '" name="' . $nombre . '" ' . $flag . '/></td>';
			}

			$html .= '</tr>';
		}
		$html .= '
			</table>';
		return $html;
	}
	private function set_medico($especialidad)
	{

		$sql = "SELECT IdMedico 
							FROM medicos
							WHERE Especialidad='$especialidad'
							ORDER BY RAND()
							LIMIT 1;";

		$res = $this->con->query($sql);

		if ($res) {
			$row = $res->fetch_assoc();
			return ($row) ? $row['IdMedico'] : null;
		} else {
			return null;
		}
	}

	private function get_horario($turno, $fechaConsulta, $especialidadID, $doctorID, $tiempoExtra)
	{
		$sql = "SELECT IdMedico, MAX(HF) AS HORAFINAL FROM consultas WHERE IdMedico='$doctorID' AND FechaConsulta='$fechaConsulta' ORDER BY IdMedico";
		$sql_franja = "SELECT IdEsp, Franja_HI, Franja_HF FROM especialidades WHERE IdEsp=$especialidadID ORDER BY IdEsp";
		$res_franja = $this->con->query($sql_franja);
		$row_franja = $res_franja->fetch_assoc();

		// Ejecutar la consulta
		$res = $this->con->query($sql);

		if ($res === false) {
			// Manejar el error de la consulta SQL
			die("Error en la consulta SQL: " . $this->con->error);
		}

		// Verificar si las franjas horarias son NULL y asignar valores por defecto
		$limiteSuperior = ($turno == 'Vespertina') ? '12:00:00' : '18:00:00';
		$limiteInferior = ($turno == 'Diurna') ? '08:00:00' : '12:00:00';

		// Verificar si las franjas horarias de la base de datos son NULL y asignar valores por defecto
		if ($row_franja['Franja_HI'] === null) {
			$row_franja['Franja_HI'] = $limiteInferior;
		}
		if ($row_franja['Franja_HF'] === null) {
			$row_franja['Franja_HF'] = $limiteSuperior;
		}

		if ($turno == 'Diurna') {
			$limiteSuperior = '12:00:00';
			$limiteInferior = '08:00:00';
		} elseif ($turno == 'Vespertina') {
			$limiteInferior = '12:00:00';
			$limiteSuperior = '18:00:00';
		}

		// Verificar si las franjas horarias de la base de datos son válidas y ajustar según las reglas
		if ($row_franja['Franja_HI'] >= $limiteSuperior || $row_franja['Franja_HF'] <= $limiteInferior) {
			// Mensaje de error si la franja horaria de la base de datos no es válida para el turno seleccionado
			$html = '<div class="container mt-5">
							<!-- Alerta de éxito -->
							<div class="alert alert-danger" role="alert">
								"No hay turnos disponibles en esta franja horaria para el turno seleccionado.".
							</div>
						</div>';
			echo $html;
			echo $this->_message_error_turnos("turno");
			die();
		}

		// Ajustar las franjas horarias según las reglas y las franjas horarias de la base de datos
		if ($limiteSuperior <= $row_franja['Franja_HF']) {
			$row_franja['Franja_HF'] = $limiteSuperior;
		} elseif ($limiteInferior > $row_franja['Franja_HI']) {
			$row_franja['Franja_HI'] = $limiteInferior;
		}

		$row = $res->fetch_assoc();

		if (!$row || !isset($row['HORAFINAL'])) {
			$horaFinDB = $row_franja['Franja_HI'];
			// Si no se obtiene un resultado válido, asignar 8:00 AM
			// $horaFinDB = '08:00:00';
		} else {
			// Obtener la hora final desde la base de datos
			$horaFinDB = $row['HORAFINAL'];
		}

		// Crear objeto DateTime usando la hora final
		$horaFinalDateTime = DateTime::createFromFormat('H:i:s', $horaFinDB);
		$horaFinalDateTime->add(new DateInterval($tiempoExtra));
		// Obtener la nueva hora final
		$nuevaHoraFinal = $horaFinalDateTime->format('H:i:s');

		if ($horaFinalDateTime === false) {
			// Manejar el error de creación de DateTime
			die("Error al crear objeto DateTime desde la base de datos.");
		}
		if ($row_franja['Franja_HF'] < $nuevaHoraFinal) {
			$horaFinDB = NULL;
			$nuevaHoraFinal = NULL;
		}

		return array($horaFinDB, $nuevaHoraFinal);
	}
}?>