<?php

//=====================================================================
// Affichage des informations techniques
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('G')) {
    header('Location: ' . $root . '/');
    exit();
}

$titre = $LG_Menu_Title['Tech_Info'];
$x = Lit_Env();

require(__DIR__ . '/../app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'Infos_Tech', '');

echo '<br>' . "\n";
echo my_html(LG_TECH_INFO_VERSION) . LG_SEMIC . $Version . '<br>';
if ($Environnement == 'I') echo my_html(LG_TECH_INFO_ENVIR_INTERNET);
else echo my_html(LG_TECH_INFO_ENVIR_LOCAL);
echo '<br><br><br>' . "\n";

// $x = getPhpinfo();
// var_dump($x);

/* phpinfo(
    INFO_GENERAL
    # +INFO_CREDITS
    + INFO_CONFIGURATION
    + INFO_MODULES
    + INFO_ENVIRONMENT
    + INFO_VARIABLES
); */

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';
?>
</body>

</html>