<?php

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

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
    <?php
    $x = Ecrit_Meta('Installation Généamania', 'Installation Généamania', '');
    echo "</head>\n";

    echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
    echo '<form id="saisie" method="post" ENCTYPE="multipart/form-data" action="' . my_self() . '">' . "\n";
    echo '<input type="hidden" name="vvTest" VALUE="' . $vTest . '">' . "\n";
    echo '<table cellpadding="0" WIDTH="100%">' . "\n";
    echo '<tr>' . "\n";
    echo '<td ALIGN="CENTER">' . "\n";
    echo '<h1>R&eacute;cup&eacute;ration de la derni&egrave;re version de ';
    if ($vTest == 'O') echo 'test';
    else echo 'r&eacute;f&eacute;rence';
    echo '</h1>';
    echo '</td></tr>' . "\n";
    echo '<tr><td>&nbsp;</td></tr>' . "\n";
    echo '<tr align="center">' . "\n";
    echo '<td><input type="submit" name="majsource" VALUE="' . $lib_bt_OK . '"></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
    echo '</form>' . "\n";
    echo '<br>NB : il faut &ecirc;tre connect&eacute; &agrave; Internet pour r&eacute;cup&eacute;rer les sources de G&eacute;n&eacute;amania<br><br>';
    echo '<br>NB : la r&eacute;cup&eacute;ration des sources peut prendre un certain temps !<br><br>';

    echo "<hr>\n";
    echo '<a href="install.php">Page d\'installation</a>';

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

        echo '<br>R&eacute;cup&eacute;ration de l\'archive sur le site de r&eacute;f&eacute;rence<br />';
        echo '  - D&eacute;but :&nbsp;';
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
            echo '  - Fin :&nbsp;';
            aff_heure();

            echo 'D&eacute;compression de l\'archive<br>';
            echo '  - D&eacute;but :&nbsp;';
            aff_heure();
            // Traitement de l'rchive en local
            $zip = new ZipArchive;
            if ($zip->open($nom_arch_locale) === TRUE) {
                $zip->extractTo('.');
                $zip->close();
                echo '  - Fin :&nbsp;';
                aff_heure();
                echo '<br>';

                // Restauration des fichiers initiaux
                if ($pres_connexion) {
                    echo 'Restauration du fichier de connexion original<br>';
                    unlink($nom_fic_cnx);
                    rename($pref_sav . $nom_fic_cnx, $nom_fic_cnx);
                }
                if ($pres_param_part) {
                    echo 'Restauration du fichier des param&egrave;tres particuliers original<br>';
                    // Le fichier n'exxiste pas forcément dans l'archive contrairement au fichier de onnexion
                    if (file_exists($nom_fic_param_part)) unlink($nom_fic_param_part);
                    rename($pref_sav . $nom_fic_param_part, $nom_fic_param_part);
                }

                // Suppression de l'archive locale
                unlink($nom_arch_locale);


                echo '<br>Appel de la page de <a href="install.php">migration</a>';
            } else {
                echo 'Echec de l\'ouverture de l\'archive ; vérifiez que votre installation autorise PHP à utiliser les fonctions de compression';
            }
        } else {
            echo 'Echec de la r&eacute;cup&eacute;ration de l\'archive sur le serveur G&eacute;n&eacute;amania';
        }
        fclose($fp);
    }

    ?>
    </body>

</html>