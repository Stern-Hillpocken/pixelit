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

////////////////////////////////////////////////////////////////////////////////
// LOGIN
////////////////////////////////////////////////////////////////////////////////
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
    $emptyGrid = '';
    for($i = 0; $i < 81; $i++){
      $emptyGrid.='0';
    }
    // Insertion du message à l'aide d'une requête préparée
    $req = $bdd->prepare('INSERT INTO users (pseudo, lobby, score, grid, guess) VALUES(?, ?, ?, ?, ?)');
    $req->execute(array($_SESSION['pseudo'], '', 0, $emptyGrid, ''));
  }
}

////////////////////////////////////////////////////////////////////////////////
// LOBBY
////////////////////////////////////////////////////////////////////////////////
$lobby = $_POST['lobby'];
if($lobby === '' || strlen($lobby) !== 8){
  $lobby = '';
  // Générer un nouveau lobby
  $chars = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
  while(strlen($lobby) < 8){
    $randomChar = rand(0, count($chars)-1);
    $lobby.=$chars[$randomChar];
  }
	// S'assurer qu'il n'est pas dans la BDD
	//TODO
	// Créer dans la BDD
	$req = $bdd->prepare('INSERT INTO lobbies (name, status, timeDraw, timeAnswer, answer) VALUES(?, ?, ?, ?, ?)');
	$req->execute(array($lobby, 'lobby', 0, 0, ''));
}
// Y mettre le joueur
$req = $bdd->prepare('UPDATE users SET lobby = :lobby WHERE pseudo = :pseudo');
$req->execute(array(
  'lobby' => $lobby,
  'pseudo' => $_SESSION['pseudo']
  ));
header('Location: ./../?'.$lobby);
?>
