<?php
require(__DIR__ . '/app/ressources/fonctions.php');
$x = Lit_Env();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <?php Ecrit_Meta('Aide mot de passe', 'Aide mot de passe', ''); ?>
</head>

<body>
    <br>Vous avez oublié votre mot de passe ?<br>
    <ul>
        <li>
            si vous êtes le gestionnaire du site, cliquez
            <a href="http://genealogies.geneamania.net/demande_code_gest.php">ici</a>
            pour régénérer un mot de passe gestionnaire
        </li>
        <li>sinon adressez-vous au gestionnaire du site dont l'adresse figure sur la page d'accueil</li>
    </ul>
</body>

</html>