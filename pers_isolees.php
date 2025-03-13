<?php

//=====================================================================
// Liste des personnes isolées non référencées dans les unions, filiations ou liens personnes
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

function affiche($req, $sexe)
{
    global $echo_modif;
    if ($res = lect_sql($req)) {

        $nb_res = $res->rowCount();
        //$plu = pluriel($nb_res);
        echo '<br />' . $nb_res;
        switch ($sexe) {
            case 'm':
                echo my_html(LG_PERS_NO_LK_FOUND_MEN);
                break;
            case 'f':
                echo my_html(LG_PERS_NO_LK_FOUND_WOMEN);
                break;
            case 'x':
                echo my_html(LG_PERS_NO_LK_FOUND_UNDEF);
                break;
        }
        echo '<br />';

        // Affichage
        while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            $idPers = $enreg[0];
            echo '<a ' . Ins_Ref_Pers($idPers) . '>' .
                my_html($enreg[1] . ' ' . $enreg[2]) . '</a>';
            echo '&nbsp;<a ' . Ins_Edt_Pers($idPers) . '>' . $echo_modif;
        }
        $res->closeCursor();
    }
}

$acces = 'L';                          // Type d'accès de la page : (L)ecture
$titre = $LG_Menu_Title['Non_Linked_Pers']; // Titre pour META
$x = Lit_Env();
$niv_requis = 'C';                        // Page acessible au gestionnaire uniquement
require(__DIR__ . '/app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'Pers_Isolees', '');

// $a = microtime_float();

$n_personnes = nom_table('personnes');
$n_filiations = nom_table('filiations');
$n_unions = nom_table('unions');
$n_rel = nom_table('relation_personnes');

// Optimisation : préparation echo des images
$echo_modif = '<img src="' . $root . '/assets/img/' . $Icones['fiche_edition'] . '" alt="' . $LG_modify . '" title="' . $LG_modify . '"></a><br />' . "\n";

// ===== Lecture de la base
/*
$sql = 'SELECT Reference, Nom, Prenoms FROM '.nom_table('personnes')
      . ' where Reference not in (select Enfant from '.nom_table('filiations').')'
      . ' and Reference not in (select Pere from '.nom_table('filiations').')'
      . ' and Reference not in (select Mere from '.nom_table('filiations').')'
      . ' and Reference not in (select Conjoint_1 from '.nom_table('unions').')'
      . ' and Reference not in (select Conjoint_2 from '.nom_table('unions').')'
      . ' and Reference not in (select Personne_1 from '.nom_table('relation_personnes').')'
      . ' and Reference not in (select Personne_2 from '.nom_table('relation_personnes').')'
      . ' and Reference <> 0 '
      . ' ORDER BY Nom , Prenoms';
*/
/*      
$sql = 'SELECT Reference, Nom, Prenoms FROM '.$n_personnes
      . ' where Reference <> 0 and Sexe = \'m\''
      . ' and Reference not in (select Enfant from '.$n_filiations.')'
      . ' and Reference not in (select Pere from '.$n_filiations.')'
      . ' and Reference not in (select Conjoint_1 from '.$n_unions.')'
      . ' and Reference not in (select Personne_1 from '.$n_rel.')'
      . ' and Reference not in (select Personne_2 from '.$n_rel.')'
      . ' and Reference <> 0 '
      . ' union '
      . 'SELECT Reference, Nom, Prenoms FROM '.$n_personnes
      . ' where Reference <> 0 and Sexe = \'f\''
      . ' and Reference not in (select Enfant from '.$n_filiations.')'
      . ' and Reference not in (select Mere from '.$n_filiations.')'
      . ' and Reference not in (select Conjoint_2 from '.$n_unions.')'
      . ' and Reference not in (select Personne_1 from '.$n_rel.')'
      . ' and Reference not in (select Personne_2 from '.$n_rel.')'
      . ' and Reference <> 0 '
      . ' union '
      .'SELECT Reference, Nom, Prenoms FROM '.$n_personnes
      . ' where Reference <> 0 and Sexe is null'
      . ' and Reference not in (select Enfant from '.$n_filiations.')'
      . ' and Reference not in (select Pere from '.$n_filiations.')'
      . ' and Reference not in (select Mere from '.$n_filiations.')'
      . ' and Reference not in (select Conjoint_1 from '.$n_unions.')'
      . ' and Reference not in (select Conjoint_2 from '.$n_unions.')'
      . ' and Reference not in (select Personne_1 from '.$n_rel.')'
      . ' and Reference not in (select Personne_2 from '.$n_rel.')'
      . ' ORDER BY Nom , Prenoms';
*/
$sql1 = 'SELECT Reference, Nom, Prenoms FROM ' . $n_personnes
    . ' where Reference <> 0 and Sexe = \'m\''
    . ' and Reference not in (select Enfant from ' . $n_filiations . ')'
    . ' and Reference not in (select Pere from ' . $n_filiations . ')'
    . ' and Reference not in (select Conjoint_1 from ' . $n_unions . ')'
    . ' and Reference not in (select Personne_1 from ' . $n_rel . ')'
    . ' and Reference not in (select Personne_2 from ' . $n_rel . ')'
    . ' ORDER BY Nom , Prenoms';
$sql2 = 'SELECT Reference, Nom, Prenoms FROM ' . $n_personnes
    . ' where Reference <> 0 and Sexe = \'f\''
    . ' and Reference not in (select Enfant from ' . $n_filiations . ')'
    . ' and Reference not in (select Mere from ' . $n_filiations . ')'
    . ' and Reference not in (select Conjoint_2 from ' . $n_unions . ')'
    . ' and Reference not in (select Personne_1 from ' . $n_rel . ')'
    . ' and Reference not in (select Personne_2 from ' . $n_rel . ')'
    . ' ORDER BY Nom , Prenoms';
$sql3 = 'SELECT Reference, Nom, Prenoms FROM ' . $n_personnes
    . ' where Reference <> 0 and Sexe is null'
    . ' and Reference not in (select Enfant from ' . $n_filiations . ')'
    . ' and Reference not in (select Pere from ' . $n_filiations . ')'
    . ' and Reference not in (select Mere from ' . $n_filiations . ')'
    . ' and Reference not in (select Conjoint_1 from ' . $n_unions . ')'
    . ' and Reference not in (select Conjoint_2 from ' . $n_unions . ')'
    . ' and Reference not in (select Personne_1 from ' . $n_rel . ')'
    . ' and Reference not in (select Personne_2 from ' . $n_rel . ')'
    . ' ORDER BY Nom , Prenoms';

// En local, on fait sauter les limites      
if ($Environnement == 'L') set_time_limit(0);

affiche($sql1, 'm');
affiche($sql2, 'f');
affiche($sql3, 'x');

/*
$b = microtime_float();
echo 'Début : '.$a.'<br />';
echo 'Fin : '.$b.'<br />';
$c = $b - $a;
echo 'Diff : '.$c.'<br />';
*/

Insere_Bas($compl);

?>
</body>

</html>