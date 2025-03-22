<?php
//=====================================================================
// Erreur sur profil
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$titre = $LG_function_noavailable_profile;        // Titre pour META

require(__DIR__ . '/../app/ressources/gestion_pages.php');
$x = Lit_Env();

Insere_Haut($titre, '', 'Erreur_Profil', '');

echo '<center><font color="red"><br><br><br><h2>' . $LG_function_noavailable_profile . '</h2></font></center>';
echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';
?>
</body>

</html>