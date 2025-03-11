<?php
//=====================================================================
// Erreur sur profil
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$titre = $LG_function_noavailable_profile;        // Titre pour META
$acces = 'L';                                    // Type d'accès de la page : (M)ise à jour, (L)ecture
require(__DIR__ . '/app/ressources/gestion_pages.php');
$x = Lit_Env();

Insere_Haut($titre, '', 'Erreur_Profil', '');

aff_erreur($LG_function_noavailable_profile);

Insere_Bas('');
?>
</body>

</html>