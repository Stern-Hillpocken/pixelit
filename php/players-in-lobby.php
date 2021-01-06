<?php
session_start();
// Connexion à la base de données
include 'bdd-connexion.php';
$reponse = $bdd->prepare('SELECT pseudo, team FROM users WHERE lobby=:lobby ORDER BY ID LIMIT 0,1');
$reponse->execute(array(':lobby' => $_SESSION['lobby']));
while ($donnees = $reponse->fetch()){
  $host = $donnees['pseudo'];
}
$reponse->closeCursor();

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
      $players .= '<span class="highlight" id='.$donnees['ID'].'>'.htmlspecialchars($donnees['pseudo']).'</span> ';
    } else {
      $players .= '<span id='.$donnees['ID'].' style="text-decoration:underline">'.htmlspecialchars($donnees['pseudo']);
      if($_SESSION['pseudo'] === $host){
        $players .= '<svg onclick="window.location.href = \'./post/delete-player.php?pseudo='. htmlspecialchars($donnees['pseudo']) .'\'" viewBox="0 0 24 24">
    <path d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9M7,6H17V19H7V6M9,8V17H11V8H9M13,8V17H15V8H13Z" /></svg>';
      }
      $players .= '</span> ';
    }
  }
  $requete->closeCursor();

  echo $players; // retour à AJAX
}
?>
