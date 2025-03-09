<?php

//=====================================================================
// Liste des zones géographiques
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture

// Recup de la variable passée dans l'URL : type de liste
$Type_Liste = Recup_Variable('Type_Liste', 'C', 'SVDRP');
// Si pas de type de liste demandé (robots ?), on envoie la liste des pays
if (!$Type_Liste)
    $Type_Liste = 'P';

switch ($Type_Liste) {
    case 'S':
        $objet = LG_LAREAS_SUBDIVS;
        break;
    case 'V':
        $objet = LG_LAREAS_TOWNS;
        break;
    case 'D':
        $objet = LG_LAREAS_COUNTIES;
        break;
    case 'R':
        $objet = LG_LAREAS_REGIONS;
        break;
    case 'P':
        $objet = LG_LAREAS_COUNTRIES;
        break;
    default:
        break;
}
$titre = $objet;                       // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/Gestion_Pages.php');

// Sortie dans un fichier CSV ?
$csv_dem = Recup_Variable('csv', 'C', 'ce');
$CSV = false;
if ($csv_dem === 'c') $CSV = true;
if (($SiteGratuit) and (!$Premium)) $CSV = false;

$compl = Ajoute_Page_Info(600, 150);
if ((!$SiteGratuit) or ($Premium)) {
    if ($_SESSION['estCnx']) {
        $compl .= Affiche_Icone_Lien('href="' . my_self() . '?' . Query_Str() . '&amp;csv=c' . '"', 'exp_tab', my_html($LG_csv_export)) . '&nbsp;';
    }
}
Insere_Haut(my_html($objet), $compl, 'Liste_Villes', $Type_Liste);

$n_subdivs = nom_table('subdivisions');
$n_villes = nom_table('villes');
$n_departements = nom_table('departements');
$n_regions = nom_table('regions');
$n_pays = nom_table('pays');

