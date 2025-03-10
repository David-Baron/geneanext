<?php

//=====================================================================
// Affichage des informations techniques
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

$acces = 'L';
$titre = $LG_Menu_Title['Tech_Info'];
$niv_requis = 'G';
$x = Lit_Env();

require(__DIR__ . '/Gestion_Pages.php');

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'Infos_Tech', '');

echo '<br>' . "\n";
echo my_html(LG_TECH_INFO_VERSION) . LG_SEMIC . $Version . '<br>';
if ($Environnement == 'I') echo my_html(LG_TECH_INFO_ENVIR_INTERNET);
else echo my_html(LG_TECH_INFO_ENVIR_LOCAL);
echo '<br><br><br>' . "\n";

// $x = getPhpinfo();
// var_dump($x);

phpinfo(
    INFO_GENERAL
    # +INFO_CREDITS
    + INFO_CONFIGURATION
    + INFO_MODULES
    + INFO_ENVIRONMENT
    + INFO_VARIABLES
);

Insere_Bas($compl);
?>
</body>

</html>