<?php

//=====================================================================
// Répartition des âges de décès sur une période donnée en fonction de l'année de naissance
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$tab_variables = array('cache', 'annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

// Recup de la variable passée dans l'URL : année de début et de fin
$Debut = Recup_Variable('Debut', 'N');
$Fin = Recup_Variable('Fin', 'N');

// Gestion standard des pages
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = LG_CH_HISTO_DEATH_TITLE . $Debut . '-' . $Fin;      // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = Ajoute_Page_Info(600, 180);

Insere_Haut($titre, $compl, 'Histo_Ages_Deces', $Debut . '-' . $Fin);

// Zones pour les doyen(ne)s
$max_mois_h = 0;
$max_mois_f = 0;
$ref_max_h = 0;
$ref_max_f = 0;

for ($nb = 0; $nb <= 10; $nb++) $nb_pers[$nb] = 0;

// Zone pour les incohérences de base : décès avant naissance...
$warning_negatif = false;

// Récupération des personnes pour lesquelles on peut calculer un âge, partie hommes
$deb_sql = 'SELECT Ne_le, Decede_Le, Reference FROM ' . nom_table('personnes') .
    " WHERE Ne_le LIKE '_________L'" .
    " and Ne_le >= '" . $Debut . "0101L'" .
    " and Ne_le <= '" . $Fin . "1231L'" .
    " AND Decede_Le LIKE '_________L' ";
if (!$est_privilegie) {
    $deb_sql .= "and Diff_Internet = 'O' ";
}
$sql = $deb_sql . " and Sexe = 'm' order by Ne_Le";

if ($res = lect_sql($sql)) {
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $mois = Age_Mois($row[0], $row[1]);
        $ans = floor($mois / 12);
        if ($ans < 100) $rang = floor($ans / 10);
        else            $rang = 10;
        if ($rang < 0)
            $warning_negatif = true;
        else
            $nb_pers[$rang]++;
        if ($mois > $max_mois_h) {
            $max_mois_h = $mois;
            $ref_max_h = $row[2];
        }
    }
}

//for ($nb=0;$nb<=10;$nb++) echo $nb_pers[$nb].', ';

$datas_h = '';
$labels = '';
for ($nb = 0; $nb < 10; $nb++) {
    $datas_h .= $nb_pers[$nb] . '*';
    $labels .= $nb . '0-' . $nb . '9' . '*';
}
$datas_h .= $nb_pers[10] . '*';
$labels .= '100et+' . '*';

$datas_h = substr($datas_h, 0, strlen($datas_h) - 1);
$labels = substr($labels, 0, strlen($labels) - 1);

// Récupération des personnes pour lesquelles on peut calculer un âge, partie femmes
$sql = $deb_sql . " and Sexe = 'f' order by Ne_Le";

for ($nb = 0; $nb <= 10; $nb++) $nb_pers[$nb] = 0;

if ($res = lect_sql($sql)) {
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $mois = Age_Mois($row[0], $row[1]);
        $ans = floor($mois / 12);
        if ($ans < 100) $rang = floor($ans / 10);
        else            $rang = 10;
        if ($rang < 0)
            $warning_negatif = true;
        else
            $nb_pers[$rang]++;
        if ($mois > $max_mois_f) {
            $max_mois_f = $mois;
            $ref_max_f = $row[2];
        }
    }
}

//for ($nb=0;$nb<=10;$nb++) echo $nb_pers[$nb].', ';

$datas_f = '';
for ($nb = 0; $nb < 10; $nb++) {
    $datas_f .= $nb_pers[$nb] . '*';
}
$datas_f .= $nb_pers[10] . '*';

$datas_f = substr($datas_f, 0, strlen($datas_f) - 1);

/*
echo '<br>';
echo 'H : '.$datas_h.'<br>';
echo 'F : '.$datas_f.'<br>';
echo 'Labels : '.$labels.'<br>';
*/

$largeur = 300;
$hauteur = 250;
$largeur_labels = 180;

require(__DIR__ . '/app/ressources/piechart3.php');
echo '<br />';
echo '<table align="center">';
echo '<tr>';
echo '<th class="titre_hommes" align="center">' . LG_CH_HISTO_AGE_MEN . '</th>';
echo '<th class="titre_femmes" align="center">' . LG_CH_HISTO_AGE_WOMEN . '</th>';
echo '</tr>';
echo '<tr>';
echo '<td align="center" class="case_hommes">';
$x = aff_degrade($datas_h, $labels, 'B', $largeur, $hauteur, '1', $largeur_labels, ' ' . LG_CH_HISTO_YEARS);
echo '</td>' . "\n";
echo '<td align="center" class="case_femmes">';
$x = aff_degrade($datas_f, $labels, 'R', $largeur, $hauteur, '2', $largeur_labels, ' ' . LG_CH_HISTO_YEARS);
echo '</td>' . "\n";
echo '</tr>';
echo '</table>';

$Nom = '';
$Prenoms = '';
if (Get_Nom_Prenoms($ref_max_h, $Nom, $Prenoms)) {
    echo '<br />' . LG_CH_HISTO_AGE_OLDEST_M . ' : <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $ref_max_h . '">' . $Prenoms . '&nbsp;' . $Nom . '</a> (' . Decompose_Mois($max_mois_h) . ')';
}
if (Get_Nom_Prenoms($ref_max_f, $Nom, $Prenoms)) {
    echo '<br />' . LG_CH_HISTO_AGE_OLDEST_W . ' : <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $ref_max_f . '">' . $Prenoms . '&nbsp;' . $Nom . '</a> (' . Decompose_Mois($max_mois_f) . ')';
}

// Un décès est antérieur à la date de naissance
if ($est_contributeur) {
    if ($warning_negatif) {
        echo '<br><br><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Conseil" title="Conseil"> ' . LG_CH_HISTO_AGE_CTL_BIRTH;
    }
}

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '?' . Query_Str());

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