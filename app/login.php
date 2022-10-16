<?php
session_start();
session_destroy();
session_start();

require_once "Fonctions/db.php";
require_once "Classes/class.users.php";

// echo "<pre>";
// print_r($_REQUEST);
// echo "</pre>";

$errors = array();

if (isset($_REQUEST['username'])) {
    $user = new Users($db);
    $user->setUsername($_REQUEST['username']);
    $user->select();
    if ($user->getId() > 0) {
        //echo $user->getPassword();
        if (password_verify($_REQUEST['password'], $user->getPassword())) {
            $_SESSION['username'] = $user->getUsername();
            $_SESSION['id'] = $user->getId();
            header("Location: index.php");
            exit();
        } else {
            array_push($errors, "Identifiant ou mot-de-passe incorrect ! MDP");
        }
    } else {
        array_push($errors, "Identifiant ou mot-de-passe incorrect ! ID ");
    }
}
if (isset($_REQUEST['context']) && $_REQUEST['context'] == "success") {
    echo '<h2 style="color : green;"> Création de compte réussie veuillez vous connecter </h2>';
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
</head>

<body>
    <?php if (isset($_GET['context']) && $_GET['context'] == "réussie") {?>
    <h2 style=" color : green ;">
        <?php echo ("Modification réussie vous pouvez vous
			connecter avec votre nouveau mot-de-passe !"); ?>
    </h2>
    <?php
}
?>
    <?php
foreach ($errors as $error) {
    ?>
    <p><?php echo $error; ?></p>
    <?php
}
?>
    <form method="post">
        <div>
            <label>Identifiant:</label><br>
            <input type="text" name="username" required><br>
            <label>Mot-de-passe:</label><br>
            <input type="password" name="password" required><br>
            <input type="submit" name="connexion" value="Se connecter">
            <input type="reset" name="reset" value="Annuler">
        </div>
        <a href="signup.php">Créer un compte</a>
        <a href="forgotPassword.php">Mot de passe oublié ?</a><br>
    </form>
    <div>

    </div>

</body>

</html>