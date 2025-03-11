<?php

//=====================================================================
// Lien d'un évènement à un objet (union ou filiation)
// Parametres a renseigner :
// - refEvt : évènement (mettre -1 pour creer un lien depuis 1 fiche filiation ou union) TODO: lol
// - refObjet : objet concerné (mettre -1 pour creer un lien depuis 1 fiche évènement) TODO: lol
// - TypeObjet : type de l'objet concerné
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'Horigine',
    'supprimer',
    'annuler',
    'refEvtF',
    'refObjF',
    'typeObjetF'
);
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = LG_LINK_EVT_TITLE;      // Titre pour META
$x = Lit_Env();
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

$refEvtF    = Secur_Variable_Post($refEvtF, 1, 'N');
$refObjF    = Secur_Variable_Post($refObjF, 1, 'N');
$typeObjetF = Secur_Variable_Post($typeObjetF, 1, 'S');

// Recup des variables passées dans l'URL
$refEvt = Recup_Variable('refEvt', 'N');                  // - refEvt : évènement (-1 en création de la fiche)
$refObjet = Recup_Variable('refObjet', 'N');              // - refObjet : objet concerné
$TypeObjet = Recup_Variable('TypeObjet', 'C', 'UF');       // - typeObjet : type de l'objet concerné

$lib = lib_pfu($TypeObjet);

$entete = $titre;
//$entete = 'Lien évènement - '.$lib;

$message = '';

$n_concerne_objet = nom_table('concerne_objet');

