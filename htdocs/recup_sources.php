<?php

require(__DIR__ . '/../app/ressources/fonctions.php');

// Remove d'un répertoire non vide
function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") rrmdir($dir . "/" . $object);
                else unlink($dir . "/" . $object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

// Présence d'un fichier ?
// Si oui, on mémorise
function memo_fic($nom_fic)
{
    global $pref_sav;
    $res_memo = false;
    if (file_exists($nom_fic)) {
        $res_memo = true;
        rename($nom_fic, $pref_sav . $nom_fic);
    }
    return $res_memo;
}

$lib_bt_OK = 'METTRE A JOUR';

// Récupération des variables de l'affichage précédent
$tab_variables = array('majsource', 'vvTest');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$majsource = Secur_Variable_Post($majsource, strlen($lib_bt_OK), 'S');
$vvTest    = Secur_Variable_Post($vvTest, 1, 'S');

// On retravaille le libellé du bouton pour être standard...
if ($majsource == $lib_bt_OK) $majsource = 'OK';

// Version de test demandé ?
$vTest = Recup_Variable('test', 'C', 'Oo');
$vTest = ucfirst($vTest);

if ($vvTest != '') $vTest = $vvTest;

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <style type="text/css">
        body {
            background-color: #a7d8a9;
        }
    </style>
    <?php Ecrit_Meta('Installation Généamania', 'Installation Généamania', ''); ?>
</head>
<?php
echo '<body vlink="#0000ff" link="#0000ff">';
echo '<form id="saisie" method="post" enctype="multipart/form-data">';
echo '<input type="hidden" name="vvTest" value="' . $vTest . '">';
echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td>';
echo '<h1>Récupération de la dernière version de ';
if ($vTest == 'O') echo 'test';
else echo 'référence';
echo '</h1>';
echo '</td></tr>';
echo '<tr><td> </td></tr>';
echo '<tr>';
echo '<td><input type="submit" name="majsource" value="' . $lib_bt_OK . '"></td>';
echo '</tr>';
echo '</table>';
echo '</form>';
echo '<br>NB : il faut être connecté à Internet pour récupérer les sources de Généamania<br><br>';
echo '<br>NB : la récupération des sources peut prendre un certain temps !<br><br>';
echo "<hr>";
echo '<a href="' . $root . '/install.php">Page d\'installation</a>';

// L'utilisateur a cliqué sur Mettre à jour les paramètres
if ($majsource == 'OK') {
    $nom_arch_locale = 'comp.zip';
    $nom_arch_distante = 'http://tech.geneamania.net/Telechargements/';
    if ($vTest == 'O') $nom_arch_distante .= 'Geneamania_test.zip';
    else $nom_arch_distante .= 'Geneamania.zip';

    $nom_fic_cnx = 'connexion_inc.php';
    $nom_fic_param_part = 'param_part.php';
    $pref_sav = 'sv_inst_';

    set_time_limit(0);

    echo '<br>Récupération de l\'archive sur le site de référence<br>';
    echo '  - Début : ';
    aff_heure();
    if ($fp = fopen($nom_arch_distante, 'rb')) {

        // Vide au préalable le réperoire de la documentation GénéGraphe
        rrmdir('documentation');

        // Présence du fichier de connexion ?
        // Si oui, on mémorise
        $pres_connexion = memo_fic($nom_fic_cnx);

        // Présence du fichier des paramètres particuliers ?
        $pres_param_part = memo_fic($nom_fic_param_part);

        if ($pointer = fopen($nom_arch_locale, 'wb+')) {
            while ($buffer = fread($fp, 1024)) {
                if (!fwrite($pointer, $buffer)) {
                    return FALSE;
                }
            }
        }
        fclose($pointer);
        echo '  - Fin : ';
        aff_heure();

        echo 'Décompression de l\'archive<br>';
        echo '  - Début : ';
        aff_heure();
        // Traitement de l'rchive en local
        $zip = new ZipArchive;
        if ($zip->open($nom_arch_locale) === TRUE) {
            $zip->extractTo('.');
            $zip->close();
            echo '  - Fin : ';
            aff_heure();
            echo '<br>';

            // Restauration des fichiers initiaux
            if ($pres_connexion) {
                echo 'Restauration du fichier de connexion original<br>';
                unlink($nom_fic_cnx);
                rename($pref_sav . $nom_fic_cnx, $nom_fic_cnx);
            }
            if ($pres_param_part) {
                echo 'Restauration du fichier des paramètres particuliers original<br>';
                // Le fichier n'exxiste pas forcément dans l'archive contrairement au fichier de onnexion
                if (file_exists($nom_fic_param_part)) unlink($nom_fic_param_part);
                rename($pref_sav . $nom_fic_param_part, $nom_fic_param_part);
            }

            // Suppression de l'archive locale
            unlink($nom_arch_locale);


            echo '<br>Appel de la page de <a href="' . $root . '/install.php">migration</a>';
        } else {
            echo 'Echec de l\'ouverture de l\'archive. vérifiez que votre installation autorise PHP à utiliser les fonctions de compression';
        }
    } else {
        echo 'Echec de la récupération de l\'archive sur le serveur Généamania.';
    }
    fclose($fp);
}

?>
</body>

</html>