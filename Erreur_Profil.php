<?php
//=====================================================================
// Erreur sur profil
//=====================================================================

session_start();

include('fonctions.php');
$titre = $LG_function_noavailable_profile;        // Titre pour META
$acces = 'L';                                    // Type d'accès de la page : (M)ise à jour, (L)ecture
include('Gestion_Pages.php');
$x = Lit_Env();

Insere_Haut($titre, '', 'Erreur_Profil', '');

aff_erreur($LG_function_noavailable_profile);

Insere_Bas('');
?>
</body>

</html>