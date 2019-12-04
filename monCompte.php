<?php
session_start();
require('inc/pdo.php');
require('function/function.php');
$title = 'Home Page';
$errors = array();
$succes = false;

if (isLogged()){
if (!empty($_GET['token']) && $_GET['email']) {

    $token = clean($_GET['token']);
    $email = clean($_GET['email']);
    $email = urldecode($_GET['email']);
    $sql = "SELECT email,token FROM users WHERE email = :email AND token = :token";
    $query = $pdo->prepare($sql);
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->bindValue(':token', $token, PDO::PARAM_STR);

    $query->execute();
    $user = $query->fetch();
    if(!empty($user)) {

        if (!empty($_POST['sumbitted'])) {
            $password1 = clean($_POST['']);
            $password2 =  clean($_POST['']);
            if (!empty($password1)) {
                if ($password1 != $password2) {
                    $errors['password'] = 'Les deux mot de passe doivent être identique';
                } elseif (mb_strlen($password1) <= 5) {
                    $errors['password'] = 'Min 6 caractères';
                }


            }else{
                $errors['password'] = 'Veuillez renseigné ce champ';
            }
            if (count($errors)==0){
                $hashpsw = password_hash($password1, PASSWORD_BCRYPT);
                $token = generateRandomString(200);
                $sql = "UPDATE users SET password = :password, token = :token WHERE email = :email";
                $query = $pdo->prepare($sql);

                $query->bindValue(':email', $email, PDO::PARAM_STR);
                $query->bindValue(':password', $hashpsw, PDO::PARAM_STR);
                $query->bindValue(':token', $token, PDO::PARAM_STR);
                $query->execute();
                header('Location: index.php');
            }
        } else {
            die('404');
        }


    }
}
include('inc/header.php');
?>
    <h1>Modifier mot de passe</h1>
    <form action="" method="post">
        <label for="password1">Mot de passe *</label>
        <input type="password" id="password1" name="password1" value="">
        <p class="error"><?php if (!empty($errors['password'])) {
                echo $errors['password'];
            } ?></p>


        <label for="password2">Confirmation mdp *</label>
        <input type="password" id="password2" name="password2" value="">

        <input type="submit" name="submitted" value="Inscrivez-vous">
    </form>

<?php
}else{
    echo 'Erreur 403, vous n&apos;avez pas accès a cette fonctionnalité';
}
include('inc/footer.php');
