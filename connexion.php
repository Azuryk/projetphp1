<?php
session_start();
if(
    isset($_POST['email']) &&
    isset($_POST['password'])
){
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $errors[] = '! E-mail : invalide !';
    } 

    if(!preg_match('#^.{2,200}$#', $_POST['password'])){
        $errors[] = '! Mot de passe : invalide !';
    }

    if(!isset($errors)){
        require 'bdd.php';
        $getUserInfos = $bdd->prepare('SELECT * FROM users WHERE email = ?');
        $getUserInfos->execute(array(
            $_POST['email']
        ));
        $userInfos = $getUserInfos->fetch(PDO::FETCH_ASSOC);

        if(empty($userInfos)){

            $errors[] = 'Compte inexistant !';

        } else {

            if(password_verify($_POST['password'], $userInfos['password'])){

                
                $success = 'Vous êtes bien connecté !';
                $displayForm = false;
                $_SESSION['user'] = array(
                    'email' => $userInfos['email'],
                    'id' => $userInfos['id'],
                    'name' => $userInfos['name']
                );

            } else {

                $errors[] = 'Mot de passe incorrect !';
            }

        }


    }

}




?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
    <form action="connexion.php" method="POST">
        <input name="email" type="text" placeholder="E-mail">
        <input name="password" type="password" placeholder="Mot de passe">
        <input type="submit">
    </form>
    
    <?php

if(isset($errors)){
    foreach($errors as $error){
        echo '<p style="color:red;">' . $error .' </p>';
    }
    
}

if(isset($success)){
    echo '<p style="color:green;">' . $success . '</p>';
}



?>   


</body>
</html>