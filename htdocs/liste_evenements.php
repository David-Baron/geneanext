<?php
//====================================================================
//  Affichage de la liste des évènements éventuellement par type
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('P')) {
    header('Location: ' . $root . '/');
    exit();
}

$actu = Recup_Variable('actu', 'C', 'xo');
$actualites = ($actu === 'o' ? true : false);
$prof = Recup_Variable('prof', 'C', 'xo');
$professions = ($prof === 'o' ? true : false);

$titre = $LG_Menu_Title['Event_List'];       // Titre pour META
if ($actualites) $titre = $LG_Menu_Title['News_List'];
if ($professions) $titre = $LG_Menu_Title['Jobs_List'];

$x = Lit_Env();

require(__DIR__ . '/../app/ressources/gestion_pages.php');          // Appel de la gestion standard des pages

// Recup de la variable passée dans l'URL : sortie dans un fichier CSV ?
$csv_dem = Recup_Variable('csv', 'C', 'ce');
$CSV = false;
if ($csv_dem === 'c') $CSV = true;
if (($SiteGratuit) and (!$Premium)) $CSV = false;

$compl = Ajoute_Page_Info(600, 200);
if ((!$SiteGratuit) or ($Premium)) {
    if (IS_AUTHENTICATED())
        $compl .= Affiche_Icone_Lien('href="' . $root . '/liste_evenement?csv=c"', 'exp_tab', $LG_csv_export) . '&nbsp;';
}

Insere_Haut(my_html($titre), $compl, 'Liste_Evenements', '');

$n_evenements = nom_table('evenements');
$n_personnes = nom_table('personnes');
$n_participe = nom_table('participe');
$n_concerne_objet = nom_table('concerne_objet');
$n_unions = nom_table('unions');

// Optimisation : préparation echo des images
$texte = $LG_add;
$echo_modif = '<img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $texte . '" title="' . $texte . '"/></a>';

// Lien direct sur le dernier évènement saisi et possibilité d'insérer un évènement
if (IS_GRANTED('G')) {
    $MaxRef = 0;

    $sql = 'SELECT Reference, Titre FROM ' . $n_evenements . ' a ' .
        'WHERE a.Reference = ( SELECT max( Reference ) FROM ' . $n_evenements . ')';
    $resmax = lect_sql($sql);
    $enrmax = $resmax->fetch(PDO::FETCH_NUM);
    $MaxRef = $enrmax[0];
    // Lien direct sur le dernièr évènement saisi
    if ($MaxRef > 0) {
        echo my_html(LG_EVENT_LIST_lAST) . LG_SEMIC . '<a href="' . $root . '/fiche_evenement?refPar=' . $MaxRef . '">' .
            my_html($enrmax[1]) . '</a>&nbsp;';
        echo '&nbsp;<a href="' . $root . '/edition_evenement?refPar=' . $MaxRef . '">' . $echo_modif . '<br />' . "\n";
    }
    // Possibilité d'insérer un evenement
    echo my_html($LG_Menu_Title['Event_Add']) . LG_SEMIC . Affiche_Icone_Lien('href="' . $root . '/edition_evenement?refPar=-1"', 'ajouter', $LG_add) . '<br /><br />' . "\n";
}

// Récupération du type sélectionné sur l'affichage précédent
$defaut = '^^^^';
$TypeEv = '';

