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

if(isset($_SESSION['pseudo']) && isset($_POST['sended-painting'])){
  //Nettoyer
  $paint = $_POST['sended-painting'];
  if(strlen($paint) !== 81){
    $paint = '';
    for($i = 0; $i < 81; $i++){
      $paint += '0';
    }
  }
  //Tout checker
  for($i = 0; $i < 81; $i++){
    if($paint[$i] !== '0' AND $paint[$i] !== '1' AND $paint[$i] !== '2'){
      $paint[$i] = '0';
    }
  }
  //Envoyer
  $req = $bdd->prepare('UPDATE users SET grid = :grid WHERE pseudo = :pseudo');
	$req->execute(array(
	  'pseudo' => $_SESSION['pseudo'],
		'grid' => $paint
	  ));
}
// Actualiser
header('Location: ./../?');
?>
