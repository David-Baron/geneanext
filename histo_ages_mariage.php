<?php

//=====================================================================
// Répartition des âges de mariage ou de 1er enfant sur une période donnée en fonction de l'année de naissance
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

// Recup de la variable passée dans l'URL : année de début et de fin et type d'historique
$Debut = Recup_Variable('Debut', 'N');
$Fin = Recup_Variable('Fin', 'N');
$Type_Histo = Recup_Variable('Type', 'C', 'UF');

// Gestion standard des pages
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
// Titre pour META
switch ($Type_Histo) {
    case 'U':
        $titre = $LG_Menu_Title['First_Wedding'];
        $Ch_Histo_Age_Youngest_M = LG_CH_HISTO_AGE_YOUNGEST_M;
        $Ch_Histo_Age_Youngest_W = LG_CH_HISTO_AGE_YOUNGEST_W;
        $ch_warn_age = LG_CH_HISTO_AGE_CTL_UNION;
        break;
    case 'F':
        $titre = $LG_Menu_Title['Histo_First_Child'];
        $Ch_Histo_Age_Youngest_M = LG_CH_HISTO_AGE_YOUNGEST_FATH;
        $Ch_Histo_Age_Youngest_W = LG_CH_HISTO_AGE_YOUNGEST_MOTH;
        $ch_warn_age = LG_CH_HISTO_AGE_CTL_CHILD;
        break;
}
$titre .= ' ' . $Debut . '-' . $Fin;
$x = Lit_Env();

require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$compl = Ajoute_Page_Info(600, 180);

Insere_Haut($titre, $compl, 'Histo_Ages_Mariage', $Debut . '-' . $Fin);

// Zones pour les cadet(te)s
$min_mois_h = 9999;
$min_mois_f = 9999;
$ref_min_h = 0;
$ref_min_f = 0;

for ($nb = 0; $nb <= 10; $nb++) $nb_pers[$nb] = 0;

$n_personnes = nom_table('personnes');

if ($Type_Histo == 'U') {
    // Récupération des personnes pour lesquelles on peut calculer un âge au mariage
    $deb_sql = 'SELECT Ne_le, Maries_Le, p.Reference  ' .
        'FROM ' . $n_personnes . ' p, ' . nom_table('unions') . ' u ' .
        "WHERE Ne_le LIKE '_________L' " .
        " AND Ne_le >= '" . $Debut . "0101L'" .
        " AND Ne_le <= '" . $Fin . "1231L'" .
        ' AND (p.Reference = u.Conjoint_1 or p.Reference = u.Conjoint_2) ' .
        " AND Maries_Le LIKE '_________L' ";
    if (!$est_privilegie) {
        $deb_sql .= "and Diff_Internet = 'O' ";
    }
    $order = ' order by Ne_Le, p.Reference, Maries_Le';
}
if ($Type_Histo == 'F') {
    // Récupération des personnes pour lesquelles on peut calculer un âge de paternité / maternité
    $deb_sql = 'SELECT p.Ne_le, e.Ne_le, p.Reference  ' .
        'FROM ' . $n_personnes . ' p, ' . nom_table('filiations') . ' f, ' . $n_personnes . ' e ' .
        "WHERE p.Ne_le LIKE '_________L' " .
        " AND p.Ne_le >= '" . $Debut . "0101L'" .
        " AND p.Ne_le <= '" . $Fin . "1231L'" .
        ' AND (p.Reference = f.Pere or p.Reference = f.Mere) ' .
        ' AND e.Reference = f.Enfant ' .
        " AND e.Ne_le LIKE '_________L' ";
    if (!$est_privilegie) {
        $deb_sql .= "and p.Diff_Internet = 'O' ";
    }
    $order = ' order by p.Ne_Le, p.Reference, e.Ne_le';
}

// Zone pour les incohérences de base : enfant né avant parent, union avant naissance...
$warning_negatif = false;

