<?php

//=====================================================================
// Exportation au format Gedcom
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

require(__DIR__ . '/Commun_Gedcom.php');
$acces = 'L';                        // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Exp_Ged'];            // Titre pour META
$niv_requis = 'G';                    // Disponible pour le gestionnaire
$x = Lit_Env();
require(__DIR__ . '/Gestion_Pages.php');

// Recup de la variable passée dans l'URL : export léger O/N
$leger = Recup_Variable('leger', 'C', 'no');
if ($leger === 'o') $leger = true;
else $leger = false;

if ($leger) $titre = $LG_Menu_Title['Exp_Ged_Light'];


// Pas de limite de temps en local
// Sur le net, limite fixée à la valeur paramétrée ; plus importante sur les sites Premium
if ($Environnement == 'L') set_time_limit(0);
if ($SiteGratuit) set_time_limit($lim_temps);

// Optimisation : noms de tables
$n_filiations       = nom_table('filiations');
$n_unions           = nom_table('unions');
$n_concerne_objet   = nom_table('concerne_objet');
$n_evenements       = nom_table('evenements');
$n_types_evenement  = nom_table('types_evenement');


$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'exp_Gedcom', '');

$nom_fic_exp = construit_fic($chemin_Gedcom, 'Export_gedcom#', 'ged');
$n_fic = basename($nom_fic_exp);