if ((!$actualites) and (!$professions)) {
    if (isset($_POST['TypeEv']))
        $TypeEv = $_POST['TypeEv'];
    if ($TypeEv == $defaut)
        $TypeEv = '';
    if ($TypeEv == '')
        $TypeEv = Recup_Variable('tev', 'S', '');

    $sql = 'select distinct e.Code_Type, t.Libelle_Type ' .
        'from ' . $n_evenements . ' e , ' . nom_table('types_evenement') . ' t ' .
        'where e.Code_Type = t.Code_Type ' .
        'order by t.Libelle_Type';

    echo '<form action="' . my_self() . '" method="post">' . "\n";
    echo '<table width="50%" align="center">' . "\n";
    echo '<tr align="center" class="rupt_table">';
    echo '<td width="50%">' . my_html(LG_EVENT_LIST_TYPE) . LG_SEMIC . "\n";
    echo '<select name="TypeEv">' . "\n";
    echo '<option value="' . $defaut . '">' . my_html($LG_All) . '</option>' . "\n";
    if ($res = lect_sql($sql)) {
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            echo '<option value="' . $row[0] . '"';
            if ($TypeEv == $row[0]) {
                echo ' selected="selected"';
            }
            echo '>' . my_html($row[1]) . '</option>' . "\n";
        }
    }
    $res->closeCursor();
    echo '</select>' . "\n";
    echo '</td>' . "\n";
    echo '<td width="50%"><input type="submit" value="' . my_html($LG_modify_list) . '"/></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
    echo '</form>' . "\n";
}

if ($TypeEv == 0)
    $TypeEv = '';
$crit_type = '';
if ($TypeEv != '') $crit_type = ' and e.Code_Type = "' . $TypeEv . '"';
if ($actualites) $crit_type = ' and e.Code_Type = "AC3U"';
if ($professions) $crit_type = ' and e.Code_Type = "OCCU"';

// Constitution de la requête d'extraction
$requete = 'SELECT e.Reference, e.Titre, t.Objet_Cible, t.Code_Type, t.Libelle_Type, e.Debut, e.Fin ' .
    'from ' . $n_evenements . ' e , ' . nom_table('types_evenement') . ' t ' .
    'where e.Code_Type = t.Code_Type ' .
    $crit_type .
    ' ORDER BY e.Titre';
$result = lect_sql($requete);

