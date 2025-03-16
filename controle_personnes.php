<?php

//=====================================================================
// Contrôle des personnes
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');

$acces = 'L';                          // Type d'accès de la page : (L)ecture
$titre = $LG_Menu_Title['Check_Persons']; // Titre pour META

$tab_variables = array('annuler', 'Horigine', 'Ignore');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');
$Ignore = Secur_Variable_Post($Ignore, 1, 'S');

if ($Ignore == 'I') $Ignore = true;
else $Ignore = false;

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$x = Lit_Env();
$niv_requis = 'C';                // Page accessible à partir du niveau contributeur
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Verrouillage sur les gratuits non Premium
if (($SiteGratuit) and (!$Premium)) Retour_Ar();

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup des variables passées dans l'URL :

$texte = Dem_Texte();                // texte ou non
$CT = Recup_Variable('CT', 'C', 'O');    // Demande de contrôle
$al = Recup_Variable('al', 'S');        // Niveau des alertes demandées pour le texte

$controle = false;
if ($CT === 'O') $controle = true;

$dem_alerte_0 = false;
$dem_alerte_1 = false;
$dem_alerte_2 = false;
if (isset($_POST['ch_alerte_0'])) $dem_alerte_0 = true;
if (isset($_POST['ch_alerte_1'])) $dem_alerte_1 = true;
if (isset($_POST['ch_alerte_2'])) $dem_alerte_2 = true;

if ($texte) {
    $dem_alerte_0 = false;
    $dem_alerte_1 = false;
    $dem_alerte_2 = false;
    if ($al[0] == 'O') $dem_alerte_0 = true;
    if ($al[1] == 'O') $dem_alerte_1 = true;
    if ($al[2] == 'O') $dem_alerte_2 = true;
}

$al_texte = '';
if ($dem_alerte_0) $al_texte .= 'O';
else $al_texte .= 'N';
if ($dem_alerte_1) $al_texte .= 'O';
else $al_texte .= 'N';
if ($dem_alerte_2) $al_texte .= 'O';
else $al_texte .= 'N';



$lien = 'href="' . $root . '/controle_personnes.php?texte=O' .
    '&amp;al=' . $al_texte .
    '&CT=O';

$compl = Ajoute_Page_Info(700, 250) .
    Affiche_Icone_Lien($lien . '"', 'text', $LG_printable_format) . ' '
    //.Affiche_Icone_Lien('href="'. $root .'/controle_personnes.php?idNom='.$idNom.'&amp;Nom='.$NomL.'&amp;Sortie=c"','exp_tab','Export CSV').' '
;


if (! $texte) {
    Insere_Haut(my_html($titre), $compl, 'Controle_Personnes', '');
} else {
    echo '</head>' . "\n";
    echo '<body vlink="#0000ff" link="#0000ff">' . "\n";
    echo '<table cellpadding="0" width="100%">' . "\n";
    echo '<tr>' . "\n";
    echo '<td align="center"><b>' . StripSlashes($titre) . '</b></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
    echo '<br>';
    $deja = Recup_Variable('deja', 'S');
}

$n_personnes = nom_table('personnes');
$n_filiations = nom_table('filiations');
$n_unions = nom_table('unions');

$num_lig = 0;

/*
if (!$Sortie) $Sortie = 'e';
$CSV = ($Sortie == 'c') ? true : false;
*/

$niv_alerte_0 = 'OK';
$niv_alerte_1 = 'Alerte';
$niv_alerte_2 = 'Erreur';

if (!$controle) {
    $dem_alerte_0 = true;
    $dem_alerte_1 = true;
    $dem_alerte_2 = true;
}

$img_vert =  '<img src="' . $root . '/assets/img/' . $Icones['drapeau_vert'] . '" alt="OK" title="OK">';
$img_orange =  '<img src="' . $root . '/assets/img/' . $Icones['drapeau_orange'] . '" alt="Alerte" title="Alerte">';
$img_rouge =  '<img src="' . $root . '/assets/img/' . $Icones['drapeau_rouge'] . '" alt="Erreur" title="Erreur">';

if (! $texte) {
    echo '<form action="' . my_self() . '?CT=O" method="post">' . "\n";
    echo '<input type="hidden" name="deja" value = "O"/>';
    echo '<table border="0" width="60%" align="center">' . "\n";
    echo '<tr align="center" class="rupt_table">';
    echo '<td>Afficher : ' . "\n";
    aff_cbox('ch_alerte_0', $niv_alerte_0, $img_vert,   $dem_alerte_0);
    aff_cbox('ch_alerte_1', $niv_alerte_1, $img_orange, $dem_alerte_1);
    aff_cbox('ch_alerte_2', $niv_alerte_2, $img_rouge,  $dem_alerte_2);
    echo '</td>' . "\n";
    echo '<td><input type="checkbox" name="Ignore" value="I"';
    if ($Ignore) echo ' checked="checked"';
    echo '/> Ignorer les personnes validées</td>' . "\n";
    echo '<td><input type="submit" value="Afficher la liste"/></td>' . "\n";
    echo '</tr>' . "\n";
    echo '</table>' . "\n";
    echo '</form>' . "\n";
} else {
    echo 'Affichage demandé :';
    echo ' ' . $niv_alerte_0;
    O_N($dem_alerte_0);
    echo ' ; ' . $niv_alerte_1;
    O_N($dem_alerte_1);
    echo ' ; ' . $niv_alerte_2;
    O_N($dem_alerte_2);
    echo '<br><br>';
}

