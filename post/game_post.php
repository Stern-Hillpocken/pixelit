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

if(isset($_SESSION['pseudo'])){

	// Insertion du message à l'aide d'une requête préparée
	$req = $bdd->prepare('INSERT INTO minichat (pseudo, message) VALUES(?, ?)');
	$req->execute(array($_SESSION['pseudo'], $_POST['message']));

} else {
	// Login
	if(isset($_POST['pseudo'])){
	$pseudo = $_POST['pseudo'];
	// Nettoyer le pseudo
	while($pseudo[0] === ' '){
		$pseudo = substr($pseudo, 1, strlen($pseudo)-1);
	}
	// S'assurer qu'il n'est pas dans la BDD
	// TODO
	// Check
		if($pseudo !== ''){
			$_SESSION['pseudo'] = $pseudo;
			// Insertion du message à l'aide d'une requête préparée
			$req = $bdd->prepare('INSERT INTO scoreboard (pseudo, score, guess) VALUES(?, ?, ?)');
			$req->execute(array($_SESSION['pseudo'], 0, ''));
		}
	}
}

// Redirection du visiteur vers la page du jeu
if($_POST['lobby'] === '' || strlen($_POST['lobby']) !== 8){
  // Générer un nouveau lobby
  $newlobby = '';
  $chars = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
  while(strlen($newlobby) < 8){
    $randomChar = rand(0, count($chars)-1);
    $newlobby.=$chars[$randomChar];
  }
	// Créer dans la BDD
	$req = $bdd->prepare('INSERT INTO lobby (name, status, time, answer) VALUES(?, ?, ?, ?)');
	$req->execute(array($newlobby, 'lobby', 5, ''));
  header('Location: ./../?'.$newlobby);
} else {
	header('Location: ./../?'.$_POST['lobby']);
}
?>
