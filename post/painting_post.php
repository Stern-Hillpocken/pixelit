<?php
session_start();
// Connexion à la base de données
try
{
	$bdd = new PDO('mysql:host=localhost;dbname=pixelit_database;charset=utf8', 'root', '');
}
catch(Exception $e)
{
        die('Erreur : '.$e->getMessage());
}

if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby']) AND isset($_POST['sended-painting'])){
  $paint = $_POST['sended-painting'];
	$emptyGrid = '';
	for($i = 0; $i < 81; $i++){
		$emptyGrid += '0';
	}

  if(strlen($paint) !== 81){
		//Nettoyer
    $paint = $emptyGrid;
  } else {
		//Tout checker
		for($i = 0; $i < 81; $i++){
			if($paint[$i] !== '0' AND $paint[$i] !== '1' AND $paint[$i] !== '2'){
				$paint[$i] = '0';
			}
		}
  }

  //Envoyer
	if($paint !== $emptyGrid){
		$req = $bdd->prepare('UPDATE users SET grid = :grid WHERE pseudo = :pseudo');
		$req->execute(array(
		  'pseudo' => $_SESSION['pseudo'],
			'grid' => $paint
		  ));
		$req->closeCursor();
		//Prems > set timer
		//Récupérer la valeur
		$reponse = $bdd->prepare('SELECT startTime FROM lobbies WHERE name=:currentLobby ');
		$reponse->execute(array(':currentLobby' => $_SESSION['lobby']));
		// Affichage
		$startTime = null;
		while ($donnees = $reponse->fetch()){
			$startTime = strtotime($donnees['startTime']);
		}
		$reponse->closeCursor();
		//Modifier la valeur
		$time = date("Y-m-d H:i:s");
		if($startTime < 0){
			$req = $bdd->prepare('UPDATE lobbies SET startTime = :startTime WHERE name = :currentLobby');
			$req->execute(array(
			  'startTime' => $time,
				'currentLobby' => $_SESSION['lobby']
			  ));
			$req->closeCursor();
		}
	}
}
// Actualiser
header('Location: ./../?'.$_SESSION['lobby']);
?>
