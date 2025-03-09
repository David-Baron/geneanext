<!DOCTYPE html>
<html lang="fr">

<head>
    <?php
    include('fonctions.php');
    Ecrit_Meta('Aide mot de passe', 'Aide mot de passe', '');
    echo "</head>\n";
    $x = Lit_Env();
    Ligne_Body();
    ?><br />Vous avez oubli&eacute; votre mot de passe ?<br />
    <ul>
        <li>si vous &ecirc;tes le gestionnaire du site, cliquez <a href="http://genealogies.geneamania.net/demande_code_gest.php">ici</a> pour r&eacute;g&eacute;n&eacute;rer un mot de passe gestionnaire</li>
        <li>sinon adressez-vous au gestionnaire du site dont l'adresse figure sur la page d'accueil</li>
    </ul>
    </body>

</html>