//  Suppression du lien
if ($bt_Sup) {
    $req  = 'delete from ' . $n_concerne_objet .
        ' where Evenement = ' . $refEvtF .
        ' and Reference_Objet = ' . $refObjF .
        ' and Type_Objet = "' . $typeObjetF . '"';
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

//  Création (pas de modification possible)
if ($bt_OK) {
    if ($refEvtF == '') $refEvtF = -1;
    if ($refObjF == '') $refObjF = -1;
    if ((($refEvt == -1) or ($refObjet == -1)) and
        (($refEvtF != -1) and ($refObjF != -1))
    ) {
        Ins_Zone_Req($refEvtF, 'N', $req);
        Ins_Zone_Req($refObjF, 'N', $req);
        Ins_Zone_Req($TypeObjet, 'A', $req);
        if ($req != '') {
            $req  = 'insert ' . $n_concerne_objet . ' values (' . $req . ')';
            $res = maj_sql($req);
            maj_date_site();
        }
    }
    Retour_Ar();
}

//
//  ========== Programme principal ==========

//  Affichage pour saisie
if ((!$bt_OK) && (!$bt_An) && (!$bt_Sup)) {

    $compl = Ajoute_Page_Info(600, 250);
    Insere_Haut($entete, $compl, 'Edition_Lier_Objet', '');

    //  Debut de la page
    echo '<form id="saisie" method="post" action="' . my_self() . '?refEvt=' . $refEvt .
        '&amp;refObjet=' . $refObjet .
        '&amp;TypeObjet=' . $TypeObjet . '">' . "\n";
    aff_origine();
    echo '<input type="' . $hidden . '" name="typeObjetF" value="' . $TypeObjet . '"/>' . "\n";
    echo "<br />\n";

    // Récupération du libellé à afficher pour l'objet ==> pas de modification possible
    if ($refObjet != -1) {
        echo '<input type="' . $hidden . '" name="refObjF" value="' . $refObjet . '"/>' . "\n";
        // Lien avec une filiation, on va afficher les prénoms et le nom de la personne
        if ($TypeObjet == 'F') {
            $x = Get_Nom_Prenoms($refObjet, $Nom, $Prenoms);
            $lib_obj = ' filiation de ' . $Prenoms . ' ' . $Nom;
            // Libellé spécifique pour personne connue et évènement inconnu
            if ($refEvt == -1) {
                if ($x) echo my_html(LG_LINK_TO_PARENTS) . $lib_obj . "<br />\n";
            }
        }
        // Lien avec une union, on va afficher les prénoms et le nom des 2 personnes
        if ($TypeObjet == 'U') {
            // Récupération des références des conjoints
            $req = 'select Conjoint_1, Conjoint_2 from ' . nom_table('unions') .
                ' where Reference = ' . $refObjet . ' limit 1';
            //$result = send_sql($db , $req);
            if ($res = lect_sql($req)) {
                if ($enreg = $res->fetch(PDO::FETCH_NUM)) {
                    $c1 = $enreg[0];
                    $c2 = $enreg[1];
                    $x1 = Get_Nom_Prenoms($c1, $Nom1, $Prenoms1);
                    $x2 = Get_Nom_Prenoms($c2, $Nom2, $Prenoms2);
                    $lib_obj = ' de ' . $Prenoms1 . ' ' . $Nom1 . ' et de ' . $Prenoms2 . ' ' . $Nom2;
                    // Libellé spécifique pour personne connue et évènement inconnu
                    if ($refEvt == -1) {
                        if ($x) echo my_html(LG_LINK_TO_UNION) . $lib_obj . "<br />\n";
                    }
                }
            } else echo my_html(LG_LINK_UNION_NF) . "<br />\n";
        }
    }

    // Si l'évènement est connu, on ne pourra pas le modifier
    if ($refEvt != -1) {
        echo '<input type="' . $hidden . '" name="refEvtF" value="' . $refEvt . '"/>' . "\n";
        // Affichage du libellé : lier tel type d'objet avec tel évènement
        $lib_evt = libelle_lien_evt($refEvt, $TypeObjet, $refObjet);
    }

    // L'objet et l'évènement sont connus ==> on est en modification
    if (($refObjet != -1) and ($refEvt != -1)) {
        echo my_html(LG_LINK_WITH_1) . ' "' . $lib_evt . '" ' . my_html(LG_LINK_WITH_2) . ' "' . $lib_obj . '"<br />' . "\n";
    }

    // L'évènement n'est pas modifiable si connu
    if (($refEvt != -1) and ($refObjet == -1)) {
        // Affichage des objets possibles
        if ($refObjet == -1) {
            echo '<br />' . ucfirst($lib) . '&nbsp;';
            // Filiations
            if ($TypeObjet == 'F') {
                // Affichage d'un select avec la liste des personnes présentes dans des filiations
                aff_liste_pers(
                    'refObjF',                      // Nom du select
                    1,                              // 1ère fois
                    1,                              // dernière fois
                    -1,                             // critère de sélection
                    ' Reference in (select Enfant from ' . nom_table('filiations') . ')' .
                        ' and Reference not in (select Reference_Objet from ' . $n_concerne_objet .
                        ' where Evenement = ' . $refEvt . ' and Type_Objet = \'F\')', // crtitère de  sélection
                    'Nom, Prenoms',                 // critère de tri de la liste
                    1
                );                             // zone obligatoire
            }
            // Unions
            if ($TypeObjet == 'U') {
                // Affichage d'un select avec la liste des personnes présentes dans des unions
                echo '<select name="refObjF"' . $style_z_oblig . '>' . "\n";
                $req = 'select u.Reference, m.Nom as NomM, m.Prenoms as PrenomsM, f.Nom as NomF, f.Prenoms as PrenomsF' .
                    ' from ' . nom_table('personnes') . ' m, ' .
                    nom_table('personnes') . ' f, ' . nom_table('unions') . ' u ' .
                    ' where m.reference = u.Conjoint_1 ' .
                    ' and f.reference = u.Conjoint_2 ' .
                    ' and u.Reference not in ' .
                    ' (select Reference_Objet from ' . $n_concerne_objet .
                    ' where Reference_Objet = 2 and Type_objet = \'U\') ' .
                    ' order by NomM, PrenomsM';
                $res = lect_sql($req);
                while ($row = $res->fetch(PDO::FETCH_NUM)) {
                    echo '<option value="' . $row['0'] . '">' .
                        my_html($row[2] . ' ' . $row[1] . ' x ' . $row[4] . ' ' . $row[3]) .
                        '</option>' . "\n";
                }
                echo '</select>&nbsp;';
                $res->closeCursor();
            }
        }
    }
    // L'objet lié n'est pas modifiable si connu
    if (($refObjet != -1) and ($refEvt == -1)) {
        // Afficher les évènements possibles en fonction du type d'objet
        // Donc les évènements non déjà reliés à l'objet
        $req = 'select ev.Reference, ev.Code_Type, ev.Titre, ev.Debut, ev.Fin ' .
            'from ' . nom_table('evenements') . ' as ev, ' . nom_table('types_evenement') . ' as ty ' .
            ' where ty.Objet_Cible = "' . $TypeObjet . '" ' .
            ' and ty.Code_Type = ev.Code_Type ' .
            ' and ev.Reference not in (' .
            'select Evenement from ' . $n_concerne_objet .
            ' where ty.Objet_Cible = "' . $TypeObjet . '" ' .
            ' and Reference_Objet = ' . $refObjet . ')' .
            ' order by Code_Type, Titre, Debut, Fin';
        select_liste_evenements('refEvtF', $req);
    }

    //  Bas de page
    echo "<br />\n";
    $lib_sup = '';
    if (($refEvt != -1) and ($refObjet != -1)) {
        $lib_sup = $lib_Supprimer;
    }
    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, LG_LINK_THIS, false);

    echo "</form>\n";
    Insere_Bas($compl);
}
?>
</body>

</html>