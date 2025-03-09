<?php

//=====================================================================
// Affichage des informations techniques
// (c) JLS
// UTF-8
//=====================================================================

session_start();

include('fonctions.php');
$acces = 'L';
$titre = $LG_Menu_Title['Tech_Info'];
$niv_requis = 'G';
$x = Lit_Env();

include('Gestion_Pages.php');

$compl = Ajoute_Page_Info(600,150);
Insere_Haut($titre,$compl,'Infos_Tech','');

echo '<br>'."\n";
echo my_html(LG_TECH_INFO_VERSION).LG_SEMIC.$Version.'<br>';
if ($Environnement == 'I') echo my_html(LG_TECH_INFO_ENVIR_INTERNET);
else echo my_html(LG_TECH_INFO_ENVIR_LOCAL);
echo '<br><br><br>'."\n";

// $x = getPhpinfo();
// var_dump($x);

phpinfo(
 INFO_GENERAL
//+INFO_CREDITS
+INFO_CONFIGURATION
+INFO_MODULES
+INFO_ENVIRONMENT
+INFO_VARIABLES
);

Insere_Bas($compl);
?>
</body>
</html>