// Lien direct sur la dernière personne zone et possibilité d'insérer une zone
if ($est_gestionnaire) {
    $echo_modif = Affiche_Icone('fiche_edition', my_html($LG_modify)) . '</a>';
    $MaxRef = 0;

    //$sql = 'select max(Identifiant_zone) from ';
    switch ($Type_Liste) {
        case 'S':
            $n_table = $n_subdivs;
            $n_rub_nom = 'Nom_Subdivision';
            break;
        case 'V':
            $n_table = $n_villes;
            $n_rub_nom = 'Nom_Ville';
            break;
        case 'D':
            $n_table = $n_departements;
            $n_rub_nom = 'Nom_Depart_Min';
            break;
        case 'R':
            $n_table = $n_regions;
            $n_rub_nom = 'Nom_Region_Min';
            break;
        case 'P':
            $n_table = $n_pays;
            $n_rub_nom = 'Nom_Pays';
            break;
    }

    $sql = 'SELECT Identifiant_zone, ' . $n_rub_nom . ' FROM ' . $n_table . ' a ' .
        'WHERE a.Identifiant_zone = ( SELECT max( Identifiant_zone ) FROM ' . $n_table . ')';
    if ($resmax = lect_sql($sql)) {
        if ($enrmax = $resmax->fetch(PDO::FETCH_NUM)) {
            $MaxRef = $enrmax[0];
        }
    }
    // Lien direct sur la dernière zone et possibilité d'insérer
    if ($MaxRef > 0) {
        $aff_nom = my_html($enrmax[1]);
        switch ($Type_Liste) {
            case 'S':
                echo my_html(LG_LAREAS_SUBDIV_LAST) . LG_SEMIC . '<a href="Fiche_Subdivision.php?Ident=' . $MaxRef . '">' . $aff_nom . '</a>';
                echo '&nbsp;<a href="Edition_Subdivision.php?Ident=' . $MaxRef . '">' . $echo_modif . '<br />' . "\n";
                break;
            case 'V':
                echo my_html(LG_LAREAS_TOWN_LAST) . LG_SEMIC . '<a href="Fiche_Ville.php?Ident=' . $MaxRef . '">' . $aff_nom . '</a>';
                echo '&nbsp;<a href="Edition_Ville.php?Ident=' . $MaxRef . '">' . $echo_modif . '<br />' . "\n";
                break;
            case 'D':
                echo my_html(LG_LAREAS_COUNTY_LAST) . LG_SEMIC . '<a href="Liste_Villes.php?Type_Liste=V#' . $enrmax[1] . '">' . my_html($enrmax[1]) . '</a>';
                echo '&nbsp;<a href="Edition_Depart.php?Ident=' . $MaxRef . '">' . $echo_modif . '<br />' . "\n";
                break;
            case 'R':
                echo my_html(LG_LAREAS_REGION_LAST) . LG_SEMIC . '<a href="Liste_Villes.php?Type_Liste=D#' . $enrmax[1] . '">' . my_html($enrmax[1]) . '</a>';
                echo '&nbsp;<a href="Edition_Region.php?Ident=' . $MaxRef . '">' . $echo_modif . '<br />' . "\n";
                break;
                //case 'P' : $n_table = $n_pays; $n_rub_nom = 'Nom_Pays'; break;
        }
    }
    $resmax->closeCursor();
    switch ($Type_Liste) {
        case 'S':
            echo my_html(LG_LAREAS_SUBDIV_ADD) . LG_SEMIC . '<a href="Edition_Subdivision.php?Ident=-1">' . Affiche_Icone('ajouter', $LG_add) . '</a><br /><br />' . "\n";
            break;
        case 'V':
            echo my_html(LG_LAREAS_TOWN_ADD) . LG_SEMIC . '<a href="Edition_Ville.php?Ident=-1">' . Affiche_Icone('ajouter', $LG_add) . '</a><br /><br />' . "\n";
            break;
        case 'D':
            echo my_html(LG_LAREAS_COUNTY_ADD) . LG_SEMIC . ' <a href="Edition_Depart.php?Ident=-1">' . Affiche_Icone('ajouter', $LG_add) . '</a><br /><br />' . "\n";
            break;
        case 'R':
            echo my_html(LG_LAREAS_REGION_ADD) . LG_SEMIC . '<a href="Edition_Region.php?Ident=-1">' . Affiche_Icone('ajouter', $LG_add) . '</a><br /><br />' . "\n";
            break;
    }
}

// Constitution de la requête d'extraction'
switch ($Type_Liste) {
    case 'S': // Requête pour la liste des subdivisions
        $sql = 'select s.Identifiant_zone, Nom_Subdivision, Nom_Ville, s.Latitude, s.Longitude ' .
            ' from ' . $n_subdivs . ' s, ' . $n_villes . ' v ' .
            ' where v.Identifiant_zone = s.Zone_Mere ' .
            ' and s.Identifiant_zone <> 0' .
            ' order by Nom_Ville, Nom_Subdivision';
        break;
    case 'V': // Requête pour la liste des villes
        $sql = 'select v.Identifiant_zone, Nom_Ville, Nom_Depart_Min, Latitude, Longitude ' .
            ' from ' . $n_villes . ' v, ' . $n_departements . ' d ' .
            ' where d.Identifiant_zone = v.Zone_Mere ' .
            ' and v.Identifiant_zone <> 0' .
            ' order by Nom_Depart_Min, Nom_Ville';
        break;
    case 'D': // Requête pour la liste des départements
        $sql = 'select d.Identifiant_zone, Nom_Depart_Min,Nom_Region_Min ' .
            ' from ' . $n_departements . ' d, ' . $n_regions . ' r ' .
            ' where r.Identifiant_zone = d.Zone_Mere ' .
            ' and d.Identifiant_zone <> 0' .
            ' order by Nom_Region_Min, Nom_Depart_Min';
        break;
    case 'R': // Requête pour la liste des régions
        $sql = 'select r.Identifiant_zone, Nom_Region_Min,Nom_Pays ' .
            ' from ' . $n_regions . ' r, ' . $n_pays . ' p ' .
            ' where p.Identifiant_zone = r.Zone_Mere ' .
            ' and r.Identifiant_zone <> 0' .
            ' order by Nom_Pays, Nom_Region_Min';
        break;
    case 'P': // Requête pour la liste des pays
        $sql = 'select Nom_Pays, Code_Pays_ISO_Alpha, Code_Pays_ISO_Alpha3 ' .
            ' from ' . $n_pays .
            ' where Identifiant_zone <> 0' .
            ' order by Nom_Pays';
        break;
    default:
        break;
}

