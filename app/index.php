<?php
session_start();

require_once 'Fonctions/db.php';
require_once 'Classes/class.post.php';
require_once 'Classes/class.users.php';

if (!isset($_SESSION['username'])) {
    header("Location:login.php");
    exit();
}

$errors = array();

echo "<pre>";
print_r($_REQUEST);
echo "</pre>";

if (isset($_REQUEST['insert'])) {
    if ($_REQUEST['text_post'] > 0) {

        $post = new Post($db);
        $post->setFkUser($_SESSION['id']);
        $post->setTextPost($_REQUEST['text_post']);
        $post->insert();
        unset($post);
    } else {
        array_push($errors, "<h2 style='color : red;'> Le contenu du post ne peut pas être vide ! </h2>");
    }
}

if (isset($_REQUEST['update'])) {
    if ($_REQUEST['text_post_update'] > 0) {
        $post = new Post($db);
        $post->setId($_REQUEST['update']);
        $post->setFkUser($_SESSION['id']);
        $post->setTextPost($_REQUEST['text_post_update']);
        $post->update();
        unset($post);
    } else {
        array_push($errors, "<h2 style='color : red;'> Le contenu du post ne peut pas être vide ! </h2>");
    }
}

if (isset($_REQUEST['delete'])) {

    $post = new Post($db);
    $post->setId($_SESSION['id']);
    $post->delete();
    unset($post);
}

$user = new Users($db);
$user->setUsername($_SESSION['username']);
$adminOrUser = $user->select();

if ($adminOrUser['role'] == "admin") {
    $post = new Post($db);
    $results = $post->selectAll();
    unset($post);
} else {

    $post = new Post($db);
    $post->setFkUser($_SESSION['id']);
    $results = $post->select();
    unset($post);
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page d'accueil</title>
</head>

<body>

    <a href="login.php"><button>Déconnexion</button></a><span> </span><br>
    <h1>Bienvenue à toi <em><?php echo $_SESSION['username']; ?></em></h1><br>
    <?php
foreach ($errors as $error) {
    ?>
    <p><?php echo $error; ?></p>
    <?php
}
?>
    <form method="post" action="index.php">
        <div> Ecrire un post :<br><textarea style=" height : 12vh; width : 50%;" name="text_post" id="" cols="30"
                rows="10"></textarea>
        </div>
        <p></p>
        <button type="submit" name="insert">Poster</button>
    </form>
    <?php
if (isset($results) && $results != null) {
    ?>
    <table>

        <?php
$i = 1;
    foreach ($results as $result) {
        ?>

        <form method="post" action="index.php">

            <label for="">Post n° <?php echo $i ?> </label>
            <label for="">Ecrit par <em><?php echo $result["username"] ?></em></label><br>

            <textarea name="text_post_update"
                style=" height: 10vh; width: 50% ; border: 1px solid grey; border-radius: 3px; margin-top: 2%; margin-bottom: 2%;"
                cols="30" rows="10"><?php echo $result["text_post"] ?></textarea><br>

            <button type="submit"
                style="background-color: deepskyblue; color : white; margin-right : 2px; border : 1px solid black; border-radius : 3px;"
                value="<?php echo $result["id"]; ?>" name="update">Modifier</button>

            <button type="submit"
                style="background-color: crimson; color : white; border : 1px solid black; border-radius : 3px; margin-bottom : 4%;"
                value="<?php echo $result["id"]; ?>" name="delete">Supprimer</button><br>

        </form>

        <?php
$i++;
    }?>

    </table>
    <?php

}
?>
</body>

</html>