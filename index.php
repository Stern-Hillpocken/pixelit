<?php
session_start();
if(isset($_SESSION['pseudo'])){
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
          // Connexion à la base de données
          try {
          	$bdd = new PDO('mysql:host=localhost;dbname=pixelit_database;charset=utf8', 'root', '');
          } catch(Exception $e) {
                  die('Erreur : '.$e->getMessage());
          }
          // Récupération des scores
          $reponse = $bdd->query('SELECT pseudo, score FROM scoreboard ORDER BY score DESC');
          // Affichage
          while ($donnees = $reponse->fetch()){
            if($donnees['pseudo'] === $_SESSION['pseudo']){
              echo '<span class="hightlight">' . htmlspecialchars($donnees['pseudo']) . ' :</span> ';
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
          // Connexion à la base de données
          try {
          	$bdd = new PDO('mysql:host=localhost;dbname=pixelit_database;charset=utf8', 'root', '');
          } catch(Exception $e) {
                  die('Erreur : '.$e->getMessage());
          }
          // Récupération des 10 derniers messages
          $reponse = $bdd->query('SELECT pseudo, message FROM minichat ORDER BY ID LIMIT 0, 10');
          // Affichage de chaque message (toutes les données sont protégées par htmlspecialchars)
          while ($donnees = $reponse->fetch()){
            if($donnees['pseudo'] === $_SESSION['pseudo']){
              echo '<span class="hightlight">' . htmlspecialchars($donnees['pseudo']) . ' :</span> ';
            } else {
              echo '<b>' . htmlspecialchars($donnees['pseudo']) . ' :</b> ';
            }
            echo htmlspecialchars($donnees['message']) . '<br/>';
          }
          $reponse->closeCursor();
          ?>
          <form action="post/game_post.php" method="post">
            <input type="text" name="message" id="message" placeholder="Propose ici..." autofocus/>
            <button type="submit"><span class="hightlight">>></span></button>
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
        <form action="post/game_post.php" method="post">
          <label for="time">Temps (secondes) :</label><input id="time" name="time" type="number" value="30" min="5" max="99"/>
          <label for="words">Mots :</label><textarea id="words" name="words" maxlength="65000" placeholder="Mots à faire deviner séparés par une virgule..."></textarea>
          <button type="submit">Jouer <span class="hightlight">>></span></button>
        </form>
        <div>
          <?php
          // Connexion à la base de données
          try {
          	$bdd = new PDO('mysql:host=localhost;dbname=pixelit_database;charset=utf8', 'root', '');
          } catch(Exception $e) {
            die('Erreur : '.$e->getMessage());
          }
          // Récupération
          $reponse = $bdd->query('SELECT pseudo FROM scoreboard ORDER BY ID');
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
        <p>Lien vers le lobby : <span class="hightlight">
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
        <form action="post/game_post.php" method="post">
          <input type="text" name="pseudo" id="pseudo" placeholder="Pseudo ici..." autofocus/>
          <input name="lobby" value=
            <?php
              echo '"'.$_SERVER['QUERY_STRING'].'"';
            ?>
          type="hidden" />
          <br/>
          <button type="submit">Jouer <span class="hightlight">>></span></button>
        </form>
    </div>
    </body>
    <footer>Code source sur github <a href="">>></a></footer>
</html>
<?php
}
?>