$res = lect_sql($sql);

$interdits = array("-", "'", " ");

if (!$CSV) {
    if ($Type_Liste != 'P') {
        // Liste des noms : chaque nouveau nom est affiché
        $Anc_Nom = '';
        echo '<table align="center" width="95%" border="0" cellspacing="0" cellpadding="0">' . "\n";
        echo '<tr align="center"><td>' . "\n";
        $premier = true;
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $Nouv_Nom = my_html($row[2]);
            $Nouv_Nom_Int = my_html($row[2]);
            $Nouv_Nom_Int = str_replace($interdits, '', $Nouv_Nom);
            if ($Nouv_Nom_Int != $Anc_Nom) {
                echo '<a ';
                if ($premier) {
                    echo 'id="top" ';
                    $premier = false;
                }
                echo 'href="#' . $Nouv_Nom_Int . '">' . $Nouv_Nom . '</a>&nbsp;' . "\n";
                $Anc_Nom = $Nouv_Nom_Int;
            }
        }
        echo "</td></tr>\n";
        echo "</table>\n";
        echo '<hr width="90%"/><br />' . "\n";
    }
}

if ($res->rowCount() > 0) {
    if ($CSV) {
        switch ($Type_Liste) {
            case 'S':
                $nom_fic = $chemin_exports . 'liste_subdivisions.csv';
                break;
            case 'V':
                $nom_fic = $chemin_exports . 'liste_villes.csv';
                break;
            case 'D':
                $nom_fic = $chemin_exports . 'liste_departements.csv';
                break;
            case 'R':
                $nom_fic = $chemin_exports . 'liste_regions.csv';
                break;
            case 'P':
                $nom_fic = $chemin_exports . 'liste_pays.csv';
                break;
            default:
                break;
        }
        $fp = ouvre_fic($nom_fic, 'w+');
        // Ecriture de l'entête
        $ligne = '';
        switch ($Type_Liste) {
            case 'S':
                ecrire($fp, 'Identifiant_Subdivision;Nom_Subdivision;Nom_Ville;Latitude;Longitude;');
                break;
            case 'V':
                ecrire($fp, 'Identifiant_Ville;Nom_Ville;Nom_Depart_Min;Latitude;Longitude;');
                break;
            case 'D':
                ecrire($fp, 'Identifiant_Departement;Nom_Depart;Nom_Region;');
                break;
            case 'R':
                ecrire($fp, 'Identifiant_Region;Nom_Region;Nom_Pays;');
                break;
            case 'P':
                ecrire($fp, 'Nom_Pays;Code_Pays_ISO_Alpha;Code_Pays_ISO_Alpha3;');
                break;
            default:
                break;
        }
    }
    // Repositionnement dans l'ensemble des données
    $res->closeCursor();
    $res = lect_sql($sql);

    // Optimisation : préparation echo des images
    $echo_haut  = '</a>' . Affiche_Icone_Lien('href="#top"', 'page_haut', LG_LAREAS_TOP) . '<br />';

    $Anc_Nom = '';
    $tab = '&nbsp;&nbsp;&nbsp;';
    while ($row = $res->fetch(PDO::FETCH_NUM)) {
        if (!$CSV) {
            if ($Type_Liste != 'P') {
                $Nouv_Nom = my_html($row[2]);
                $Nouv_Nom_Int = my_html($row[2]);
                $Nouv_Nom_Int = str_replace($interdits, '', $Nouv_Nom);
                if ($Nouv_Nom_Int != $Anc_Nom) {
                    echo '<br /><a name="' . $Nouv_Nom_Int . '">' . $Nouv_Nom . $echo_haut . "\n";
                    $Anc_Nom = $Nouv_Nom_Int;
                }
            }
            switch ($Type_Liste) {
                case 'S':
                    echo $tab . '<a href="Fiche_Subdivision.php?Ident=' . $row[0] . '">' . my_html($row[1]) . '</a>';
                    $Lat_V = $row[3];
                    $Long_V = $row[4];
                    appelle_carte_osm();
                    if ($est_gestionnaire) {
                        echo '&nbsp;<a href="Edition_Subdivision.php?Ident=' . $row[0] . '">' . $echo_modif . "\n";
                    }
                    break;
                case 'V':
                    echo $tab . '<a href="Fiche_Ville.php?Ident=' . $row[0] . '">' . my_html($row[1]) . '</a>';
                    $Lat_V = $row[3];
                    $Long_V = $row[4];
                    appelle_carte_osm();
                    if ($est_gestionnaire) {
                        echo '&nbsp;<a href="Edition_Ville.php?Ident=' . $row[0] . '">' . $echo_modif . "\n";
                    }
                    break;
                case 'D':
                    $n_dep = str_replace($interdits, '', $row[1]);
                    echo $tab . '<a href="Liste_Villes.php?Type_Liste=V#' . $n_dep . '">' . my_html($row[1]) . "</a>";
                    if ($est_gestionnaire) {
                        echo '&nbsp;<a href="Edition_Depart.php?Ident=' . $row[0] . '">' . $echo_modif . "\n";
                    }
                    break;
                case 'R':
                    $n_reg = str_replace($interdits, '', $row[1]);
                    echo $tab . '<a href="Liste_Villes.php?Type_Liste=D#' . $n_reg . '">' . my_html($row[1]) . "</a>";
                    if ($est_gestionnaire) {
                        echo '&nbsp;<a href="Edition_Region.php?Ident=' . $row[0] . '">' . $echo_modif . "\n";
                    }
                    break;
                case 'P':
                    $n_pays = str_replace($interdits, '', $row[0]);
                    echo $tab . '<a href="Liste_Villes.php?Type_Liste=R#' . $n_pays . '">' . my_html($row[0]) . "</a>";
                    break;
                default:
                    break;
            }
            echo "<br />\n";
        } else {
            $ligne = '';
            if ($debug) {
                echo $tab;
                var_dump($row);
                echo '<br />';
            }
            switch ($Type_Liste) {
                case 'S': // s.Identifiant_zone, Nom_Subdivision, Nom_Ville, s.Latitude, s.Longitude
                    constit_ligne(4);
                    break;
                case 'V': // v.Identifiant_zone, Nom_Ville, Nom_Depart_Min, Latitude, Longitude
                    constit_ligne(4);
                    break;
                case 'D': // d.Identifiant_zone, Nom_Depart_Min, Nom_Region_Min
                    constit_ligne(2);
                    break;
                case 'R': // .Identifiant_zone, Nom_Region_Min,Nom_Pays
                    constit_ligne(2);
                    break;
                case 'P': // Nom_Pays, Code_Pays_ISO_Alpha, Code_Pays_ISO_Alpha3
                    constit_ligne(2);
                    break;
                default:
                    break;
            }
        }
    }
    if ($CSV) {
        fclose($fp);
        echo '<br /><br />' . my_html($LG_csv_available_in) . ' <a href="' . $nom_fic . '" target="_blank">' . $nom_fic . '</a><br />' . "\n";
    }
}

Insere_Bas($compl);

function constit_ligne($max_champ)
{
    global $ligne, $row, $fp, $debug;
    for ($nb = 0; $nb <= $max_champ; $nb++) {
        $ligne .= $row[$nb] . ';';
    }
    if ($debug) echo $ligne . '<br />';
    ecrire($fp, $ligne);
}
?>
</body>

</html>