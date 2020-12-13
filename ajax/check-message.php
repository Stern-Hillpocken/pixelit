<?php
session_start();
// Connexion à la base de données
include '../php/bdd-connexion.php';

if(!empty($_GET['id']) AND isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){

    $ID = (int) $_GET['id'];

    include '../php/guessing-values.php';

    $requete = $bdd->prepare('SELECT * FROM minichat WHERE ID > :ID AND lobby = :currentLobby ORDER BY ID DESC');
    $requete->execute(array('ID' => $ID, 'currentLobby' => $_SESSION['lobby']));

    $reponse = $bdd->prepare('SELECT status FROM lobbies WHERE name=:currentLobby');
    $reponse->execute(array(':currentLobby' => $_SESSION['lobby']));
    while ($donnees = $reponse->fetch()){
      $lobbyStatus = $donnees['status'];
    }
    $reponse->closeCursor();

    $chatText = '';
    while ($donnees = $requete->fetch()){
      $strTxt = '<p id='.$donnees['ID'];
      $writterDisplayName = htmlspecialchars($donnees['pseudo']);
      if(in_array($donnees['pseudo'], $teamPseudo) AND $lobbyStatus === 'guessing'){
        $strTxt .= ' class="txt-clue"';
        $writterDisplayName = 'Indice';
      }
      $strTxt .= '>';
      if($donnees['pseudo'] === $_SESSION['pseudo']){
        $strTxt .= '<span class="highlight">' . $writterDisplayName . ' :</span> ';
      } else {
        $strTxt .= '<b>' . $writterDisplayName . ' :</b> ';
      }
      $strTxt .= htmlspecialchars($donnees['message']) . '</p>';
      $chatText = $strTxt.$chatText;
    }
    $requete->closeCursor();
    echo $chatText;// Retour à AJAX
}
?>
