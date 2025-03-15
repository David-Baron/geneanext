<?php
//====================================================================
//  Rectification de zones nulles
// a engistrer dans le repertoire www en enlevant l'extension ".txt" et a executer dans le navigateur
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'M';
$titre = 'Rectification de zones nulles en base';
$x = Lit_Env();
$niv_requis = 'G';                // Page reservee au profil gestionnaire
require(__DIR__ . '/app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 250);
Insere_Haut(my_html($titre), $compl, 'Rectif_Null', '');

$LG_mod = ' enregistrement(s) rectifié(s)';

rectif_null("UPDATE " . nom_table('evenements') . " SET Identifiant_zone = 0 WHERE Identifiant_zone is null", 'Evènements : Identifiant_zone');
rectif_null("UPDATE " . nom_table('unions') . " SET Ville_Notaire = 0 WHERE Ville_Notaire is null", 'Unions : Ville_Notaire');
rectif_null("UPDATE " . nom_table('villes') . " SET latitude = 0 WHERE latitude is null", 'Villes : latitude');
rectif_null("UPDATE " . nom_table('villes') . " SET longitude = 0 WHERE longitude is null", 'Villes : longitude');

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';

function rectif_null($req, $lib)
{
    global $enr_mod, $LG_mod;
    $res = maj_sql($req);
    echo '<br>' . $lib . ' : ' . $enr_mod . $LG_mod . '<br>';
}

?>
</body>

</html>