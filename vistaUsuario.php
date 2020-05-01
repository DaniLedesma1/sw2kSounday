<?php
require_once 'includes/config.php';
use es\ucm\fdi\aw\classes\databaseClasses\Users as Users;
use es\ucm\fdi\aw\classes\classes\user as user;

if (!es\ucm\fdi\aw\Application::getSingleton()->usuarioLogueado()) {
    header("Location: index.php");
}

$form = new es\ucm\fdi\aw\FormularioEditarPerfil();
$htmlform = $form->gestiona();

$_GET["busquedaUsuario"] = "1"; //para probar

$usuarios = new Users();
$usuarios = $usuarios->where("id", "=" , 
$_GET["busquedaUsuario"])->get();
$usuario="";
foreach($usuarios as $us) {
    $usuario=$us;
}
$userId=$usuario->getId();
$userName=$usuario->getName();
$userEmail=$usuario->getEmail();
$userRol=$usuario->getRol();
$userDesc=$usuario->getDescripcion();

function muestra($cor, $us, $nom, $rol, $id, $form){
    $html = "<img src= 'server/images/users/";
    $html .= $id;
    $html .= ".png'>";
    $html .= "<h1> ";
    $html .= $nom;
    $html .= " </h1> ";
    $html .= $cor;
    $html .= ", ";
    $html .= $rol;
    $html .= $us;

    if (isset($_GET['editar'])) $html .= $form;
    else $html .= '<h1><a type="button" href="vistaUsuario.php?editar=true">Editar</a></h1>';
    return $html;
}

function sidebar(){
    if (user::esGestor($_SESSION['idUser'])) require 'includes/handlers/sidebarLeftGestor.php';
    else require 'includes/handlers/sidebarLeft.php';
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="css/styles-contents.css"/>
	<link rel="stylesheet" type="text/css" href="css/styles-footer.css"/>
    <link rel="stylesheet" type="text/css" href="css/styles-navSidebarLeft.css"/>
	<script src="https://kit.fontawesome.com/9d868392d8.js"></script>
    <title>Usuario</title>
</head>
<body>

<div id="container" class="wrapper">

    <nav>
        <?php
        sidebar();
        ?>
    </nav>

    <section id="contents" class="contents">
        <?php
        echo muestra($userEmail, $userDesc, $userName, $userRol, $userId, $htmlform);
        ?>
    </section>

    <footer>
        <?php
        require 'includes/handlers/footer.php';
        ?>
    </footer>

</div>

</body>
</html>