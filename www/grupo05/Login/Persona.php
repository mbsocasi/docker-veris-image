<?php 
require_once "../constantes.php";
class Persona
{
	private $IdUsuario;
	private $Nombre;
	private $Password;

	/*Constructor*/

	public function __construct()
	{
		
	}
	
	public function getIdUsuario()
	{
		return $this->IdUsuario;
	}
	public function setIdUsuario($IdUsuario)
	{
		$this->IdUsuario = $IdUsuario;
	}
	public function getNombre(): string
	{
		return $this->Nombre;
	}
	public function setNombre($Nombre)
	{
		$this->Nombre = $Nombre;
	}
	public function getPassword()
	{
		return $this->Password;
	}
	public function setPassword($Password)
	{
		$this->Password = $Password;
	}

	public function getUsuarios(): array
	{
		$nombres = [];

		$cn = $this->conectar();
		$sql = "SELECT Nombre, Password, Rol FROM usuarios;";
		$res = $cn->query($sql);

		while ($row = $res->fetch_assoc()) {
			// Utiliza el nombre de Nombre como clave y agrega la fila completa al array de Nombres
			$nombres[$row['Nombre']] = $row;
		}
		$cn->close();
		return $nombres;
	}

	public function validarLogin(): array
	{
		$Nombre = $this->getNombre();
		$Password = (string) $this->getPassword();

		$cn = $this->conectar();
		$sql = "SELECT * FROM `usuarios` WHERE `Nombre`= '$Nombre' and Password = '$Password'";
		$res = $cn->query($sql);

		// Obtener el valor de Rol si se encuentra un Nombre
		$NombreEncontrado = mysqli_num_rows($res);
		$Rol = 0; // Valor predeterminado si no se encuentra el Nombre

		if ($NombreEncontrado) {
			$NombreData = $res->fetch_assoc();
			$Rol = $NombreData['Rol'];
		}
		$cn->close();
		return ['validacion' => $NombreEncontrado, 'Rol' => $Rol];
	}


	//*******************************************************
	function conectar()
	{
		//echo "<br> CONEXION A LA BASE DE DATOS<br>";
		$c = new mysqli(SERVER, USER, PASS, BD);

		if ($c->connect_errno) {
			die("Error de conexión: " . $c->connect_error);
		} else {
			//echo "La conexión tuvo éxito .......<br><br>";
		}

		$c->set_charset("utf8");
		return $c;
	}
	//**********************************************************	
}
?>