<?php

//=====================================================================
// Liste des noms non utilisés par des personnes
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array('ok', 'annuler', 'S_Int', 'idNom');

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$ok  = Secur_Variable_Post($ok, strlen($lib_Supprimer), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Gestion standard des pages
$acces = 'M';                                  // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Name_Not_Used'];     // Titre pour META
$niv_requis = 'C';                               // Page réservée à partir du profil contributeur
$x = Lit_Env();

require(__DIR__ . '/app/ressources/gestion_pages.php');

// Verrouillage de la gestion des documents sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = Ajoute_Page_Info(600, 200);

Insere_Haut($titre, $compl, 'Liste_Noms_Non_Ut', '');

//  ===== Appliquer les suppressions demandées
if ($ok == $lib_Supprimer) {
    $nombre = count($idNom);
    $deb_req  = 'delete from ' . nom_table('noms_famille') . ' where idNomFam = ';
    $nb_sup = 0;
    for ($ligne = 0; $ligne < $nombre; $ligne++) {
        $idNomLu = $idNom[$ligne];
        if (isset($S_Int[$ligne])) {
            $req = $deb_req . $idNomLu;
            $Res = maj_sql($req);
            $nb_sup++;
        }
    }
    if ($nb_sup) maj_date_site();
    $plu1 = pluriel($nb_sup);
    echo $nb_sup . ' ' . $LG_Names_NU_Del . $plu1 . ' ' . $LG_Names_NU_Req . $plu1 . '.<br />';
}

$sql = 'SELECT idNomFam, nomFamille '
    . 'FROM ' . nom_table('noms_famille') . ' nf '
    . 'WHERE NOT EXISTS(SELECT 1 '
    . 'FROM ' . nom_table('noms_personnes') . ' np '
    . 'WHERE nf.idNomFam = np.idNom)'
    . 'ORDER BY nomFamille';

$res = lect_sql($sql);
$nb_lignes = $res->rowCount();

if ($nb_lignes > 0) {

    echo '<form id="saisie" method="post">';

    bt_ok_an_sup($lib_Supprimer, $lib_Annuler, '', '', false);

    echo '<br />' . "\n";
    echo '<table width="35%" border="0" class="classic" align="center" >' . "\n";
    echo '<tr>';
    echo '<th width="40%">' . $lib_Supprimer;
    echo '&nbsp;<input type="checkbox" id="selTous" name="selTous" value="on" onclick="checkUncheckAll(this);"/>&nbsp;'
        . '<label for="selTous">' . $LG_All . '</label>';
    echo '</th>';
    echo '<th width="60%">' . $LG_Name . 's</th>';
    echo '</tr>' . "\n";
    $num_lig = 0;

    $deb_visu  = '&nbsp;<a href="' . $root . '/fiche_nomfam.php?idNom=';
    $deb_modif = 'href="' . $root . '/edition_nomfam.php?idNom=';
    $numLig = 0;

    while ($enr = $res->fetch(PDO::FETCH_NUM)) {

        echo '<tr>' . "\n";
        $nom = $enr[1];
        $id_nom = $enr[0];

        echo '<td align="center">';
        echo  '<input type="checkbox" name="S_Int[' . $numLig . ']"/>';
        echo  '<input type="hidden" name="idNom[' . $numLig . ']" value="' . $id_nom . '"/>';
        echo '</td>' . "\n";

        echo '<td>' . $deb_visu . $enr[0] . '&amp;Nom=' . $nom . '">' . $nom . '</a>';

        if ($est_gestionnaire)
            echo '&nbsp;' . Affiche_Icone_Lien($deb_modif . $id_nom . '"', 'fiche_edition', 'Modifier');

        echo '</td>';
        echo '</tr>' . "\n";

        $numLig++;;
    }

    $res->closeCursor();

    echo '</table>' . "\n";
    echo '<br />' . "\n";

    bt_ok_an_sup($lib_Supprimer, $lib_Annuler, '', '', false, true);
    echo '</form>';
}

echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';

?>
</body>

</html>