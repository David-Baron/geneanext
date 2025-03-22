<?php

//=====================================================================
// Affichage de l'historique du nombre d'enfants par femme en fonction de l'année de naissance
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$tab_variables = array('annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

$titre = $LG_Menu_Title['Children_Per_Mother'];        // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 220);

Insere_Haut($titre, $compl, 'Enfants_Femme_Histo', '');

$bloc_annees = 20;
$larg_maxi = 200;

$deb_barre = '<img src="' . $barre_femme . '" height="15" width="';

$max_nb_enfants = 0;
$ref_femme_max_nb_enfants = 0;

// Compatge des enfants par femme dont on connait la date de naissance
$sql = 'select count(*), f.Mere, p.Ne_le'
    . ' from ' . nom_table('filiations') . ' f, ' . nom_table('personnes') . ' p'
    . ' where Mere <> 0'
    . ' and f.Mere = p.Reference'
    . ' and Ne_le LIKE "_________L"';
if (!IS_GRANTED('P')) {
    $sql = $sql . " and Diff_Internet = 'O' ";
}
$sql = $sql
    . ' group by Mere, p.Ne_le'
    . ' order by Ne_Le';
// Stockage des cumuls d'âges des hommes et des femmes dans des tableaux
$presence = false;
if ($res = lect_sql($sql)) {
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $presence = true;
        $nb = $row[0];
        $annee = intval(substr($row[2], 0, 4));
        $ref_annee = intval($annee / $bloc_annees) * $bloc_annees;
        if (!isset($nb_femmes[$ref_annee])) {
            $nb_femmes[$ref_annee] = 0;
            $nb_enfants[$ref_annee] = 0;
        }
        $nb_femmes[$ref_annee]++;
        $nb_enfants[$ref_annee] += $nb;
        if ($nb > $max_nb_enfants) {
            $max_nb_enfants = $nb;
            $ref_femme_max_nb_enfants = $row[1];
        }
    }
}

if ($presence) {
    echo '<br />';
    echo '<table width="50%" border="0" class="classic" align="center" >' . "\n";
    echo '<tr>';
    echo '<th>' . LG_CH_PER_MOTHER_BORN . '</th>';
    echo '<th colspan="2">' . LG_CH_PER_MOTHER_AVG . '</th>';
    echo '</tr>' . "\n";

    // Calcul des moyennes par période
    foreach ($nb_femmes as $key => $value) {
        $moyennes[$key] = $nb_enfants[$key] / $nb_femmes[$key];
    }
    // Moyenne maxi ?
    $maxi = 1;
    if (isset($moyennes)) {
        $maxi = max($moyennes);
    }

    foreach ($moyennes as $key => $value) {
        $borne_sup = $key + $bloc_annees - 1;
        echo '<tr valign="middle">' . "\n";
        echo '<td align="center">' . $key . ' - ' . $borne_sup . '</td>';
        $larg = intval($value / $maxi * $larg_maxi);
        echo '<td>';
        echo $deb_barre . $larg . '" alt="Moyenne" title=""/>';
        //if ($value == $maxi) echo ' *';
        echo '</td>';
        $moy_fr = number_format($value, 2, ',', ' ');
        echo '<td align="center">' . $moy_fr . '</td>';
        echo '</tr>';
    }
    echo '</table>';

    $Nom = '';
    $Prenoms = '';
    if (Get_Nom_Prenoms($ref_femme_max_nb_enfants, $Nom, $Prenoms)) {
        echo '<br />' . LG_CH_PER_MOTHER_MAX_WOMAN . ' : <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $ref_femme_max_nb_enfants . '">' . $Prenoms . '&nbsp;' . $Nom . '</a>';
        echo ' ; ' . LG_CH_PER_MOTHER_SHE_HAD . ' ' . $max_nb_enfants . ' ' . LG_CHILD . pluriel($max_nb_enfants) . '.';
    }
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