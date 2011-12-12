<?php
include_once("dbaccess.php");

function is_email($Addr) { 
$p = '/^[a-z0-9!#$%&*+-=?^_`{|}~]+(\.[a-z0-9!#$%&*+-=?^_`{|}~]+)*'; 
$p.= '@([-a-z0-9]+\.)+([a-z]{2,3}'; 
$p.= '|info|arpa|aero|coop|name|museum)$/ix'; 
return preg_match($p, $Addr); 
}

$a=$_GET['a'];

	if ($a==1){
	$email=$_POST['email'];
	$provincia=$_POST['provincia'];
		if ($email!="" && $provincia!=""){
			if (!is_email($email)) {
			header("Location:index.php?msj=1&u=$email");
			}else{		
			$comparar = mysql_query("SELECT * FROM usuarios WHERE email='$email'");
			$array = mysql_fetch_array($comparar);
				if ($array['email']==$email){
				$u = $array['id'];
				header("Location:index.php?h=1&u=$u");
				}else{
				$consulta = "INSERT INTO usuarios (`email`, `provincia`";
				$consulta .= ") VALUES ('$email', '$provincia'";
				$consulta .= ");";
					if (mysql_query($consulta)){
					$u = mysql_insert_id($sConn); 
					header("Location:index.php?h=1&u=$u");
					}else{
					header("Location:index.php?msj=2");
					}
				}
			}
		}else{
		header("Location:index.php?msj=3");
		}
	}elseif ($a==2){
	unset ($_POST['guardar']);
		foreach($_POST as $clave => $variable){
			if(!is_int($variable)){
			
			$id_email=$_GET['u'];
			$id_tema=$variable;
			$consulta = "INSERT INTO rel (`id_email`, `id_tema`";
			$consulta .= ") VALUES ('$id_email', '$id_tema'";
			$consulta .= ");";
			mysql_query ($consulta);
			header("Location:index.php?msj=4&h=2");
			}else{
			header("Location:index.php?msj=5");
			break;
			}
		}
	}else{
	header("Location:index.php");
	}
?>