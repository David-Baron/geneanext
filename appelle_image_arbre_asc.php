<?php
// Appel de l'image générée pour un arbre ascendant

require(__DIR__ . '/app/bootstrap.php');

$acces = 'L';                    // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_assc_tree;            // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

$compl = '';

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer', 'N');

echo '</head>' . "\n";
echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
echo '<table cellpadding="0" width="100%">' . "\n";
echo '<tr>' . "\n";
echo '<td align="center"><b> </b></td>' . "\n";
echo '</tr>' . "\n";
echo '</table>' . "\n";

echo '<table width="90%">';
echo '<tr><td align="center"><img src="image_arbre_asc.php?Refer=' . $Refer . '" alt="Image"/></td></tr>';
echo '</table>';

?>
</body>

</html>