<?php
//===========================================================================================
// Affectation d'un evenement à une persone ou l'inverse
// Parametres a renseigner :
//  - refPers : reference de la personne (mettre -1 pour creer un lien depuis 1 fiche évènement)
//  - refEvt : la reférence de l'evenement (mettre -1 pour creer un lien depuis 1 fiche personne)
//  - refRolePar : l'identifiant de la table role
//===========================================================================================

session_start();

include('fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'Horigine',
    'annuler',
    'supprimer',
    //    champs recus de la transaction precedente et sauvegardes dans cette page
    //    anciennes valeurs
    'dDebAnc',
    'dFinAnc',
    'idZoneAnc',
    'idNiveauAnc',
    'persPrinAnc',
    'persPrinF',
    'Dans_Etiq_GGAnc',
    //    valeurs saisies dans le formulaire
    'refEveF',
    'refPersF',
    'evenements',
    'idZoneF',
    'idNiveauF',
    'refRoleF',
    'dDebAff',
    'dDebCache',
    'dFinAff',
    'dFinCache',
    'Dans_Etiq_GGF'
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
$acces = 'M';                                    // Type d'accès de la page : (M)ise à jour
$titre = $LG_Menu_Title['Link_Ev_Pers'];        // Titre pour META
$x = Lit_Env();
include('Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup des variables passées dans l'URL
$refPers = Recup_Variable('refPers', 'N');            // Personne associée
$refEvt = Recup_Variable('refEvt', 'N');                // Evènement associé
$refRolePar = Recup_Variable('refRolePar', 'S');        // Rôle associé

if ($persPrinF == '') $persPrinF = 'N';

//  Pour retourner a la page precedente ou non
$retourPreced = false;

//Demande de mise à jour
if (($bt_Sup) or ($bt_OK)) {
    //if ($clic_boutons) {

    $dDebAnc          = Secur_Variable_Post($dDebAnc, 10, 'S');
    $dFinAnc          = Secur_Variable_Post($dFinAnc, 10, 'S');
    $idZoneAnc        = Secur_Variable_Post($idZoneAnc, 1, 'N');
    $idNiveauAnc      = Secur_Variable_Post($idNiveauAnc, 1, 'N');
    $persPrinAnc      = Secur_Variable_Post($persPrinAnc, 1, 'S');
    $persPrinF        = Secur_Variable_Post($persPrinF, 1, 'S');
    $refEveF          = Secur_Variable_Post($refEveF, 1, 'N');
    $evenements       = Secur_Variable_Post($evenements, 1, 'N');
    $refPersF         = Secur_Variable_Post($refPersF, 1, 'N');
    $idZoneF          = Secur_Variable_Post($idZoneF, 100, 'S');
    $idNiveauF        = Secur_Variable_Post($idNiveauF, 1, 'N');
    $refRoleF         = Secur_Variable_Post($refRoleF, 4, 'S');
    $dDebCache        = Secur_Variable_Post($dDebCache, 10, 'S');
    $dFinCache        = Secur_Variable_Post($dFinCache, 10, 'S');
    $Dans_Etiq_GGF    = Secur_Variable_Post($Dans_Etiq_GGF, 1, 'S');
    $Dans_Etiq_GGAnc  = Secur_Variable_Post($Dans_Etiq_GGAnc, 1, 'S');

    //  preparation des traitements
    $valeurs = '';
    $traitCre = false;
    $traitMaj = false;
    $traitSup = false;
    $result = true;
    $maj_site = false;

    if ($supprimer == 'Supprimer') $traitSup = true;

    //  Creation ou modification ?
    if ($bt_OK) {

        // Vérification si création d'une zone géographique
        switch ($idNiveauF) {
            case 1:
                $idZoneF = Ajoute_Pays($idZoneF);
                break;
            case 2:
                $idZoneF = Ajoute_Region($idZoneF);
                break;
            case 3:
                $idZoneF = Ajoute_Departement($idZoneF);
                break;
            case 4:
                $idZoneF = Ajoute_Ville($idZoneF);
                break;
        }

        // Sur niveau zone 0, mise à zéro lieu
        if ($idNiveauF == 0) $idZoneF = '0';

        // On est en création si la personne où l'évènement était nouveau (-1)
        if (($refEvt == -1) or ($refPers == -1)) $traitCre = true;
        else {
            // Personne et évènement connus
            // Modification si rôle égal
            // Création sinon
            if ($refRoleF == $refRolePar)
                $traitMaj = true;
            else
                $traitCre = true;
        }

        // Personne non saisie, on ne peut pas créer
        if ($refPersF == 0) {
            $traitCre = false;
            $retourPreced = true;
        }

        //  Recherche s'il y a un lien defini avec un personnage principal
        //  Si on en trouve un, on supprime le personnage principal puisque la personne en cours de traitement
        //  doit etre definie comme personne principale
        if (($persPrinF == 'O') and ($persPrinAnc == 'N')) {
            $requete  = "update " . nom_table('participe') . " SET Pers_Principal='N'" .
                " WHERE Evenement = $refEveF AND Personne != $refPers " .
                "AND Pers_Principal = 'O'";
            $result = maj_sql($requete);
            $maj_site = true;
        }
        //  Creation
        if ($traitCre) {
            //Ins_Zone_Req($refEveF , 'N' , $valeurs);
            if ($evenements) Ins_Zone_Req($evenements, 'N', $valeurs);
            else Ins_Zone_Req($refEveF, 'N', $valeurs);
            Ins_Zone_Req($refPersF, 'N', $valeurs);
            Ins_Zone_Req($refRoleF, 'A', $valeurs);
            // pour éviter le null...
            $zones = explode(',', $valeurs);
            if ($zones[count($zones) - 1] == 'null') {
                $zones[count($zones) - 1] = '\'\'';
                $valeurs = implode(',', $zones);
            }
            Ins_Zone_Req($dDebCache, 'A', $valeurs);
            Ins_Zone_Req($dFinCache, 'A', $valeurs);
            Ins_Zone_Req($persPrinF, 'A', $valeurs);
            Ins_Zone_Req($idZoneF, 'N', $valeurs);
            Ins_Zone_Req($idNiveauF, 'N', $valeurs);
            Ins_Zone_Req($Dans_Etiq_GGF, 'A', $valeurs);
            $requete  = 'INSERT INTO ' . nom_table('participe') . " VALUES ($valeurs)";
            $result =  maj_sql($requete);
            $maj_site = true;
        }
        //  Mise a jour
        if ($traitMaj) {
            Aj_Zone_Req('Debut', $dDebCache, $dDebAnc, 'A', $valeurs);
            Aj_Zone_Req('Fin', $dFinCache, $dFinAnc, 'A', $valeurs);
            Aj_Zone_Req('Pers_Principal', $persPrinF, $persPrinAnc, 'A', $valeurs);
            Aj_Zone_Req('Identifiant_zone', $idZoneF, $idZoneAnc, 'N', $valeurs);
            Aj_Zone_Req('Identifiant_Niveau', $idNiveauF, $idNiveauAnc, 'N', $valeurs);
            Aj_Zone_Req('Dans_Etiquette_GeneGraphe', $Dans_Etiq_GGF, $Dans_Etiq_GGAnc, 'A', $valeurs);
            if ($valeurs != '') {
                $requete  = 'UPDATE ' . nom_table('participe') . " SET $valeurs"
                    . " WHERE Evenement = $refEveF AND Personne = $refPersF AND Code_Role = '$refRoleF'";
                $result = maj_sql($requete);
                $maj_site = true;
            }
        }
    }

    //  Suppression du lien
    if ($traitSup) {
        $requete = 'DELETE FROM ' . nom_table('participe') . " WHERE Evenement = $refEveF";
        $requete .= " AND Personne = $refPersF AND Code_Role = '$refRoleF'";
        $result = maj_sql($requete);
        $maj_site = true;
    }

    // Modification de la date de mise à jour du site sans toucher au paramètre base vide
    if ($maj_site) maj_date_site(false);

    // Retour à la page précédente
    if ($result) Retour_Ar();
} else {

    //
    //  ========== Programme principal ==========

    //  =============================================================================================
    //  Affichage pour saisie
    //  =============================================================================================

    include('jscripts/Edition_Lier_Eve.js');

    $compl = Ajoute_Page_Info(600, 200);

    Insere_Haut(my_html($titre), $compl, 'Edition_Lier_Eve', '');
    //  Valeurs par defaut
    $persPrin = 'N';
    $dDebLue = ' ';
    $dFinLue = ' ';

    // Si la personne est connue, on ne pourra pas la modifier
    if ($refPers != -1) {
        $x = Get_Nom_Prenoms($refPers, $Nom, $Prenoms);
        // Libellé spécifique pour personne connue et évènement inconnu
        if ($refEvt == -1) {
            if ($x) echo '<br />' . $LG_Link_Ev_Link . ' ' . $Prenoms . ' ' . $Nom . "<br />\n";
        }
    }
    // Si l'évènement est connu, on ne pourra pas le modifier
    $lib_evt = libelle_lien_evt($refEvt, 'P', $refPers);

    // La personne et l'évènement sont connus ==> on est en modification
    if (($refPers != -1) and ($refEvt != -1)) {
        $requete  = 'SELECT Titre, Code_Type,p.Debut AS dDeb,p.Fin AS dFin,Pers_Principal,Personne,Code_Role,' .
            ' p.Identifiant_zone, p. Identifiant_Niveau, p.Dans_Etiquette_GeneGraphe ' .
            ' FROM ' . nom_table('participe') . ' AS p , ' . nom_table('evenements') .
            " AS e WHERE Evenement = $refEvt" .
            " AND Personne = $refPers AND Code_Role = '$refRolePar' " .
            ' AND Evenement = Reference limit 1';
        $result    = lect_sql($requete);
        $enreg  = $result->fetch(PDO::FETCH_ASSOC);
        //  Valeurs lues dans la base
        if ($enreg) {
            $codeTypeLu = $enreg['Code_Type'];
            Champ_car($enreg, 'Code_Type');
            $persPrin = $enreg['Pers_Principal'];
            $dDebLue = $enreg['dDeb'];
            $dFinLue = $enreg['dFin'];
            $idZoneLu = $enreg['Identifiant_zone'];
            $idNiveauLu = $enreg['Identifiant_Niveau'];
            $Dans_Etiq_GG = $enreg['Dans_Etiquette_GeneGraphe'];
            // Libellé spécifique pour évènement connu et personne connue
            echo 'Lien de ' . $Prenoms . ' ' . $Nom . ' avec <a href="Fiche_Evenement.php?refPar=' . $refEvt . '">' . $lib_evt . '</a><br />' . "\n";
        }
    }
    // Initialisation des variables d'affichage en création
    else {
        $codeTypeLu = '';
        $persPrin = '';
        $dDebLue = '';
        $dFinLue = '';
        $idZoneLu = 0;
        $idNiveauLu = 0;
        $Dans_Etiq_GG = 'n';
    }

    $z_controle = '';
    if ($refPers == -1) $z_controle = 'refPersF';
    if ($refEvt == -1) $z_controle = 'refEveF';

    //  Debut de la page
    echo '<br />';
    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'' . $z_controle . '\');" action="' . my_self() . '?' . Query_Str() . '">' . "\n";
    aff_origine();
    // Zones actuelles
    echo '<input type="' . $hidden . '" name="persPrinAnc" value="' . $persPrin . '"/>' . "\n";
    echo '<input type="' . $hidden . '" name="idZoneF" value="' . $idZoneLu . '"/>' . "\n";
    echo '<input type="' . $hidden . '" name="idZoneAnc" value="' . $idZoneLu . '"/>' . "\n";
    echo '<input type="' . $hidden . '" name="idNiveauAnc" value="' . $idNiveauLu . '"/>' . "\n";
    echo '<input type="' . $hidden . '" name="Dans_Etiq_GGAnc" value="' . $Dans_Etiq_GG . '"/>' . "\n";
    echo '<input type="' . $hidden . '" name="maxi" id="maxi" />';

    // L'évènement n'est pas modifiable si connu
    if ($refEvt != -1) {
        echo '<input type="' . $hidden . '" name="refEveF" value="' . $refEvt . '"/>' . "\n";
    }
    // La personne n'est pas modifiable si connue
    if ($refPers != -1) {
        echo '<input type="' . $hidden . '" name="refPersF" value="' . $refPers . '"/>' . "\n";
    }

    $larg_titre = '30';

    echo '<table width="90%" class="table_form" align="center">' . "\n";

    // Choix des personnes qui peuvent être rattachées à un évènement
    if ($refPers == -1) {
        echo colonne_titre_tab($LG_Link_Ev_Link_Pers);
        // Affichage d'un select avec la liste des personnes
        aff_liste_pers(
            'refPersF',           // Nom du select
            1,                    // 1ère fois
            1,                    // dernière fois
            -1,                   // critère de sélection
            '',                   // crtitère de  sélection
            'Nom, Prenoms',       // critère de tri de la liste
            1
        );                   // zone obligatoire
        echo "</td></tr>\n";
    }

    // Choix des évènements qui peuvent être rattachés à la personne
    if ($refEvt == -1) {
        echo colonne_titre_tab($LG_Link_Ev_Link_Event_Type);
        // On retient les évènements multiples ou
        // les évènements uniques qui ne sont pas déjà utilisés
        $sql_types = 'SELECT DISTINCT e.Code_Type, t.Libelle_Type' .
            ' FROM ' . nom_table('evenements') . ' e, ' . nom_table('types_evenement') . ' t' .
            ' WHERE e.Code_Type = t.Code_Type' .
            '  and t.Objet_Cible = "P"' .
            '  and (t.Unicite="M" or (' .
            '		t.Unicite="U" and e.Code_Type not in (select Code_Type from ' . nom_table('evenements') . ' where' .
            ' Reference in (select Evenement ' .
            ' from ' . nom_table('participe') . ' where Personne = ' . $refPers . ')) ' .
            ')' .
            ')' .
            ' ORDER by t.Libelle_Type';

        $res = lect_sql($sql_types);
        echo '<select name="types_evt" id="types_evt" onchange="updateEvts(this.value)" class="oblig">';
        while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
            echo '<option value="' . $enreg[0] . '">' . $enreg[1] . '</option>';
        }
        echo '</select>' . "\n";
        echo '&nbsp;&nbsp;&nbsp;' . Img_Zone_Oblig('imgObligTEvt');
        echo "</td></tr>\n";

        echo colonne_titre_tab($LG_Link_Ev_Link_Event);
        echo '<select name="evenements" id="evenements" class="oblig"></select>';
        echo '&nbsp;&nbsp;&nbsp;' . Img_Zone_Oblig('imgObligEvts');
        echo '<div class="buttons">';
        echo '<button type="submit" class="positive" ' .
            'onclick="document.forms.saisie.evenements.value = document.forms.saisie.maxi.value;return false;"> ' .
            '<img src="' . $chemin_images_icones . $Icones['dernier_ajoute'] . '" alt=""/>' . my_html($LG_Link_Ev_Link_Last_Event) . '</button>';
        echo '</div>';
        echo "</td></tr>\n";
    }
    /*
	else {
		echo '<select name="types_evt" id="types_evt" >';
		// while ($enreg = $res->fetch(PDO::FETCH_NUM)) {
			// echo '<option value="'.$enreg[0].'">'.$enreg[1].'</option>';
		// }
		echo '</select>'."\n";
	}
	*/

    //
    //  ===== Role
    echo colonne_titre_tab($LG_Link_Ev_Link_Role);
    $refRole = '';
    $requete = 'SELECT Code_Role, Libelle_Role FROM ' . nom_table('roles') . ' ORDER BY Libelle_Role';
    $result = lect_sql($requete);
    echo '<select name="refRoleF" id="refRoleF" class="oblig">' . "\n";
    while ($enreg = $result->fetch(PDO::FETCH_NUM)) {
        $c_role = $enreg[0];
        echo '<option value="' . $c_role . '"';
        if ($c_role ===     $refRolePar) echo ' selected="selected" ';
        echo '>' . my_html($enreg[1]) . "</option>\n";
    }
    echo '</select>&nbsp;';
    Img_Zone_Oblig('imgObligRole');
    echo "</td></tr>\n";
    echo col_titre_tab($LG_Link_Ev_Main_Pers, $larg_titre);
    echo '<td class="value">';
    echo '<input type="checkbox" value="O" name="persPrinF"';
    if ($persPrin == 'O') echo ' checked="checked"';
    echo '/>';
    echo "</td></tr>\n";

    ligne_vide_tab_form(1);

    //  ===== Zone géographique
    echo colonne_titre_tab($LG_Link_Ev_Link_Place);
    // Niveau de la zone géographique associée
    echo '<input type="radio" id="idNiveauF_0" name="idNiveauF" value="0"';
    if ($idNiveauLu == 0) echo ' checked="checked"';
    echo ' onclick="cache_image_lieu(\'img_zone\')"/> <label for="idNiveauF_0">' . $LG_Link_Ev_Link_NoPlace . '</label> ' . "\n";
    // echo ' onclick="bascule_image(\'img_zone\')"/> <label for="idNiveauF_0">'.$LG_Link_Ev_Link_NoPlace.'</label> '."\n";
    $req = 'select Identifiant_Niveau, Libelle_Niveau from ' . nom_table('niveaux_zones');
    $result = lect_sql($req);
    while ($enr_zone = $result->fetch(PDO::FETCH_NUM)) {
        $la_zone = $enr_zone[0];
        echo '<input type="radio" id="idNiveauF_' . $la_zone . '" name="idNiveauF" value="' . $la_zone . '"';
        if ($la_zone == $idNiveauLu) echo ' checked="checked"';
        echo ' onclick="montre_image_lieu(\'img_zone\')"/><label for="idNiveauF_' . $la_zone . '">' . $enr_zone[1] . '</label>&nbsp;' . "\n";
        // echo ' onclick="bascule_image(\'img_zone\')"/><label for="idNiveauF_'.$la_zone.'">'.$enr_zone[1].'</label>&nbsp;'."\n";
    }

    // Recherche du libellé de la zone géographique
    $lib_zone = '';
    if ($idZoneLu != 0) {
        switch ($idNiveauLu) {
            case 1:
                $lib_zone = lib_pays($idZoneLu);
                break;
            case 2:
                $lib_zone = lib_region($idZoneLu);
                break;
            case 3:
                $lib_zone = lib_departement($idZoneLu);
                break;
            case 4:
                $lib_zone = lib_ville($idZoneLu);
                break;
        }
    }
    echo '<input type="text" readonly="readonly" name="zoneAff" size="50" value="' . $lib_zone . '"' . "/>\n";
    echo '<img id="img_zone" src="' . $chemin_images_icones . $Icones['localisation'] . '" alt="' . $LG_Place_Select . '" title="' . $LG_Place_Select .
        '" onclick="Appelle_Zone();" ';
    if ($idNiveauLu == 0) echo ' style="display:none; visibility:hidden;"';
    echo '/>' . "\n";
    echo "</td></tr>\n";

    //
    //  ===== Date de début de participation
    echo colonne_titre_tab($LG_Link_Ev_Link_Beg_Part);
    zone_date2('dDebAnc', 'dDebAff', 'dDebCache', $dDebLue);
    echo "</td></tr>\n";
    //
    //  ===== Date de fin de participation
    echo colonne_titre_tab($LG_Link_Ev_Link_End_Part);
    zone_date2('dFinAnc', 'dFinAff', 'dFinCache', $dFinLue);
    echo '&nbsp;<img src="' . $chemin_images_icones . $Icones['copie_calend'] . '" alt= "' . $LG_Link_Ev_Link_Paste_Beg . '" title="' . $LG_Link_Ev_Link_Paste_Beg .
        '"onclick="copieDate();"/>' . "\n";
    echo "</td></tr>\n";

    //  ===== Etiquette GénéGraphe, uniquement en local
    if ($Environnement == 'L') {
        echo colonne_titre_tab($LG_Link_Ev_Link_Save_GeneGraphe);
        echo '<input type="radio" name="Dans_Etiq_GGF" value="o"';
        if ($Dans_Etiq_GG == 'o') echo ' checked="checked"';
        echo '/>' . my_html($LG_Yes) . '&nbsp;' . "\n";
        echo '<input type="radio" name="Dans_Etiq_GGF" value="n"';
        if ($Dans_Etiq_GG == 'n') echo ' checked="checked"';
        echo '/>' . my_html($LG_No) . '&nbsp;' . "\n";
        echo "</td></tr>\n";
    } else {
        echo '<input type="' . $hidden . '" name="Dans_Etiq_GGF" value="n"/>' . "\n";
    }

    ligne_vide_tab_form(1);
    $lib_sup = '';
    if (($refEvt != -1) and ($refPers != -1)) $lib_sup = $lib_Supprimer;
    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, $LG_this_link);

    echo "</table>";

    echo "</form>\n";
    Insere_Bas($compl);
}

?>
</body>

</html>