//  Affichage des évènements
if ($result->rowCount() > 0) {

    // Sortie CSV
    if ($CSV) {
        // Crée le fichier et écrit la ligne d'entête
        $nom_fic = $chemin_exports . 'liste_evenements.csv';
        $fp = fopen($nom_fic, 'w+');
        $ligne = '';
        // $ligne .= 'Ref'.';';
        $ligne .= $LG_Event_Title . ';';
        $ligne .= LG_TARGET_OBJECT . ';';
        $ligne .= LG_EVENT_LIST_BEG_PREC . ';';
        $ligne .= $LG_Event_Event_Beg . ';';
        $ligne .= LG_EVENT_LIST_BEG_CAL . ';';
        $ligne .= LG_EVENT_LIST_END_PREC . ';';
        $ligne .= $LG_Event_Event_End . ';';
        $ligne .= LG_EVENT_LIST_END_CAL . ';';
        $ligne .= LG_EVENT_LIST_TYPE . ';';
        $ligne .= LG_EVENT_LIST_TYPE_LABEL . ';';
        fputs($fp, $ligne);
    }

    $premier = 1;
    while ($enreg = $result->fetch(PDO::FETCH_ASSOC)) {
        $ref_evt = $enreg['Reference'];
        if ($CSV) {
            $titre_ev = $enreg['Titre'];
            $titre_ev = str_replace(';', ' ', $titre_ev);
            $ligne = '';
            // $ligne .= $ref_evt.';';
            $ligne .= $titre_ev . ';';
            $ligne .= $enreg['Objet_Cible'] . ';';
            $ligne .= Retourne_Date_CSV($enreg['Debut']) . ';';
            $ligne .= Retourne_Date_CSV($enreg['Fin']) . ';';
            $ligne .= $enreg['Code_Type'] . ';';
            $ligne .= $enreg['Libelle_Type'] . ';';
            fputs($fp, $ligne);
        } else {
            $page = 'fiche_evenement';
            if (($actualites) or ($enreg['Code_Type'] == 'AC3U')) $page = 'fiche_actualite';
            echo '<a href="' . $root . '/' . $page . '?refPar=' . $ref_evt . '">' . $enreg['Titre'] . "</a>\n";
            echo Etend_2_dates($enreg['Debut'], $enreg['Fin'], true) . '&nbsp';

            $cible = $enreg['Objet_Cible'];
            // Si l'on n'affiche pas tous les types d'évènements, on va chercher les informations complémentaires
            if ($TypeEv != '') {
                // L'évènement pointe sur une personne
                if ($cible == 'P') {
                    $req2 = 'SELECT p.Reference, p.Nom, p.Prenoms, p.Diff_Internet  ' .
                        'from ' . $n_personnes . ' p,' . $n_participe . ' q ' .
                        'where p.Reference = q.Personne ' .
                        'and q.Evenement = ' . $ref_evt;
                    $resP = lect_sql($req2);
                    $prems = true;
                    if ($resP->rowCount() > 0) {
                        echo '(';
                        while ($row = $resP->fetch(PDO::FETCH_NUM)) {
                            if (($row[3] == 'O') or IS_GRANTED('P')) {
                                if (!$prems) echo ', ';
                                else $prems = false;
                                echo $row[2] . ' ' . $row[1];
                            } else echo '- ';
                        }
                        echo ')';
                    }
                }

                // L'évènement pointe sur une filiation
                if ($cible == 'F') {
                    // Vu que l'identifiant est celui de la filiation, on peut s'en passer dans la table
                    $req2 = 'SELECT p.Reference, p.Nom, p.Prenoms, p.Diff_Internet  ' .
                        'from ' . $n_personnes . ' p,' . $n_concerne_objet . ' c ' .
                        'where ' . $ref_evt . ' = c.Evenement and c.Type_Objet = \'F\' ' .
                        ' and p.Reference = c.Reference_Objet';
                    $resP = lect_sql($req2);
                    if ($resP->rowCount() > 0) {
                        echo '(';
                        $prems = true;
                        while ($row = $resP->fetch(PDO::FETCH_NUM)) {
                            if (($row[3] == 'O') or IS_GRANTED('P')) {
                                if (!$prems) echo ', ';
                                else $prems = false;
                                echo my_html($row[2] . ' ' . $row[1]);
                            } else echo '- ';
                        }
                        echo ')';
                    }
                }

                // L'évènement pointe sur une union
                if ($cible == 'U') {
                    // Vu que l'identifiant est celui de la filiation, on peut s'en passer dans la table
                    $req2 = 'SELECT m.Reference, m.Nom, m.Prenoms, m.Diff_Internet,  ' .
                        'f.Reference, f.Nom, f.Prenoms, f.Diff_Internet ' .
                        'from ' . $n_personnes . ' m,' . $n_personnes . ' f,' .
                        $n_concerne_objet . ' c, ' .
                        $n_unions . ' u ' .
                        'where ' . $ref_evt . ' = c.Evenement and c.Type_Objet = \'U\' ' .
                        ' and u.Reference = c.Reference_Objet ' .
                        ' and m.Reference = u.Conjoint_1 ' .
                        ' and f.Reference = u.Conjoint_2 ';
                    $resP = lect_sql($req2);
                    if ($resP->rowCount() > 0) {
                        echo '(';
                        while ($row = $resP->fetch(PDO::FETCH_NUM)) {
                            if (($row[3] == 'O') or IS_GRANTED('P')) {
                                echo my_html($row[2] . ' ' . $row[1]) . ' ';
                            } else echo '- ';
                            echo ' x ';
                            if (($row[7] == 'O') or IS_GRANTED('P')) {
                                echo my_html($row[6] . ' ' . $row[5]) . ' ';
                            } else echo '- ';
                        }
                        echo ')';
                    }
                }
            }
            if (IS_GRANTED('G')) {
                $ajout = '';
                if (($actualites) or ($enreg['Code_Type'] == 'AC3U')) $ajout = '&amp;actu=o';
                echo '&nbsp;<a href="' . $root . '/edition_evenement?refPar=' . $ref_evt . $ajout . '">' . $echo_modif . "\n";
            }
            echo '<br />';
        }
    }
    if ($CSV) {
        fclose($fp);
        echo '<br><br>' . $LG_csv_available_in . ' <a href="' . $nom_fic . '" target="_blank">' . $nom_fic . '</a><br />' . "\n";
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