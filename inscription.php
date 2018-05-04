<?php

require 'recaptcha.php';

if(
    isset($_POST['email']) &&
    isset($_POST['name']) &&
    isset($_POST['password']) &&
    isset($_POST['password_2'])
){
    if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        $errors[] = '! E-mail : invalide !';
    } 

    if(!preg_match('#^[a-zA-Z -]{2,70}$#', $_POST['name'])){
        $errors[] = '! Nom : invalide !';
    }

    if(!preg_match('#^.{2,200}$#', $_POST['password'])){
        $errors[] = '! Mot de passe : invalide !';
    }

    if($_POST['password'] != $_POST['password_2']){
        $errors[] = '! Confirmation du mot de passe : invalide !';
    }


    if(!recaptcha_valid($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR'])){
        $errors[] = 'Captcha  invalide !';
    }
 
    if(!isset($errors)){
        require 'bdd.php';
        $verifyEmail = $bdd->prepare('SELECT * FROM users WHERE email = ?');
        $verifyEmail->execute(array($_POST['email']));
        
        if(!empty($verifyEmail->fetch())){
            $errors[] = 'Email déjà utilisée !';
        } else {
            $createNewUser = $bdd->prepare('INSERT INTO users(email, name, password) VALUES(?,?,?)');
            $createNewUser->execute(array(
                $_POST['email'],
                $_POST['name'],
                password_hash($_POST['password'], PASSWORD_BCRYPT)
            ));
    
            if($createNewUser->rowCount() > 0){
                $success = 'Compte créé !';
            } else {
                $errors[] = 'Erreur serveur, veuillez ré-essayer !';
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

    <form action="inscription.php" method="POST">
        <input name="email" type="text" placeholder="E-mail">
        <input name="name" type="text" placeholder="Votre nom">
        <input name="password" type="password" placeholder="Mot de passe">
        <input name="password_2" type="password" placeholder="Confirmation">
        <div class="g-recaptcha" data-sitekey="6LeU8VYUAAAAAJhK2meMPzqbdr6Yg0H7KW5bBERc"></div>
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
<script src='https://www.google.com/recaptcha/api.js'></script>
</html>


