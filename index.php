<?php
session_start();
// Connexion à la base de données
try {
  $bdd = new PDO('mysql:host=localhost;dbname=pixelit_database;charset=utf8', 'root', '');
} catch(Exception $e) {
        die('Erreur : '.$e->getMessage());
}

//GRID et HOST
if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){
	$reponse = $bdd->prepare('SELECT grid FROM users WHERE pseudo=:pseudo');
	$reponse->execute(array(':pseudo' => $_SESSION['pseudo']));
	while ($donnees = $reponse->fetch()){
    $grid = $donnees['grid'];
	}
  if($_SESSION['lobby'] !== $_SERVER['QUERY_STRING']){// On le rebascule sur son lobby
    header('Location: ./?'.$_SESSION['lobby']);
  }
	$reponse->closeCursor();

	$reponse = $bdd->prepare('SELECT pseudo FROM users WHERE lobby=:lobby ORDER BY ID LIMIT 0,1');
	$reponse->execute(array(':lobby' => $_SESSION['lobby']));
	while ($donnees = $reponse->fetch()){
		$host = $donnees['pseudo'];
	}
	$reponse->closeCursor();
}

// Est-on en jeu ?
$lobbyStatus = '';
if(isset($_SESSION['pseudo'])){
  $reponse = $bdd->prepare('SELECT status FROM lobbies WHERE name=:currentLobby');
  $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
  while ($donnees = $reponse->fetch()){
    $lobbyStatus = $donnees['status'];
  }
  $reponse->closeCursor();
}

// Grid
$emptyGrid = '';
for($i = 0; $i < 81; $i++){
  $emptyGrid .= '0';
}


// Display choice...
if(isset($_SESSION['pseudo']) AND ($lobbyStatus === 'drawing' OR $lobbyStatus === 'guessing')){
////////////////////////////////////////////////////////////////////////////////
// SESSION
////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="icon" type="image/png" href="images/favicon.png" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script>
        //Check is $lobbyStatus change --> refresh
        $(document).ready(function(){
          function loopCheckLobbyStatus(){
            $.ajax({
                url : 'php/lobby-status.php',
                type : 'GET',
                data : false,
                success : function(realStatus){
                    if(realStatus !== '<?php echo $lobbyStatus; ?>'){
                      location.reload();
                    }
                }
            });
            setTimeout(loopCheckLobbyStatus, 1000);
          }
          loopCheckLobbyStatus();
        });
        </script>
        <title>pixel it - en jeu !</title>
    </head>
    <body>
      <div id="ingame">
        <div id="head">
          <?php
          // Récupération des mots à deviner
          $reponse = $bdd->prepare('SELECT currentWords FROM lobbies WHERE name=:currentLobby');
          $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
          $currentWords;
          while ($donnees = $reponse->fetch()){
            $currentWords = $donnees['currentWords'];
          }
          $reponse->closeCursor();
          preg_match_all('/(\w+\s*\w+)(,|$)/', $currentWords, $out_preg);
          $currentWordsArray = $out_preg[1];
          // Récupération des scores
          $reponse = $bdd->prepare('SELECT team FROM users WHERE pseudo=:pseudo ');
          $reponse->execute(array(':pseudo' => $_SESSION['pseudo']));
          // Affichage
          while ($donnees = $reponse->fetch()){
            echo $currentWordsArray[$donnees['team']];
          }
          $reponse->closeCursor();
          ?>
        </div>
        <div id="scoreboard">
          <?php
          // Récupération des scores
          $reponse = $bdd->prepare('SELECT pseudo, score, team FROM users WHERE lobby=:currentLobby ORDER BY score DESC');
          $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
          // Affichage
          while ($donnees = $reponse->fetch()){
            if($donnees['pseudo'] === $_SESSION['pseudo']){
              echo '['.$donnees['team'].']'.'<span class="highlight">'. htmlspecialchars($donnees['pseudo']).' :</span> ';
            } else {
              echo '['.$donnees['team'].']'.htmlspecialchars($donnees['pseudo']).' : ';
            }
            echo $donnees['score'] . '<br/>';
          }
          $reponse->closeCursor();
          ?>
        </div>
        <div id="draw">
          <div id="painting-options">
          </div>
          <table id="painting">
          </table>
          <div id="color-points"></div>
          <form action="post/painting_post.php" method="post">
            <input id="sended-painting" name="sended-painting" value="" type="hidden"/>
            <button type="submit">Envoyer <span class="highlight">>></span></button>
          </form>
        </div>

        <script type="text/javascript" src="js/painting.js"></script>

        <div id="chat">
          <?php
          // Récupération des 10 derniers messages
          $reponse = $bdd->prepare('SELECT pseudo, message FROM minichat WHERE lobby=:currentLobby ORDER BY ID DESC LIMIT 0, 10');
          $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
          // Affichage de chaque message (toutes les données sont protégées par htmlspecialchars)
          $chatText = '';
          while ($donnees = $reponse->fetch()){
            $strTxt = '';
            if($donnees['pseudo'] === $_SESSION['pseudo']){
              $strTxt = '<span class="highlight">' . htmlspecialchars($donnees['pseudo']) . ' :</span> ';
            } else {
              $strTxt = '<b>' . htmlspecialchars($donnees['pseudo']) . ' :</b> ';
            }
            $strTxt .= htmlspecialchars($donnees['message']) . '<br/>';
            $chatText = $strTxt.$chatText;
          }
          $reponse->closeCursor();
          echo $chatText;
          ?>
          <form action="post/msg_post.php" method="post">
            <input type="text" name="message" placeholder="Propose ici..." autofocus/>
            <input name="lobby" value=
              <?php
                echo '"'.$_SERVER['QUERY_STRING'].'"';
              ?>
            type="hidden" />
            <button type="submit"
            <?php
              if($grid === $emptyGrid){echo 'DISABLED';}
            ?>
            ><span class="highlight">>></span></button>
          </form>
        </div>

      </div>
    </body>
</html>
<?php
} else if (isset($_SESSION['pseudo'])) {
  include('lobby.php');
} else {
  include('welcome.php');
}
?>
