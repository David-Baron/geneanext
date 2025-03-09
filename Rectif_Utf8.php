<?php
//====================================================================
//  Rectification après import fichier en UTF-8
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');
require(__DIR__ . '/Rectif_Utf8_Commun.php');

$acces = 'M';
//$titre = 'Rectification des caractères UTF-8';
$titre = $LG_Menu_Title['Rect_Utf'];
$x = Lit_Env();
$niv_requis = 'G';                // Page réservée au profil gestionnaire
require(__DIR__ . '/Gestion_Pages.php');

$compl = Ajoute_Page_Info(600, 250);
Insere_Haut(my_html($titre), $compl, 'Rectif_Utf8', '');

rectif_UTF8();

echo '<br/ >' . my_html($LG_Rect_Utf_Msg_Beg . $nb_req . $LG_Rect_Utf_Msg_End) . '<br/ >';

Insere_Bas($compl);


?>
</body>

</html>