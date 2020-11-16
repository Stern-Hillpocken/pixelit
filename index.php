<?php
session_start();
// Connexion à la base de données
try {
  $bdd = new PDO('mysql:host=localhost;dbname=pixelit_database;charset=utf8', 'root', '');
} catch(Exception $e) {
        die('Erreur : '.$e->getMessage());
}

// Est-ce le/mon bon lobby ?
if(isset($_SESSION['pseudo'])){
  $reponse = $bdd->prepare('SELECT lobby FROM users WHERE pseudo=:currentPseudo');
  $reponse->execute(array(':currentPseudo' => $_SESSION['pseudo']));
  $userCurrentLobby = '';
  while ($donnees = $reponse->fetch()){
    $userCurrentLobby = $donnees['lobby'];
  }
  if($userCurrentLobby !== $_SERVER['QUERY_STRING']){
    // On le rebascule sur son lobby
    header('Location: ./?'.$userCurrentLobby);
  }
}

// Est-on en jeu ?
$lobbyStatus = '';
if(isset($_SESSION['pseudo'])){
  $reponse = $bdd->prepare('SELECT status FROM lobbies WHERE name=:currentLobby');
  $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
  while ($donnees = $reponse->fetch()){
    $lobbyStatus = $donnees['status'];
  }
}


// Display choice...
if(isset($_SESSION['pseudo']) AND $lobbyStatus === 'game'){
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
        <title>pixel it - en jeu !</title>
    </head>
    <body>
      <div id="ingame">
        <div id="head">tête</div>
        <div id="scoreboard">
          <?php
          // Récupération des scores
          $reponse = $bdd->prepare('SELECT pseudo, score FROM users WHERE lobby=:currentLobby ORDER BY score DESC');
          $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
          // Affichage
          while ($donnees = $reponse->fetch()){
            if($donnees['pseudo'] === $_SESSION['pseudo']){
              echo '<span class="highlight">' . htmlspecialchars($donnees['pseudo']) . ' :</span> ';
            } else {
              echo '<b>' . htmlspecialchars($donnees['pseudo']) . ' :</b> ';
            }
            echo $donnees['score'] . '<br/>';
          }
          $reponse->closeCursor();
          ?>
        </div>
        <div id="draw">dessin</div>
        <div id="chat">
          <?php
          // Récupération des 10 derniers messages
          $reponse = $bdd->prepare('SELECT pseudo, message FROM minichat WHERE lobby=:currentLobby ORDER BY ID LIMIT 0, 10');
          $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
          // Affichage de chaque message (toutes les données sont protégées par htmlspecialchars)
          while ($donnees = $reponse->fetch()){
            if($donnees['pseudo'] === $_SESSION['pseudo']){
              echo '<span class="highlight">' . htmlspecialchars($donnees['pseudo']) . ' :</span> ';
            } else {
              echo '<b>' . htmlspecialchars($donnees['pseudo']) . ' :</b> ';
            }
            echo htmlspecialchars($donnees['message']) . '<br/>';
          }
          $reponse->closeCursor();
          ?>
          <form action="post/msg_post.php" method="post">
            <input type="text" name="message" id="message" placeholder="Propose ici..." autofocus/>
            <input name="lobby" value=
              <?php
                echo '"'.$_SERVER['QUERY_STRING'].'"';
              ?>
            type="hidden" />
            <button type="submit"><span class="highlight">>></span></button>
          </form>
        </div>
      </div>
    </body>
</html>
<?php
} else if (isset($_SESSION['pseudo'])) {
////////////////////////////////////////////////////////////////////////////////
// LOBBY
////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="icon" type="image/png" href="images/favicon.png" />
        <title>pixel it : en attente...</title>
    </head>
    <body>
      <h1>pixel it</h1>
      <div id="lobby">
        <form action="post/start_post.php" method="post">
          <label for="timeDraw">Temps dessins (secondes) :</label><input id="timeDraw" name="timeDraw" type="number" value="30" min="5" max="99"/>
          <label for="timeAnswer">Temps propositions (secondes) :</label><input id="timeAnswer" name="timeAnswer" type="number" value="30" min="5" max="99"/>
          <label for="words">Mots :</label><textarea id="words" name="words" maxlength="65000" placeholder="Mots à faire deviner séparés par une virgule..."></textarea>
          <input name="lobby" value=
            <?php
              echo '"'.$_SERVER['QUERY_STRING'].'"';
            ?>
          type="hidden" />
          <button type="submit">Jouer <span class="highlight">>></span></button>
        </form>
        <div>
          <?php
          // Récupération
          $reponse = $bdd->prepare('SELECT pseudo FROM users WHERE lobby=:currentLobby ORDER BY ID');
          $reponse->execute(array(':currentLobby' => $_SERVER['QUERY_STRING']));
          // Affichage
          while ($donnees = $reponse->fetch()){
            if($donnees['pseudo'] === $_SESSION['pseudo']){
              echo '<span class="highlight">'.htmlspecialchars($donnees['pseudo']).'</span> ';
            } else {
              echo '<u>'.htmlspecialchars($donnees['pseudo']).'</u> ';
            }
          }
          $reponse->closeCursor();
          ?>
        </div>
        <p>Lien vers le lobby : <span class="highlight">
          <?php echo $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>
        </span></p>
    </div>
    </body>
</html>
<?php
} else {
////////////////////////////////////////////////////////////////////////////////
// NOT SESSION
////////////////////////////////////////////////////////////////////////////////
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <link rel="stylesheet" href="css/style.css" />
        <link rel="icon" type="image/png" href="images/favicon.png" />
        <title>pixel it : joue à deviner le pixelart</title>
    </head>
    <body>
      <h1>pixel it</h1>
      <div id="inscription">
        <details><summary>Comment jouer ?</summary>
          <p>Règles...TODO</p>
          <hr/>
        </details>
        <form action="post/login_post.php" method="post">
          <input type="text" name="pseudo" id="pseudo" placeholder="Pseudo ici..." autofocus/>
          <input name="lobby" value=
            <?php
              echo '"'.$_SERVER['QUERY_STRING'].'"';
            ?>
          type="hidden" />
          <br/>
          <button type="submit">Jouer <span class="highlight">>></span></button>
        </form>
    </div>
    </body>
    <footer>Code source sur github <a href="https://github.com/Stern-Hillpocken/pixelit">>></a></footer>
</html>
<?php
}
?>
