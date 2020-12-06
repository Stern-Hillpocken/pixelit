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

if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){
  if(isset($_GET['id']) AND !empty($_GET['id'])){
    $lastID = (int) $_GET['id']; // on s'assure que c'est un nombre entier
  } else {
    $lastID = 0;
  }

  $requete = $bdd->prepare('SELECT ID, pseudo FROM users WHERE ID > :ID AND lobby = :lobby ORDER BY ID');
  $requete->execute(array('ID' => $lastID, 'lobby' => $_SESSION['lobby']));

  $players = null;

  while ($donnees = $requete->fetch()){
    if($donnees['pseudo'] === $_SESSION['pseudo']){
      $players .= '<span class="highlight" id='.$donnees['ID'].'>' .htmlspecialchars($donnees['pseudo']).'</span> ';
    } else {
      $players .= '<span id='.$donnees['ID'].' style="text-decoration:underline">' .htmlspecialchars($donnees['pseudo']).'</span> ';
    }
  }
  $requete->closeCursor();

  echo $players; // retour à AJAX
}
?>
