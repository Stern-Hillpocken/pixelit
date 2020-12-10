<?php
session_start();
// Connexion à la base de données
try {
  $bdd = new PDO('mysql:host=localhost;dbname=pixelit_database;charset=utf8', 'root', '', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch(Exception $e) {
        die('Erreur : '.$e->getMessage());
}

//GRID et HOST
if(isset($_SESSION['pseudo']) AND isset($_SESSION['lobby'])){
	$reponse = $bdd->prepare('SELECT grid FROM users WHERE pseudo=:pseudo');
	$reponse->execute(array('pseudo' => $_SESSION['pseudo']));
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
  $reponse = $bdd->prepare('SELECT status, startTime, teamShow FROM lobbies WHERE name=:currentLobby');
  $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
  while ($donnees = $reponse->fetch()){
    $lobbyStatus = $donnees['status'];
    $startTime = $donnees['startTime'];
    $teamShow = $donnees['teamShow'];
  }
  $reponse->closeCursor();
}

// Grid
$emptyGrid = '';
for($i = 0; $i < 81; $i++){
  $emptyGrid .= '0';
}


// Display choice...
if(isset($_SESSION['pseudo']) AND ($lobbyStatus === 'drawing' OR $lobbyStatus === 'guessing' OR $lobbyStatus === 'showAnswer')){
////////////////////////////////////////////////////////////////////////////////
// SESSION
////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="assets/style.css" />
        <link rel="icon" type="image/png" href="assets/favicon.png" />
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <?php include 'ajax/check-lobby-status.php'; ?>
        <?php include 'ajax/check-startTime-value.php'; ?>
        <?php include 'ajax/check-teamShow.php'; ?>
        <title>pixelit - en jeu !</title>
    </head>
    <body>
      <div id="ingame">
        <div id="head">
          <?php
          if($lobbyStatus === 'drawing'){
            $playerInvolved = $_SESSION['pseudo'];
            include 'php/preg-currentWord.php';
            echo $currentWord;
          } else if($lobbyStatus === 'showAnswer'){
            include 'php/guessing-values.php';
            $playerInvolved = $teamPseudo[0];
            include 'php/preg-currentWord.php';
            echo $currentWord;
          }
          ?>
          <span id="clock">
            <img src="assets/clock.png"/><span id="time" title="Temps restant"></span>
          </span>
        </div>
        <div id="scoreboard">
          <?php
          // Récupération du round
          $reponse = $bdd->prepare('SELECT rounds, currentRound FROM lobbies WHERE name=:currentLobby');
          $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
          while ($donnees = $reponse->fetch()){
            echo '<p style="text-align:center"><b>Round :</b> '.$donnees['currentRound'].'/'.$donnees['rounds'].'</p>';
          }
          $reponse->closeCursor();
          // Récupération des scores
          $reponse = $bdd->prepare('SELECT pseudo, score, team FROM users WHERE lobby=:currentLobby ORDER BY score DESC');
          $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
          // Affichage
          while ($donnees = $reponse->fetch()){
            echo '<p><span title="Team">['.$donnees['team'].']</span> ';
            if($donnees['pseudo'] === $_SESSION['pseudo']){
              echo '<span class="highlight">'. htmlspecialchars($donnees['pseudo']).' :</span> ';
            } else {
              echo htmlspecialchars($donnees['pseudo']).' : ';
            }
            echo '<span title="Points">'.$donnees['score'] . '</span></p>';
          }
          $reponse->closeCursor();
          ?>
        </div>


        <div id="draw">
        <?php if($grid === $emptyGrid){ ?>
          <div id="painting-options"></div>
          <table id="painting">
          </table>
          <div id="color-points"></div>
          <form id="sended-painting-form" action="post/painting_post.php" method="post">
            <input id="sended-painting" name="sended-painting" value="" type="hidden"/>
            <button type="submit">Envoyer <span class="highlight">>></span></button>
          </form>
          <script type="text/javascript" src="js/painting.js"></script>
        <?php
      } else if($lobbyStatus === 'guessing' OR $lobbyStatus === 'showAnswer'){
        include 'php/display-painting.php';
      }else{echo '<span style="text-align:center">Pixelit ! Pixel art envoyé :)</span>';}
        ?>
        </div>


        <div id="chatbox">
          <div id="chat">
          <?php
          // Récupération des 10 derniers messages
          $reponse = $bdd->prepare('SELECT ID, pseudo, message FROM minichat WHERE lobby=:currentLobby ORDER BY ID DESC LIMIT 0, 10');
          $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
          // Affichage de chaque message (toutes les données sont protégées par htmlspecialchars)
          $chatText = '';
          while ($donnees = $reponse->fetch()){
            $strTxt = '<p id='.$donnees['ID'].'>';
            if($donnees['pseudo'] === $_SESSION['pseudo']){
              $strTxt .= '<span class="highlight">'.htmlspecialchars($donnees['pseudo']).' :</span> ';
            } else {
              $strTxt .= '<b>' . htmlspecialchars($donnees['pseudo']).' :</b> ';
            }
            $strTxt .= htmlspecialchars($donnees['message']).'</p>';
            $chatText = $strTxt.$chatText;
          }
          $reponse->closeCursor();
          echo $chatText;
          ?>
          </div>
          <form action="post/msg_post.php" method="post">
            <input id="message" type="text" name="message" maxlength="25" placeholder="Propose ici..." autofocus/>
            <button id="send-message" type="submit"><span class="highlight">>></span></button>
          </form>
        </div>
<script>
$(document).ready(function(){
    $('#send-message').click(function(e){
        e.preventDefault(); // Empêche le bouton d'envoyer le formulaire
        var message = encodeURIComponent( $('#message').val() );
        if(message != ''){
            $.ajax({
                url : 'post/send-message.php',
                type : 'POST',
                data : 'message=' + message,
                success : function(html){
                  $('#chat').append(html);
                  $('#message').val('');
                }
            });
        }
    });

    function loadNewMessages(){
        setTimeout( function(){
            var premierID = $('#chat p:last').attr('id');
            $.ajax({
                url : "ajax/check-message.php?id=" + premierID,
                type : 'GET',
                success : function(html){
                    $('#chat').append(html);
                    //Compter si plus de 10 : on supprime
                    while($('#chat').find('p').length > 10){
                      $('#chat').find('p').eq(0).remove();
                    }
                }
            });
            loadNewMessages();
        }, 1000);
    }
    loadNewMessages();
});
</script>

      </div>
    </body>
</html>
<?php
} else if (isset($_SESSION['pseudo']) AND isset($_SESSION['pseudo']) AND $lobbyStatus === 'endscore') {
  include('endscore.php');
} else if (isset($_SESSION['pseudo'])) {
  include('lobby.php');
} else {
  include('welcome.php');
}
?>
