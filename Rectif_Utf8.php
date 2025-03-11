<?php
//====================================================================
//  Rectification après import fichier en UTF-8
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');
require(__DIR__ . '/app/ressources/rectif_utf8_commun.php');

$acces = 'M';
//$titre = 'Rectification des caractères UTF-8';
$titre = $LG_Menu_Title['Rect_Utf'];
$x = Lit_Env();
$niv_requis = 'G';                // Page réservée au profil gestionnaire
require(__DIR__ . '/app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 250);
Insere_Haut(my_html($titre), $compl, 'Rectif_Utf8', '');

rectif_UTF8();

echo '<br>' . my_html($LG_Rect_Utf_Msg_Beg . $nb_req . $LG_Rect_Utf_Msg_End) . '<br>';

Insere_Bas($compl);


?>
</body>

</html>