echo '<div id="liste">';
echo 'Liste des contr&ocirc;les' . ' : ';
echo '<ul class="puces">';
echo '<li>' . 'Pour la personne';
echo '<ul class="puces">';
echo '<li>' . 'que le sexe soit connu' . '</li>';
echo '<li>' . 'que la fiche soit validée' . '</li>';
echo '<li>' . 'que les dates de naissance et de déc&egrave;s (dans le cas des personnes non vivantes) soient présentes et qu\'elles correspondent &agrave; un jour précis (le ...)' . '</li>';
echo '<li>' . 'que la date de naissance préc&egrave;de ou soit égale &agrave; la date de déc&egrave;s' . '</li>';
echo '</ul></li>';
echo '<li>' . 'Avec ses parents';
echo '<ul class="puces">';
echo '<li>' . 'que la personne soit née apr&egrave;s que le p&egrave;re et la m&egrave;re aient 15 ans' . '</li>';
echo '<li>' . 'que la personne soit née au plus tard 9 mois apr&egrave;s le déc&egrave;s du p&egrave;re' . '</li>';
echo '<li>' . 'que la personne soit née apr&egrave;s le déc&egrave;s de la m&egrave;re' . '</li>';
echo '</ul></li></ul>';
echo '</div>';

if ($controle) {

    // Constitution de la requête d'extraction
    $crit_ignore = '';
    if ($Ignore)
        $crit_ignore = ' and p.Statut_Fiche != "O"';
    $sql = 'SELECT '
        . 'p.Reference as Ref0, p.Nom as Nom0, p.Prenoms as Prenoms0, p.Ne_le as NeL0, p.Decede_Le as DecL0, p.Statut_Fiche, p.Sexe, '
        . 'p1.Reference as Ref1, p1.Nom as Nom1, p1.Prenoms as Prenoms1, p1.Ne_le as NeL1, p1.Decede_Le as DecL1, '
        . 'p2.Reference as Ref2, p2.Nom as Nom2, p2.Prenoms as Prenoms2, p2.Ne_le as NeL2, p2.Decede_Le as DecL2'
        . ' FROM ' . $n_personnes . ' p'
        . ' LEFT JOIN ' . $n_filiations . ' f ON p.Reference = f.Enfant'
        . ' LEFT JOIN ' . $n_personnes . ' p1 ON p1.Reference = f.Pere'
        . ' LEFT JOIN ' . $n_personnes . ' p2 ON p2.Reference = f.Mere'
        . ' WHERE p.Reference <> 0'
        . $crit_ignore
        . ' order by p.Nom, p.Prenoms, p.Reference';
    $res = lect_sql($sql);

    // Balayage
    if ($res->RowCount() > 0) {

        while ($enr = $res->fetch(PDO::FETCH_ASSOC)) {

            // Niveau 0 : OK
            // Niveau 1 : alerte 
            // Niveau 2 : erreur
            $alerte_controle = 0;

            $msg_ctrl = '';

            $sexe = $enr['Sexe'];
            if (($sexe != 'm') and ($sexe != 'f')) al_controle(1, 'le sexe de la personne n\'est pas connu');

            // Fiche de la personne validée ?
            if ($enr['Statut_Fiche'] != 0) al_controle(1, 'statut non validé');

            // Dates de naissance et de décès précises et présentes
            $dateN = $enr['NeL0'];
            $dateD = $enr['DecL0'];
            $vivant = determine_etat_vivant($dateN, $dateD);
            if ($vivant) $dateD = '99991231GL';
            $lgDateN = strlen($dateN);
            $lgDateD = strlen($dateD);
            // Contrôle de précision de la date de naissance
            if ($lgDateN == 10) {
                if ($dateN[9] != 'L') al_controle(1, 'date de naissance non précise');
            } else {
                // date renseignée et longueur non attendue, aïe...
                if ($lgDateN > 0) al_controle(2, 'format date de naissance erroné');
                else al_controle(1, 'date de naissance non connue');
            }
            // Contrôle de précision de la date de décès
            if (!$vivant) {
                if ($lgDateD == 10) {
                    if ($dateD[9] != 'L') al_controle(1, 'date de déc&egrave;s non précise');
                } else {
                    // date renseignée et longueur non attendue, aïe...
                    if ($lgDateD > 0) al_controle(2, 'format date de déc&egrave;s erroné');
                    else al_controle(1, 'date de déc&egrave;s non connue');
                }
            }
            // Contrôle cohérence date naissance / date décès
            if (($lgDateN == 10) and ($lgDateD == 10)) {
                if (($dateN[9] == 'L') and ($dateD[9] == 'L')) {
                    if ($dateD < $dateN) al_controle(2, 'date de déc&egrave;s inférieure &agrave; la date de naissance');
                }
            }

            $dateNP = $enr['NeL1'];
            $dateDP = $enr['DecL1'];
            $dateNM = $enr['NeL2'];
            $dateDM = $enr['DecL2'];
            // Pour PHP 8.1 et supra
            if (is_null($dateNP))
                $dateNP = '';
            if (is_null($dateDP))
                $dateDP = '';
            if (is_null($dateNM))
                $dateNM = '';
            if (is_null($dateDM))
                $dateDM = '';
            if (($lgDateN == 10) and ($dateN[9] == 'L')) {
                // Contrôle que la personne soit née après que le père et la mère aient 15 ans
                // Pour le père
                if ((strlen($dateNP) == 10) and ($dateNP[9] == 'L')) {
                    $ageP = Age_Mois($dateNP, $dateN) / 12;
                    if ($ageP < 15) al_controle(1, 'p&egrave;re de moins de 15 ans');
                } else al_controle(1, 'contr&ocirc;le impossible par rapport &agrave; l\'&acirc;ge du p&egrave;re');
                // Pour la mère
                if ((strlen($dateNM) == 10) and ($dateNM[9] == 'L')) {
                    $ageM = Age_Mois($dateNM, $dateN) / 12;
                    if ($ageM < 15) al_controle(1, 'm&egrave;re de moins de 15 ans');
                } else al_controle(1, 'contr&ocirc;le impossible par rapport &agrave; l\'&acirc;ge de la m&egrave;re');
            }
            // Contrôle que la personne soit née au plus tard 9 mois après le décès du père
            if (($lgDateN == 10) and ($dateN[9] == 'L')) {
                if ((strlen($dateDP) == 10) and ($dateDP[9] == 'L')) {
                    $ageP = Age_Mois($dateN, $dateDP);
                    if ($ageP < 9) al_controle(1, 'p&egrave;re décédé moins de 9 mois avant la naissance');
                }
            }
            // Contrôle que la personne soit née avant (<=) le décès de la mère
            if (($lgDateN == 10) and ($dateN[9] == 'L')) {
                if ((strlen($dateDM) == 10) and ($dateDM[9] == 'L')) {
                    if ($dateN > $dateDM) al_controle(1, 'm&egrave;re décédée avant la naissance');
                }
            }

            $Ref = $enr['Ref0'];

            $ligne_P = '';
            if (!$texte) {
                $ligne_P =  '<a href="' . $root . '/fiche_fam_pers.php?Refer=' . $Ref . '" target="_blank">' . my_html($enr['Nom0'] . ' ' . $enr['Prenoms0']) . '</a>'
                    . ' <a href="' . $root . '/edition_personne.php?Refer=' . $Ref . '" target="_blank"><img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . my_html($LG_modify) . '" title="' . my_html($LG_modify) . '"></a>'
                    . ' <a href="' . $root . '/verif_personne.php?Refer=' . $Ref . '" target="_blank"><img src="' . $root . '/assets/img/' . $Icones['fiche_controle'] . '" alt="' . my_html($LG_LPers_Check_Pers) . '" title="' . my_html($LG_LPers_Check_Pers) . '"></a>';
            } else {
                $ligne_P = my_html($enr['Nom0'] . ' ' . $enr['Prenoms0']) . ' ';
            }

            //echo '$dem_alerte_0 : '.$dem_alerte_0.'/'.$alerte_controle.'<br>';

            switch ($alerte_controle) {
                case 0:
                    if ($dem_alerte_0) echo $img_vert . ' ' . $ligne_P . '<br>';
                    break;
                case 1:
                    if ($dem_alerte_1) echo $img_orange . ' ' . $ligne_P . '<br>  ' . $msg_ctrl . '<br>';
                    break;
                case 2:
                    if ($dem_alerte_2) echo $img_rouge . ' ' . $ligne_P . '<br>  ' . $msg_ctrl . '<br>';
                    break;
            }
        }
    }

    echo '<br><img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Information" title="Information"> Légende : ';
    echo $img_vert . ' : ' . $niv_alerte_0 . ' ; ' . $img_orange . ' : ' . $niv_alerte_1 . ' ; ' . $img_rouge . ' : ' . $niv_alerte_2 . "\n";
}

if (! $texte) {

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
}

function al_controle($niveau, $ctrl)
{
    global $alerte_controle, $msg_ctrl;
    $alerte_controle = $niveau;
    if ($msg_ctrl != '') $msg_ctrl .= ', ';
    $msg_ctrl .= $ctrl;
}

function O_N($demande)
{
    if ($demande) echo ' : oui';
    else echo ' : non';
}

function aff_cbox($id_n, $lib, $img, $chk)
{
    echo '<input type="checkbox"';
    if ($chk) echo ' checked="checked"';
    echo ' name="' . $id_n . '" id="' . $id_n . '" value="1"/><label for="' . $id_n . '">' . $lib . '</label> ' . $img . '  ' . "\n";
}
?>
</body>

</html>