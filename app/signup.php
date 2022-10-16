<?php
session_start();

require_once "Fonctions/db.php";
require_once "Classes/class.users.php";

// echo "<pre>";
// print_r($_REQUEST);
// echo "</pre>";
$errors = array();

if (isset($_REQUEST['username'])) {
    if ($_REQUEST['password'] !== $_REQUEST['confPassword']) {
        array_push($errors, "Les mots-de-passe doivent être identiques");
    }
    if (!count($errors)) {
        $user = new Users($db);
        $user->setUsername($_REQUEST['username']);
        $user->setPassword($_REQUEST['password']);
        $user->insert();
        $errors = $user->getErrors();
        if ($user->getId() > 0) {
            header("Location: login.php?context=success");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign Up</title>
</head>

<body>
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
            <label>Confirmer mot-de-passe:</label><br>
            <input type="password" name="confPassword" required><br>
            <input type="submit" name="submit" value="S'enregistrer">
            <input type="reset" name="reset" value="Annuler">
        </div>
    </form>

</body>

</html>