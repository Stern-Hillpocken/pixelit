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
          <input type="text" name="pseudo" id="pseudo" placeholder="Pseudo ici..." maxlength="25" autofocus/>
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
