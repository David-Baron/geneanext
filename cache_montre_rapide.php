<?php

//=====================================================================
// Cacher|montrer une personne en 1 clic à partir de la fiche d'une personne
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

// On simule le bouton OK pour ne pas écrire l'entête de la page
$ok = 'OK'; // TODO: lol

if (($_SESSION['estPrivilegie']) or ($_SESSION['estContributeur']) or ($_SESSION['estGestionnaire'])) {

    $acces = 'M';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
    $titre = 'Cacher/montrer une personne';    // Titre pour META
    $x = Lit_Env();
    require(__DIR__ . '/app/ressources/gestion_pages.php');

    $Refer = Recup_Variable('Refer', 'N');
    $Diff = Recup_Variable('Diff', 'C', 'NO');

    $sql = 'update ' . nom_table('personnes') . ' set Diff_Internet = "' . $Diff . '" where Reference = ' . $Refer;
    if ($result = maj_sql($sql)) maj_date_site(false);
}

// Retour sur la page précédente
Retour_Ar();

?>

</body>

</html>