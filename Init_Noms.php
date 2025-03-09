<?php
//====================================================================
// Ré-initialisation des noms, au cas où certains imports fonctionnent mal...
// L'identifiant est alors à null
//=====================================================================

// Gestion standard des pages
session_start();
include('fonctions.php');
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Init_Names'];
$x = Lit_Env();
$niv_requis = 'G';                       // Page réservée au profil gestionnaire
include('Gestion_Pages.php');

$n_personnes     = nom_table('personnes');
$n_noms          = nom_table('noms_famille');
$n_liens_noms    = nom_table('noms_personnes');

// Création des noms et liaison avec les personnes
function Creation_Noms()
{
    global $idNom, $Init, $existe_null,
        $n_personnes, $n_noms, $n_liens_noms;

    //    Appel du fichier contenant la classe
    include 'phonetique.php';
    //    Initialisation d'un objet de la classe
    $codePho = new phonetique();

    $Anom = '';
    $idNom = Nouvel_Identifiant('idNomFam', 'noms_famille') - 1;
    //echo 'idNom : '.$idNom.'<br>';

    //$req ='';
    if (isset($req)) unset($req);
    $msg = '';
    $existe_null = false;
    $deb_ins_noms = 'insert into ' . $n_noms . ' values(';
    $deb_upd_pers = 'update ' . $n_personnes . ' set idNomFam=';
    $deb_ins_lien = 'insert into ' . $n_liens_noms . ' values(';
    $sql = 'SELECT UPPER(Nom), Reference FROM ' . $n_personnes . ' where Reference <> 0 and idNomFam is null order by UPPER(Nom)';
    if ($res = lect_sql($sql)) {
        $lib_ref = ', ' . my_html(LG_INIT_NAMES_REF) . ' : ';
        while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            $existe_null = true;
            $nom = $enreg[0];
            $refPers = $enreg[1];
            echo 'Nom : ' . $nom . $lib_ref . $refPers . '<br />';
            // Traitements en rupture sur le nom
            if (($nom != $Anom) and ($nom != '')) {
                $Anom = $nom;

                echo $nom . '<br />';

                //    Calcul d'un code phonétique
                $code = $codePho->calculer($nom);
                $idNom++;
                $ident_nom = $idNom;
                // Création de l'enregistrement dans la table des noms de famille
                $req[] = $deb_ins_noms . $ident_nom . ',\'' . addslashes($nom) . '\',\'' . $code . '\')';
            }
            // Modification de la table des personnes
            $req[] = $deb_upd_pers . $ident_nom . ' where Reference=' . $refPers;
            // Création de l'enregistrement dans la table des liens personnes / noms
            $req[] = $deb_ins_lien . $refPers . ',' . $ident_nom . ',\'O\',null)';
        }
        $res->closeCursor();
        $c_req = 0;
        if (isset($req)) {
            $c_req = count($req);
            //echo 'nb de requêtes : '.$c_req.'<br />';
        }
        for ($nb = 0; $nb < $c_req; $nb++) {
            //echo 'Req : '.$req[$nb].'<br>';
            $res = lect_sql($req[$nb]);
        }
        $req = '';
    }
}

$compl = Ajoute_Page_Info(600, 300);
Insere_Haut($titre, $compl, 'Init_Noms', '');

$INI = Recup_Variable('ini', 'C', 'no');    // Demande d'initialisation oui /non
// Demande de ré-initialisation des noms
if (isset($INI) and ($INI === 'o')) {
    echo my_html(LG_INIT_NAMES_INIT) . '<br />';
    $res = maj_sql('delete from ' . $n_noms);
    echo $n_noms . ' : ' . $enr_mod . '<br />';
    $res = maj_sql('delete from ' . $n_liens_noms);
    echo $n_liens_noms . ' : ' . $enr_mod . '<br />';
    $res = maj_sql('update ' . $n_personnes . ' set idNomFam = null');
    echo $n_personnes . ' : ' . $enr_mod . '<br />';
}
// Création des noms de famille
Creation_Noms();

$msg = LG_INIT_NAMES_NONE;
if ($existe_null) $msg = LG_INIT_NAMES_DONE;

echo '<br />' . my_html($msg) . '<br />';

Insere_Bas($compl);
?>
</body>

</html>