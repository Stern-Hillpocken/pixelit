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

if(!empty($_GET['id']) AND isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){

    $ID = (int) $_GET['id'];

    $requete = $bdd->prepare('SELECT * FROM minichat WHERE ID > :ID AND lobby = :currentLobby ORDER BY ID DESC');
    $requete->execute(array('ID' => $ID, 'currentLobby' => $_SESSION['lobby']));

    $chatText = '';
    while ($donnees = $requete->fetch()){
      $strTxt = '<p id='.$donnees['ID'].'>';
      if($donnees['pseudo'] === $_SESSION['pseudo']){
        $strTxt .= '<span class="highlight">' . htmlspecialchars($donnees['pseudo']) . ' :</span> ';
      } else {
        $strTxt .= '<b>' . htmlspecialchars($donnees['pseudo']) . ' :</b> ';
      }
      $strTxt .= htmlspecialchars($donnees['message']) . '</p>';
      $chatText = $strTxt.$chatText;
    }
    $requete->closeCursor();
    echo $chatText;// Retour à AJAX
}
?>
