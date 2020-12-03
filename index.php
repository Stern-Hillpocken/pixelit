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
  $reponse = $bdd->prepare('SELECT lobby, grid FROM users WHERE pseudo=:currentPseudo');
  $reponse->execute(array(':currentPseudo' => $_SESSION['pseudo']));
  $userCurrentLobby = '';
  while ($donnees = $reponse->fetch()){
    $userCurrentLobby = $donnees['lobby'];
    $grid = $donnees['grid'];
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
        <title>pixel it - en jeu !</title>
    </head>
    <body>
      <div id="ingame">
        <div id="head">tête</div>
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

        <script type="text/javascript">
          let colorPoints;
          let colorInt;
          let colorValue = ['white', 'black', 'red'];
          let colorPool;
          let painting;
          clearPainting();

          function clearPainting(){
            coloPoints = 0;
            colorInt = 1;
            colorPool = [81,20,1];

            painting = '';
            for(let i = 0; i < 81; i++){
              painting += '0';
            }
            updatePainting();
          }

          function updatePainting(){
            //options
            let optionsTable = '<img alt="erase" src="images/erase.png"/ onclick="clearPainting()">';
            for(let i = 0; i < 3; i++){
              optionsTable += ' <img alt="'+colorValue[i]+'-color" src="images/'+colorValue[i]+'-color.png" onclick="changeColor('+i+')"';
              if(i === colorInt){
                optionsTable += ' style="outline: 2px solid red"';
              }
              optionsTable += '/>';
              if(i > 0){
                optionsTable += '<span class="color-quantity">x'+colorPool[i]+'</span>';
              }
            }
            //table
            let paintingTable = '';
            for(let r = 0; r < 9; r++){
              paintingTable += '<tr>';
              for(let c = 0; c < 9; c++){
                paintingTable += '<td style="background-color:'+colorValue[painting[(r*9+c)]]+'" onclick="paintColor('+(r*9+c)+')"></td>';
              }
              paintingTable += '</tr>';
            }
            //points
            colorPoints = (81-colorPool[0])*0+(20-colorPool[1])*1+(1-colorPool[2])*4;
            //
            document.getElementById("painting-options").innerHTML = optionsTable;
            document.getElementById("painting").innerHTML = paintingTable;
            document.getElementById("color-points").innerHTML = colorPoints+' pt';
            if(colorPoints >= 2){document.getElementById("color-points").innerHTML += 's';}
            document.getElementById("sended-painting").value = painting;
          }

          function paintColor(pos){
            if(colorPool[colorInt] > 0){
              //gain color
              if(painting[pos] === '1'){
                colorPool[1] ++;
              } else if (painting[pos] === '2'){
                colorPool[2] ++;
              } else {
                colorPool[0] ++;
              }
              //loose color
              painting = painting.substring(0, pos)+colorInt+painting.substring(pos+1, painting.length);
              colorPool[colorInt] --;
              updatePainting();
            }
          }

          function changeColor(i){
            colorInt = i;
            updatePainting();
          }

          updatePainting();
        </script>

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
              if($grid === $emptyGrid){
                echo 'DISABLED';
              }
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
