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

// Envoyer un message
if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){
	// Insertion du message à l'aide d'une requête préparée
	$req = $bdd->prepare('INSERT INTO minichat (pseudo, message, lobby) VALUES(?, ?, ?)');
	$req->execute(array($_SESSION['pseudo'], $_POST['message'], $_SESSION['lobby']));
	$req->closeCursor();
	echo 'message sended';
}

?>
