<?php
//=====================================================================
// Exportation au format Gedcom d'une personne
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');
require(__DIR__ . '/../app/ressources/commun_gedcom.php');

$acces = 'L';                            // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Exp_Ged_Pers'];        // Titre pour META
$x = Lit_Env();
$niv_requis = 'P';                        // L'export est ouvert à partir du profil privilégié

// Récupération des variables de l'affichage précédent
$tab_variables = array('annuler');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

require(__DIR__ . '/../app/ressources/gestion_pages.php');

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer', 'N');

$sql = Debut_Ext_Pers_Ged() . ' reference = ' . $Refer;

$res = lect_sql($sql);
$EnrPers = $res->fetch(PDO::FETCH_NUM);

$compl = '';

if (($_SESSION['estPrivilegie']) or ($EnrPers[8] == 'O')) {
    $compl = Ajoute_Page_Info(600, 150);
    Insere_Haut($EnrPers[2] . ' ' . $EnrPers[1], $compl, 'exp_Gedcom_Personne', $Refer);

    $nom_fic_ged = $chemin_Gedcom . 'export_ged_' . $Refer . '.ged';
    if ($fp = fopen($nom_fic_ged, 'w')) {

        get_Lib_FR();

        // Données d'entête
        Entete_Gedcom($fp, 'export_ged_' . $Refer);

        // Données sur la personne
        Personne_Gedcom($fp, $EnrPers);
        $res->closeCursor();

        unset($personnes);
        unset($conjoints);
        unset($notes_unions);

        // Traitement des conjoints ==> if ?
        $sql = 'select Conjoint_1, Conjoint_2, Maries_Le, Ville_Mariage, Reference from ' . nom_table('unions') . ' where ';
        $Sexe = $EnrPers[3];
        switch ($Sexe) {
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
        if ($resUn = lect_sql($sql)) {
            while ($enreg = $resUn->fetch(PDO::FETCH_NUM)) {
                fwrite($fp, "1 FAMS @F" . $enreg[0] . '_' . $enreg[1] . "@$cr");
                switch ($Sexe) {
                    case 'm':
                        $Conjoint = $enreg[1];
                        break;
                    case 'f':
                        $Conjoint = $enreg[0];
                        break;
                }
                $personnes[] = $Conjoint . 'C';
                $conjoints[] = $Conjoint . '§' . $enreg[2] . '§' . $enreg[3];
                $notes_unions[] = $enreg[4];
            }
        }
        $resUn->closeCursor();

        // Traitement des parents ==> if ?
        $Fam_Par = '';
        if (Get_Parents($Refer, $Pere, $Mere, $Rang)) {
            $Fam_Par = $Pere . '_' . $Mere;
            fwrite($fp, "1 FAMC @F" . $Fam_Par . "@$cr");
            if ($Pere) $personnes[] = $Pere . 'P';
            if ($Mere) $personnes[] = $Mere . 'P';
        }

        // Ecriture des données sur les personnes satellites
        if (isset($personnes)) {
            for ($nb_pers = 0; $nb_pers < count($personnes); $nb_pers++) {
                $pers  = $personnes[$nb_pers];
                $lpers = strlen($pers);
                // Conjoint ou parent ?
                $conj = 0;
                if (substr($pers, $lpers - 1, 1) == 'C') $conj = 1;
                $refPers = substr($pers, 0, $lpers - 1);
                $sql = Debut_Ext_Pers_Ged() . ' reference = ' . $refPers;
                $res = lect_sql($sql);
                $enreg = $res->fetch(PDO::FETCH_NUM);
                if ($_SESSION['estGestionnaire'] or $enreg[10] == 'O') {
                    Personne_Gedcom($fp, $enreg);
                } else
                    fwrite($fp, "0 @I" . $refPers . "@ INDI$cr");
                if ($conj) {
                    $point = '';
                    switch ($Sexe) {
                        case 'm':
                            $point = $Refer . '_' . $refPers;
                            break;
                        case 'f':
                            $point = $refPers . '_' . $Refer;
                            break;
                    }
                    fwrite($fp, "1 FAMS @F" . $point . "@$cr");
                }
                $res->closeCursor();
            }
        }

        // Ecriture des familles
        // Famille des parents
        if (($Pere != 0) or ($Mere != 0)) {
            fwrite($fp, "0 @F" . $Fam_Par . "@ FAM$cr");
            fwrite($fp, "1 HUSB @I" . $Pere . "@$cr");
            fwrite($fp, "1 WIFE @I" . $Mere . "@$cr");
            fwrite($fp, "1 CHIL @I" . $Refer . "@$cr");
        }

        if (isset($conjoints)) {
            for ($nb_pers = 0; $nb_pers < count($conjoints); $nb_pers++) {
                $zones = explode('§', $conjoints[$nb_pers]);
                //echo $zones[0].'/'.$zones[1].'/'.$zones[2].'<br>';
                switch ($Sexe) {
                    case 'm':
                        $Husb = $Refer;
                        $Wife = $zones[0];
                        break;
                    case 'f':
                        $Husb = $zones[0];
                        $Wife = $Refer;
                        break;
                }
                fwrite($fp, "0 @F" . $Husb . '_' . $Wife . "@ FAM$cr");
                fwrite($fp, "1 HUSB @I" . $Husb . "@$cr");
                fwrite($fp, "1 WIFE @I" . $Wife . "@$cr");
                if (($zones[1] != '') or ($zones[2] != 0))
                    fwrite($fp, "1 MARR$cr");
                // Traitement de la date éventuelle de mariage
                $LaDate = Etend_date_GedCom($zones[1]);
                if ($LaDate != '') {
                    fwrite($fp, "2 DATE " . $LaDate . "$cr");
                }
                // Traitement du lieu éventuel de mariage
                $LaVille = $zones[2];
                $Lib_Ville = lib_villeG($LaVille);
                if ($Lib_Ville != '')
                    fwrite($fp, "2 PLAC " . $Lib_Ville . "$cr");

                // Notes pour l'union
                if (Rech_Commentaire($notes_unions[$nb_pers], 'U')) {
                    if ($_SESSION['estGestionnaire'] or $Diffusion_Commentaire_Internet == 'O')
                        Ecrit_Note_Gedcom($Commentaire, $fp);
                }
            }
        }

        fwrite($fp, "0 TRLR$cr");
        fclose($fp);
        echo '<br />' . my_html(LG_GEDCOM_FILE) . ' <a href="' . $nom_fic_ged . '" target="_blank">' . $nom_fic_ged . '</a><br />' . "\n";
    } else {
        echo '<center><font color="red"><br><br><br><h2>' . LG_GEDCOM_FILE_ERROR . '</h2></font></center>';
    }

    // Formulaire pour le bouton retour
    Bouton_Retour($lib_Retour, '?' . Query_Str());
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