// if ($fp=fopen($nom_fic_exp,'w+')) {
if ($fp = ouvre_fic($nom_fic_exp, 'w')) {

    // Données d'entête
    Entete_Gedcom($fp, $n_fic);

    // Balayage de la liste des personnes
    $sql = Debut_Ext_Pers_Ged() . ' reference <> 0;';

    $nPers = 0;

    if ($res = lect_sql($sql)) {
        echo $res->rowCount() . ' ' . my_html(LG_GEDCOM_PERS) . '<br>';

        // Recherche de l'enregistrement concernant la France pour optimisation
        get_Lib_FR();

        $sql_FR = 'select Identifiant_zone, Nom_Pays from ' . nom_table('pays') . ' where Code_Pays_ISO_Alpha3 = "FRA" limit 1';
        if ($res_FR = lect_sql($sql_FR)) {
            if ($enr = $res_FR->fetch(PDO::FETCH_NUM)) {
                $ref_FR = $enr[0];
                $lib_FR = $enr[1];
            }
            $res_FR->closeCursor();
        }
        if ($debug) echo 'Libellé FRA = ' . $lib_FR . '<br>';
        while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            // Données sur la personne
            Personne_Gedcom($fp, $enreg, $leger);
            // Ecriture des couples avec conjoints
            $Refer = $enreg[0];
            $sql = 'select Conjoint_1, Conjoint_2 from ' . $n_unions . ' where ';
            switch ($enreg[3]) {
                case 'm':
                    $sql = $sql . 'Conjoint_1 = ' . $Refer;
                    break;
                case 'f':
                    $sql = $sql . 'Conjoint_2 = ' . $Refer;
                    break;
                default:
                    $sql = $sql . 'Conjoint_1 = ' . $Refer . ' or Conjoint_2 = ' . $Refer;
                    break;
            }
            $sql .= ' limit 1';
            if ($resUn = lect_sql($sql)) {
                while ($enregUn = $resUn->fetch(PDO::FETCH_NUM)) {
                    fwrite($fp, '1 FAMS @F' . $enregUn[0] . '_' . $enregUn[1] . '@' . $cr);
                }
            }

            // Ecriture de la ligne parents
            if (Get_Parents($Refer, $Pere, $Mere, $Rang)) {
                $Fam_Par = $Pere . '_' . $Mere;
                fwrite($fp, '1 FAMC @F' . $Fam_Par . '@' . $cr);
            }
            ++$nPers;
            if (($nPers % 100) == 0) echo '&nbsp;&nbsp;&nbsp;' . $nPers . ' ' . my_html(LG_GEDCOM_PERS_PROCESS) . '<br>';
        }
    }
    echo '&nbsp;&nbsp;&nbsp;' . $nPers . ' ' . my_html(LG_GEDCOM_PERS_PROCESS) . '<br>';
    $res->closeCursor();

    // Balayage de la liste des unions
    $sql = 'select Conjoint_1, Conjoint_2, Maries_Le, Ville_Mariage, Reference, Date_K, Ville_Notaire ' .
        'from ' . $n_unions . ' order by Ville_Mariage;';
    $nUnions = 0;
    $memoVille = '';

    if ($res = lect_sql($sql)) {
        echo $res->rowCount() . ' ' . my_html(LG_GEDCOM_UNIONS) . '<br>';
        while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            $Pere = $enreg[0];
            $Mere = $enreg[1];
            $Ref_Union = $enreg[4];
            fwrite($fp, '0 @F' . $Pere . '_' . $Mere . '@ FAM' . $cr);
            fwrite($fp, '1 HUSB @I' . $Pere . '@' . $cr);
            fwrite($fp, '1 WIFE @I' . $Mere . '@' . $cr);
            if (($enreg[2] != '') or ($enreg[3] != 0)) fwrite($fp, '1 MARR' . $cr);
            $LaDate = Etend_date_GedCom($enreg[2]);
            if ($LaDate != '') fwrite($fp, '2 DATE ' . $LaDate . $cr);
            $LaVille = $enreg[3];
            if ($LaVille != 0) {
                if ($LaVille != $memoVille) {
                    $Lib_Ville = lib_villeG($LaVille);
                    $memoVille = $LaVille;
                }
                if ($Lib_Ville != '') fwrite($fp, '2 PLAC ' . $Lib_Ville . $cr);
            }

            // Traitement du contrat ; balise MARC dans le Gedcom
            $Date_K = $enreg[5];
            $Ville_K = $enreg[6];
            if (($Date_K != '') or ($Ville_K != 0)) {
                fwrite($fp, '1 MARC' . $cr);
                $LaDate = Etend_date_GedCom($Date_K);
                if ($LaDate != '') fwrite($fp, '2 DATE ' . $LaDate . $cr);
                if ($Ville_K != 0) {
                    if ($Ville_K != $memoVille) {
                        $Lib_Ville = lib_villeG($Ville_K);
                        $memoVille = $Ville_K;
                    }
                    if ($Lib_Ville != '') fwrite($fp, '2 PLAC ' . $Lib_Ville . $cr);
                }
            }

            // Notes pour l'union
            if (!$leger) {
                if (Rech_Commentaire($Ref_Union, 'U')) {
                    if ($est_gestionnaire or $Diffusion_Commentaire_Internet == 'O')
                        Ecrit_Note_Gedcom($Commentaire, $fp);
                }
            }


            /////////////////////////
            // Evènements liés à l'union
            $req = 'select Identifiant_zone, Identifiant_Niveau, Code_Type, Debut, Fin, Titre, Reference from ' . $n_evenements .
                ' where Code_Type in (SELECT Code_Type from ' . $n_types_evenement .
                ' where Type_Gedcom = "O" and Objet_Cible = "U")' .
                ' and Reference in (select Evenement from ' . $n_concerne_objet . ' where Reference_Objet = ' . $Ref_Union . ' and Type_Objet = "U")';
            if ($resEv = lect_sql($req)) {
                while ($enrEv = $resEv->fetch(PDO::FETCH_NUM)) {
                    fwrite($fp, '1 ' . $enrEv[2] . ' ' . $enrEv[5] . $cr);
                    // Date de l'évènement : début ou plage de dates
                    $deb_lu = $enrEv[3];
                    $fin_lu = $enrEv[4];
                    if (($deb_lu != '') or ($fin_lu != '')) {
                        $debut = '';
                        $fin = '';
                        // Si date de fin = date de début, on ne fait pas de between, sinon lors de l'import on obtient after
                        if ($fin_lu == $deb_lu) $fin_lu = '';
                        if ($deb_lu != '') $debut = Etend_date_GedCom($deb_lu);
                        if ($fin_lu != '') $fin = Etend_date_GedCom($fin_lu);
                        if (($deb_lu != '') and ($fin_lu == '')) fwrite($fp, '2 DATE ' . $debut . $cr);
                        if (($deb_lu != '') and ($fin_lu != '')) fwrite($fp, '2 DATE BET ' . $debut . ' AND ' . $fin . $cr);
                    }
                    // Lieu de l'évènement
                    $lieu_ev = $enrEv[0];
                    if ($lieu_ev != 0) fwrite($fp, '2 PLAC ' . lectZone($lieu_ev, $enrEv[1], 'N') . $cr);
                    // Notes pour l'évènement
                    if (Rech_Commentaire($enrEv[6], 'E')) {
                        if (($Environnement == 'L') or ($Diffusion_Commentaire_Internet == 'O')) {
                            Ecrit_Note_Gedcom($Commentaire, $fp);
                        }
                    }
                }
                $resEv->closeCursor();
            }

            // Recherche des enfants de l'union
            $sqlE = 'select Enfant from ' . $n_filiations . ' where Pere=' . $Pere . ' and Mere=' . $Mere . ';';
            if ($resE = lect_sql($sqlE)) {
                while ($enregE = $resE->fetch(PDO::FETCH_NUM)) {
                    fwrite($fp, '1 CHIL @I' . $enregE[0] . '@' . $cr);
                }
            }
            ++$nUnions;
            if (($nUnions % 100) == 0) echo '&nbsp;&nbsp;&nbsp;' . $nUnions . ' ' . my_html(LG_GEDCOM_UNIONS_PROCESS) . '<br>';
        }
    }
    echo '&nbsp;&nbsp;&nbsp;' . $nUnions . ' ' . my_html(LG_GEDCOM_UNIONS_PROCESS) . '<br>';
    $res->closeCursor();

    // Recherche des enfants de mère inconnue
    $Pere = 0;
    $sqlE = 'select Enfant, Pere from ' . $n_filiations . ' where Pere <> 0 and Mere = 0 order by Pere;';
    if ($resE = lect_sql($sqlE)) {
        while ($enregE = $resE->fetch(PDO::FETCH_NUM)) {
            if ($Pere != $enregE[1]) {
                $Pere = $enregE[1];
                fwrite($fp, '0 @F' . $Pere . '_0@ FAM' . $cr);
                fwrite($fp, '1 HUSB @I' . $Pere . '@' . $cr);
            }
            fwrite($fp, '1 CHIL @I' . $enregE[0] . '@' . $cr);
        }
    }

    // Recherche des enfants de père inconnu
    $Mere = 0;
    $sqlE = 'select Enfant, Mere from ' . $n_filiations . ' where Pere = 0 and Mere <> 0 order by Mere;';
    if ($resE = lect_sql($sqlE)) {
        while ($enregE = $resE->fetch(PDO::FETCH_NUM)) {
            if ($Mere != $enregE[1]) {
                $Mere = $enregE[1];
                fwrite($fp, '0 @F0_' . $Mere . '@ FAM' . $cr);
                fwrite($fp, '1 WIFE @I' . $Mere . '@' . $cr);
            }
            fwrite($fp, '1 CHIL @I' . $enregE[0] . '@' . $cr);
        }
    }

    fwrite($fp, "0 TRLR$cr");
    fclose($fp);

    $deb_msg = ($leger) ? LG_GEDCOM_FILE_EXPORT_LIGHT : LG_GEDCOM_FILE_EXPORT;
    echo '<br><br>' . $deb_msg . ' <a href="' . $nom_fic_exp . '" target="_blank">' . $nom_fic_exp . '</a><br>' . "\n";
} else
    echo my_html(LG_GEDCOM_FILE_ERROR);

Insere_Bas($compl);
?>
</body>

</html>