// Récupération des personnes pour lesquelles on peut calculer un âge, partie hommes
$sql = $deb_sql . " and p.Sexe = 'm'" . $order;
$ref_anc = 0;
if ($res = lect_sql($sql)) {
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $ref_nouv = $row[2];
        // Traitement en rupture sur la référence
        if ($ref_nouv != $ref_anc) {
            $ref_anc = $ref_nouv;
            $mois = Age_Mois($row[0], $row[1]);
            // Cadet
            if ($mois < $min_mois_h) {
                $min_mois_h = $mois;
                $ref_min_h = $ref_nouv;
            }
            $ans = floor($mois / 12);
            if ($ans < 100) $rang = floor($ans / 10);
            else            $rang = 10;
            // var_dump($rang);
            if ($rang < 0)
                $warning_negatif = true;
            else
                $nb_pers[$rang]++;
            if ($debug) {
                echo $ref_nouv . ', âge : ' . Decompose_Mois($mois)
                    . ' : <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $ref_nouv . '">' . $ref_nouv . '</a>'
                    . '<br />';
            }
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
$labels .= '100 et+' . '*';

$datas_h = substr($datas_h, 0, strlen($datas_h) - 1);
$labels = substr($labels, 0, strlen($labels) - 1);

for ($nb = 0; $nb <= 10; $nb++) $nb_pers[$nb] = 0;

// Récupération des personnes pour lesquelles on peut calculer un âge, partie femmes
$sql = $deb_sql . " and p.Sexe = 'f'" . $order;
$ref_anc = 0;
if ($res = lect_sql($sql)) {
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        $ref_nouv = $row[2];
        // Traitement en rupture sur la référence		
        if ($ref_nouv != $ref_anc) {
            $ref_anc = $ref_nouv;
            $mois = Age_Mois($row[0], $row[1]);
            // Cadette
            if ($mois < $min_mois_f) {
                $min_mois_f = $mois;
                $ref_min_f = $ref_nouv;
            }
            $ans = floor($mois / 12);
            if ($ans < 100) $rang = floor($ans / 10);
            else            $rang = 10;
            if ($rang < 0)
                $warning_negatif = true;
            else
                $nb_pers[$rang]++;
            if ($debug) {
                echo $ref_nouv . ', âge : ' . Decompose_Mois($mois)
                    . ' : <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $ref_nouv . '">' . $ref_nouv . '</a>'
                    . '<br />';
            }
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

if ($debug) {
    var_dump($datas_h);
    var_dump($datas_f);
    var_dump($labels);
}

$largeur = 300;
$hauteur = 250;
$largeur_labels = 180;

require(__DIR__ . '/app/ressources/piechart3.php');
echo '<br />';
echo '<table align="center" border="0">';
echo '<tr>';
echo '<th class="titre_hommes" align="center">' . LG_CH_HISTO_AGE_MEN . '</th>';
echo '<th class="titre_femmes" align="center">' . LG_CH_HISTO_AGE_WOMEN . '</th>';
echo '</tr>';
echo '<tr>';
echo '<td align="center" class="case_hommes">';
$x = aff_degrade($datas_h, $labels, 'B', $largeur, $hauteur, '1', $largeur_labels, ' ans');
echo '</td>' . "\n";
echo '<td align="center" class="case_femmes">';
$x = aff_degrade($datas_f, $labels, 'R', $largeur, $hauteur, '2', $largeur_labels, ' ans');
echo '</td>' . "\n";
echo '</tr>';
echo '</table>';

$Nom = '';
$Prenoms = '';
if (Get_Nom_Prenoms($ref_min_h, $Nom, $Prenoms)) {
    echo '<br />' . $Ch_Histo_Age_Youngest_M . ' : <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $ref_min_h . '">' . $Prenoms . '&nbsp;' . $Nom . '</a> (' . Decompose_Mois($min_mois_h) . ')';
}
if (Get_Nom_Prenoms($ref_min_f, $Nom, $Prenoms)) {
    echo '<br />' . $Ch_Histo_Age_Youngest_W . ' : <a href="' . $root . '/fiche_fam_pers.php?Refer=' . $ref_min_f . '">' . $Prenoms . '&nbsp;' . $Nom . '</a> (' . Decompose_Mois($min_mois_f) . ')';
}

// Un age d'évènement (naissance enfant, union) est antérieur à la date de naissance
if ($est_contributeur) {
    if ($warning_negatif) {
        echo '<br><br><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Conseil" title="Conseil"> ' . $ch_warn_age;
    }
}

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '?' . Query_Str());
echo '<table cellpadding="0" width="100%">';
echo '<tr>';
echo '<td align="right">';
echo $compl;
echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/' . $Icones['home'] . '" alt="Accueil" title="Accueil" /></a>';
echo "</td>";
echo '</tr>';
echo '</table>';

?>
</body>

</html>