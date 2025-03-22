<?php
//=====================================================================
// Création et modification d'un evenement
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

if (!IS_GRANTED('C')) {
    header('Location: ' . $root . '/');
    exit();
}

$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    //  Parametre d'appel de la page
    'refPar',
    //  Anciennes valeurs des zones
    'idZoneAnc',
    'codeTypeAnc',
    'titreAnc',
    'dDebAnc',
    'dFinAnc',
    'diversAnc',
    'diffNoteAnc',
    'AStatut_Fiche',
    'idNiveauAnc',
    //  Valeurs saisies
    'idZoneF',
    'codeTypeF',
    'titreF',
    'dDebCache',
    'dFinCache',
    'diversF',
    'diffNoteF',
    'Statut_Fiche',
    'idNiveauF',
    'Horigine'
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

// Recup de la variable passée dans l'URL : référence de l'évènement, actualité o/n
$refPar = Recup_Variable('refPar', 'N');
$actu = Recup_Variable('actu', 'C', 'xo');
$actualite = ($actu === 'o' ? true : false);
$Modif = true;
if ($refPar == -1) $Modif = false;

// Titre pour META
$modif = false;
if ($refPar != -1) $modif = true;
if ($actualite) {
    if ($modif) $titre = $LG_Menu_Title['New_Edit'];
    else $titre = $LG_Menu_Title['New_Add'];
} else {
    if ($modif) $titre = $LG_Menu_Title['Event_Edit'];
    else $titre = $LG_Menu_Title['Event_Add'];
}

$x = Lit_Env();
require(__DIR__ . '/../app/ressources/gestion_pages.php');


$refPar        = Secur_Variable_Post($refPar, 1, 'N');
$idZoneAnc     = Secur_Variable_Post($idZoneAnc, 1, 'N');
$codeTypeAnc   = Secur_Variable_Post($codeTypeAnc, 4, 'S');
$titreAnc      = Secur_Variable_Post($titreAnc, 80, 'S');
$dDebAnc       = Secur_Variable_Post($dDebAnc, 10, 'S');
$dFinAnc       = Secur_Variable_Post($dFinAnc, 10, 'S');
$diversAnc     = Secur_Variable_Post($diversAnc, 65535, 'S');
$diffNoteAnc   = Secur_Variable_Post($diffNoteAnc, 1, 'S');
$AStatut_Fiche = Secur_Variable_Post($AStatut_Fiche, 1, 'S');
$idNiveauAnc   = Secur_Variable_Post($idNiveauAnc, 1, 'N');
$idZoneF       = Secur_Variable_Post($idZoneF, 50, 'S');
$codeTypeF     = Secur_Variable_Post($codeTypeF, 50, 'S');
$titreF        = Secur_Variable_Post($titreF, 80, 'S');
$dDebCache     = Secur_Variable_Post($dDebCache, 10, 'S');
$dFinCache     = Secur_Variable_Post($dFinCache, 10, 'S');
$diversF       = Secur_Variable_Post($diversF, 65535, 'S');
$diffNoteF     = Secur_Variable_Post($diffNoteF, 1, 'S');
$Statut_Fiche  = Secur_Variable_Post($Statut_Fiche, 1, 'S');
$idNiveauF     = Secur_Variable_Post($idNiveauF, 1, 'N');

//  ========== Mise a jour de la base ==========
$retourPreced = false;
$valeurs = '';
$message = '';

// Type d'objet des évènements
$Type_Ref = 'E';

// Largeur de la colonne de titre exprimée en pourcentage
$largP = 20;

// Suppression demandée
if ($bt_Sup) {

    $fin_req = ' where Reference_Objet = ' . $refPar . " and Type_Objet = 'E'";
    // Suppression des commentaires
    if ($diversAnc != '') {
        $res = maj_sql('delete from ' . nom_table('commentaires') . $fin_req);
    }
    // Suppression des liens vers les documents
    $res = maj_sql('delete from ' . nom_table('concerne_doc') . $fin_req);
    // Suppression des liens vers les images
    $req = 'delete from ' . nom_table('images') . ' where Reference = ' . $refPar . " and Type_Ref = 'E'";
    $res = maj_sql($req);

    // Suppression de l'évènement
    $req = 'DELETE FROM ' . nom_table('evenements') . " WHERE reference = $refPar";
    $res = maj_sql($req);

    maj_date_site();
    Retour_Ar();
}

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
        case 5:
            $idZoneF = Ajoute_Subdivision($idZoneF);
            break;
    }

    // Init des zones de requête ==> valeurs par défaut
    if ($Statut_Fiche == '') $Statut_Fiche = 'N';
    if ($diffNoteF == '') $diffNoteF = 'N';

    $req_comment = '';

    $maj_site = false;

    // Sur niveau zone 0, mise à zéro lieu
    if ($idNiveauF == 0) $idZoneF = 0;

    //  Mise a jour de la base
    if ($refPar == -1) {
        // Creation
        Ins_Zone_Req($idZoneF, 'N', $valeurs);
        Ins_Zone_Req($idNiveauF, 'N', $valeurs);
        Ins_Zone_Req($codeTypeF, 'A', $valeurs);
        Ins_Zone_Req($titreF, 'A', $valeurs);
        Ins_Zone_Req($dDebCache, 'A', $valeurs);
        Ins_Zone_Req($dFinCache, 'A', $valeurs);
        Ins_Zone_Req($Statut_Fiche, 'A', $valeurs);
        $requete  = 'INSERT INTO ' . nom_table('evenements') .
            ' (identifiant_zone , Identifiant_Niveau, Code_Type , Titre , Debut , Fin , ' .
            ' Statut_Fiche , Date_Creation , Date_Modification) ' .
            'VALUES (' . $valeurs . ' , current_timestamp , current_timestamp)';
        $result = maj_sql($requete);
        if ($result == 1) {
            $retourPreced = true;
            $maj_site = true;
        }

        // Création d'un enregistrement dans la table commentaires
        if ($diversF != '') {
            insere_commentaire($connexion->lastInsertId(), $Type_Ref, $diversF, $diffNoteF);
        }
    } else {
        // Modification
        Aj_Zone_Req('Identifiant_zone', $idZoneF, $idZoneAnc, 'N', $valeurs);
        Aj_Zone_Req('Identifiant_Niveau', $idNiveauF, $idNiveauAnc, 'N', $valeurs);
        Aj_Zone_Req('Code_Type', $codeTypeF, $codeTypeAnc, 'A', $valeurs);
        Aj_Zone_Req('Titre', $titreF, $titreAnc, 'A', $valeurs);
        Aj_Zone_Req('Debut', $dDebCache, $dDebAnc, 'A', $valeurs);
        Aj_Zone_Req('Fin', $dFinCache, $dFinAnc, 'A', $valeurs);
        Aj_Zone_Req('Statut_Fiche', $Statut_Fiche, $AStatut_Fiche, 'A', $valeurs);
        if ($valeurs == '') $retourPreced = true;
        else {
            $requete  = 'UPDATE ' . nom_table('evenements') . " SET $valeurs , Date_Modification=current_timestamp";
            $requete .= " WHERE Reference = $refPar";
            $result = maj_sql($requete);
            if ($result == 1) {
                $retourPreced = true;
                $maj_site = true;
            }
        }
        // Traitement des commentaires
        maj_commentaire($refPar, $Type_Ref, $diversF, $diversAnc, $diffNoteF, $diffNoteAnc);
    }
    // Exécution de la requête sur les commentaires
    if ($req_comment != '') {
        $res = maj_sql($req_comment);
        $maj_site = true;
    }

    if ($maj_site) maj_date_site();
}

// Retour sur la page précédente
if ($retourPreced) Retour_Ar();

//  ========== Programme principal ==========
if (!IS_GRANTED('C')) {
    Insere_Haut(my_html($titre), '', 'Edition_Evenement', "");
    echo '<br><img src="' . $root . '/assets/img/stop.png" alt="Stop"/>' . $LG_function_noavailable_profile . '<br>';
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/' . $Icones['home'] . '" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
    return;
} else {
    include(__DIR__ . '/../public/assets/js/Edition_Evenement.js');
    include(__DIR__ . '/../public/assets/js/gest_onglets.js');
    include(__DIR__ . '/../public/assets/js/Insert_Tiny.js');

    // Récupération des données de l'évènement
    if ($Modif) {
        $requete = 'SELECT e.*, t.Objet_Cible FROM ' . nom_table('evenements') . ' e, ' . nom_table('types_evenement') . ' t' .
            " WHERE reference = $refPar" .
            ' AND e.Code_Type = t.Code_Type limit 1';
        $result = lect_sql($requete);
        $enreg = $result->fetch(PDO::FETCH_ASSOC);
        // $enreg2 = $enreg;
    } else {
        $enreg['Identifiant_zone'] = 0;
        $enreg['Identifiant_Niveau'] = 0;
        $enreg['Code_Type'] = '';
        $enreg['Titre'] = '';
        $enreg['Debut'] = '';
        $enreg['Fin'] = '';
        $enreg['Statut_Fiche'] = 'N';
        $enreg['Objet_Cible'] = '';
        $enreg['Diff_Internet'] = '';
        $enreg['Date_Creation'] = '';
        $enreg['Date_Modification'] = '';
    }

    /*
	1 	ReferencePrimaire 	int(11)
	2 	Identifiant_zone 	int(11)
	3 	Identifiant_Niveau 	int(11)
	4 	Code_Type 	varchar(4)
	5 	Titre 	varchar(80)
	6 	Debut 	varchar(10)
	7 	Fin 	varchar(10)
	8 	Date_Creation 	datetime
	9 	Date_Modification 	datetime
	10 	Statut_Fiche 	char(1)
	
	1 	Code_TypePrimaire 	varchar(4) 
	2 	Libelle_Type 	varchar(50)
	3 	Code_Modifiable 	char(1)
	4 	Objet_Cible 	char(1)
	5 	Unicite 	char(1)
	6 	Type_Gedcom 	char(1)
	*/

    //  Mise en place des donnees
    $idZoneLu     = $enreg['Identifiant_zone'];
    $idNiveauLu   = $enreg['Identifiant_Niveau'];
    $codeTypeLu   = $enreg['Code_Type'];
    $titreLu      = $enreg['Titre'];
    $dDebLu       = $enreg['Debut'];
    $dFinLu       = $enreg['Fin'];
    $statutLu     = $enreg['Statut_Fiche'];
    $objetCibleLu = $enreg['Objet_Cible'];

    // Recherche si l'évènement est lie à une personne, une filiation ou une union
    $nbLiens = 0;
    if ($refPar != -1) {
        if ($objetCibleLu == 'P') {
            //  Recherche de liens avec une personne
            $requete = 'SELECT 1 FROM ' . nom_table('participe') . ' WHERE Evenement = ' . $refPar . ' limit 1';
            $result = lect_sql($requete);
            $nbLiens += $result->rowCount();
        }
        //  Recherche de liens avec une filiation ou une union
        if (($objetCibleLu == 'F') or ($objetCibleLu == 'U')) {
            $requete = 'SELECT 1 FROM ' . nom_table('concerne_objet') . ' WHERE Evenement = ' . $refPar . ' limit 1';
            $result = lect_sql($requete);
            $nbLiens += $result->rowCount();
        }
    }

    $compl = Ajoute_Page_Info(600, 250);
    if ($refPar != -1)
        $compl .= Affiche_Icone_Lien('href="' . $root . '/liste_images?Refer=' . $refPar . '&amp;Type_Ref=E"', 'images', 'Images') . ' ';
    if ($refPar != -1) {
        $ajout = '';
        //if ($actualite) $ajout = '&amp;actu=o';
        //$compl .= Affiche_Icone_Lien('href="'. $root .'/fiche_evenement?refPar=' .$refPar . $ajout. '"','page','Fiche évènement') . ' ';
        if ($actualite)
            $compl .= Affiche_Icone_Lien('href="' . $root . '/fiche_actualite?refPar=' . $refPar . '"', 'page', $LG_Menu_Title['New']) . ' ';
        else
            $compl .= Affiche_Icone_Lien('href="' . $root . '/fiche_evenement?refPar=' . $refPar . '"', 'page', $LG_Menu_Title['Event']) . ' ';
    }

    Insere_Haut(my_html($titre), $compl, 'Edition_Evenement', '');

    echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'titreF,codeTypeF\')">' . "\n";
    echo '<input type="hidden" name="refPar" value="' . $refPar . '"/>' . "\n";

    // Dans le cas de création d'actualité, on ne présentera pas de select avec le type
    if ($actualite) {
        echo '<input type="hidden" name="codeTypeF" value="' . $TypeEv_actu . '"/>' . "\n";
        echo '<input type="hidden" name="codeTypeAnc" value="' . $TypeEv_actu . '"/>' . "\n";
    }

    echo '<div id="content">' . "\n";
    echo '<table id="cols"  cellpadding="0" cellspacing="0" >' . "\n";
    echo '<tr>' . "\n";
    echo '<td style="border-right:0px solid #9cb0bb">' . "\n";
    echo '  <img src="' . $chemin_images . $Icones['clear'] . '" width="750" height="1" alt="clear"/>' . "\n";
    echo '</td></tr>' . "\n";

    echo '<tr>' . "\n";
    echo '<td class="left">' . "\n";
    echo '<div class="tab-container" id="container1">' . "\n";
    // Onglets
    echo '<ul class="tabs">' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'pane1\', this)" id="tab1">' . $LG_Data_tab . '</a></li>' . "\n";
    if (!$actualite) {
        if ($refPar != -1) {
            // Texte à afficher en modification sur le type de lien
            $txt = lib_pfu($objetCibleLu);
            echo '<li><a href="#" onclick="return showPane(\'pane4\', this)">' . my_html('Liens vers des ' . $txt . 's') . '</a></li>' . "\n";
        }
    }
    // Pas de document en création
    if ($modif)
        echo '<li><a href="#" onclick="return showPane(\'panDocs\', this)">' . LG_CH_DOCS . '</a></li>' . "\n";
    echo '<li><a href="#" onclick="return showPane(\'panFiche\', this)">' . LG_CH_FILE . '</a></li>' . "\n";
    echo '</ul>' . "\n";

    echo '<div class="tab-panes">' . "\n";
    // Onglets données générales de l'évènement
    echo '<div id="pane1">' . "\n";
    // Pavé données
    echo '<fieldset>' . "\n";
    echo '<legend>' . $LG_Data_tab . '</legend>';
    echo '<table width="100%">' . "\n";
    // Titre
    echo '<tr><td width="20%">' . $LG_Event_Title . '</td>';
    echo '<td><input type="text" size="50" name="titreF" id="titreF" value="' . $titreLu . '" class="oblig"/> ' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    if (!$actualite) {
        if ($refPar != -1) {
            // Texte à afficher en modification sur le type de lien
            if ($txt != '') echo "  &Agrave; lier &agrave; une $txt";
        }
    }
    echo '<input type="hidden" name="titreAnc" value="' . $titreLu . '"/>' . "\n";
    echo '</td>';

    // Affichage de l'image par défaut pour l'évènement
    echo '<td rowspan="3" align="center" valign="middle">';
    // Recherche de la présence d'une image par défaut
    $image = Rech_Image_Defaut($refPar, 'E');
    if ($image != '') {
        $image = $chemin_images_util . $image;
        Aff_Img_Redim_Lien($image, 110, 110, "id_" . $refPar);
    } else echo ' ';
    echo '</td>' . "\n";

    echo "</tr>\n";
    //  ===== Type d'evenement
    $requete  = 'SELECT * FROM ' . nom_table('types_evenement');
    if ($nbLiens > 0) {
        //  cet evenement est lie a quelqu'un => on ne peut pas changer de type de lien
        $requete .= " WHERE Objet_Cible = '$objetCibleLu'";
    }
    $requete .= ' ORDER BY Objet_Cible , Libelle_Type';
    $resultat = lect_sql($requete);

    if (!$actualite) {
        $objetCibleAff = '';
        $objetCibleAffC = '';
        echo '<tr><td width="20%">' . $LG_Event_Type . '</td>';
        echo '<td><select class="oblig" name="codeTypeF">' . "\n";
        while ($enregT = $resultat->fetch(PDO::FETCH_ASSOC)) {
            //  Affichage du type de cible
            if ($enregT['Objet_Cible'] != $objetCibleAff) {
                if ($objetCibleAff != '') {
                    echo "</optgroup>\n";
                }
                $objetCibleAff = $enregT['Objet_Cible'];
                echo '<optgroup label="' . lib_pfu($objetCibleAff, false) . '">';
            }
            //  affichage d'un type d'evenement
            echo "<option value='" . $enregT['Code_Type'] . "'";
            if ($enregT['Code_Type'] == $codeTypeLu) {
                echo ' selected';
                $objetCibleAffC = $objetCibleAff;
            }
            echo '>';
            echo my_html($enregT['Libelle_Type']) . "</option>\n";
        }
        echo "</optgroup></select> ";
        echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
        echo '<input type="hidden" name="codeTypeAnc" value="' . $codeTypeLu . '"/></td></tr>' . "\n";
    }

    //  ===== Zone géographique
    echo '<tr><td width="20%">' . $LG_Event_Where . '</td>';
    echo "<td>\n";
    echo '<input type="hidden" name="idZoneF" value="' . $idZoneLu . '"/>' . "\n";
    echo '<input type="hidden" name="idZoneAnc" value="' . $idZoneLu . '"/>' . "\n";
    echo '<input type="hidden" name="idNiveauAnc" value="' . $idNiveauLu . '"/>' . "\n";

    // Niveau de la zone géographique associée
    echo '<input type="radio" id="idNiveauF0" name="idNiveauF" value="0"';
    if ($idNiveauLu == 0) echo ' checked="checked"';
    echo ' onclick="cache_image_zone()"/><label for="idNiveauF0">' . $LG_Event_No_Where . '</label> ' . "\n";
    $req = 'select * from ' . nom_table('niveaux_zones');
    $result = lect_sql($req);
    while ($enr_zone = $result->fetch(PDO::FETCH_ASSOC)) {
        $niv = $enr_zone['Identifiant_Niveau'];
        echo '<input type="radio" id="idNiveauF' . $niv . '" name="idNiveauF" value="' . $niv . '"';
        if ($enr_zone['Identifiant_Niveau'] == $idNiveauLu) echo ' checked="checked"';
        echo ' onclick="montre_image_zone()"/><label for="idNiveauF' . $niv . '">' . $enr_zone['Libelle_Niveau'] . '</label> ' . "\n";
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
            case 5:
                $lib_zone = lib_subdivision($idZoneLu);
                break;
        }
    }
    echo '<input type="text" readonly="readonly" name="zoneAff" id="zoneAff" value="' . $lib_zone . '"/>' . "\n";
    echo '<img id="img_zone" src="' . $root . '/assets/img/' . $Icones['localisation'] . '" alt="' . $LG_Place_Select . '" title="' . $LG_Place_Select .
        '" onclick="Appelle_Zone();" ';
    if ($idNiveauLu == 0) echo ' style="display:none; visibility:hidden;"';
    echo '/>' . "\n";
    echo "</td></tr>\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    // Pavé dates
    echo '<fieldset>' . "\n";
    echo '<legend>' . $LG_Event_When . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";
    //  ===== Date de début de l'évènement
    echo '<tr><td width="20%">' . $LG_Event_Event_Beg . '</td>';
    echo '<td>';
    zone_date2('dDebAnc', 'dDebAff', 'dDebCache', $dDebLu);
    echo "</td></tr>\n";
    //  ===== Date de fin de l'évènement
    echo '<tr><td width="20%">' . $LG_Event_Event_End . '</td>';
    echo '<td>';
    zone_date2('dFinAnc', 'dFinAff', 'dFinCache', $dFinLu);
    $lib = $LG_Event_Event_Copy_Date;
    echo '  <img src="' . $root . '/assets/img/' . $Icones['copie_calend'] .
        '" alt = "' . $lib . '" title = "' . $lib . '" onclick="copieDate();"/>' . "\n";
    echo "</td></tr>\n";

    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";

    // === Commentaire
    echo '<fieldset>' . "\n";
    echo '<legend>' . ucfirst(LG_CH_COMMENT) . '</legend>' . "\n";
    echo '<table width="95%">' . "\n";
    //Divers
    echo '<tr>' . "\n";
    echo '<td>';
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($refPar, 'E');
    echo '<textarea cols="50" rows="4" name="diversF">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="hidden" name="diversAnc" value="' . my_html($Commentaire) . '"/>';
    echo '</td></tr><tr>';
    // Diffusion Internet commentaire
    echo '<td><label for="diffNoteF">' . LG_CH_COMMENT_VISIBILITY . '</label>'
        . ' <input type="checkbox" id="diffNoteF" name="diffNoteF" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
    echo "/>\n";
    echo '<input type="hidden" name="diffNoteAnc" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";
    echo '</fieldset>' . "\n";
    echo '</div>' . "\n";

    // Données des évènements
    echo '<div id="pane4">' . "\n";

    // Affichage des personnes reliées à l'évènement          // PUF
    if ($objetCibleAffC == 'P') {
        aff_lien_pers($refPar, 'O');

        // Ajout de lien autorisé en mise à jour uniquement
        if ($refPar != -1) {
            echo '<br /><br />Ajouter une personne : ' .
                '<a href="' . $root . '/edition_lier_eve?refPers=-1&amp;refEvt=' . $refPar . '">' .
                '<img src="' . $root . '/assets/img/' . $Icones['ajout'] . '" alt="Ajouter un &eacute;v&egrave;nement"/></a>' . "\n";
        }
    }

    // Affichage des filiations reliées à l'évènement          // PUF
    if ($objetCibleAffC == 'F') {
        aff_lien_filiations($refPar, 'O');
    }

    // Affichage des unions reliées à l'évènement          // PUF
    if ($objetCibleAffC == 'U') {
        aff_lien_unions($refPar, 'O');
    }

    echo '</div>' . "\n";

    // Documents liés à l'évènement
    echo '<div id="panDocs">' . "\n";
    //
    Aff_Documents_Objet($refPar, 'E', 'N');
    // Possibilité de lier un document pour l'évènement
    echo '<br /> Lier un document existant &agrave; l\'&eacute;v&egrave;nement : ' .
        Affiche_Icone_Lien('href="' . $root . '/edition_lier_doc?refObjet=' . $refPar .
            '&amp;typeObjet=E&amp;refDoc=-1"', 'ajout', 'Ajout d\'un document') . "\n";
    echo '</div>' . "\n";

    // Données de la fiche
    echo '<div id="panFiche">' . "\n";
    // Affiche les données propres à l'enregistrement de la fiche
    $x = Affiche_Fiche($enreg, 1);
    //  Sources lies à l'évènement
    if ($modif) {
        echo '<hr/>';
        $x = Aff_Sources_Objet($refPar, 'E', 'N');
        // Possibilité de lier un document pour l'évènement
        echo '<br /> Lier une source existante &agrave; l\'&eacute;v&egrave;nement : ' .
            Affiche_Icone_Lien('href="' . $root . '/edition_lier_source?refObjet=' . $refPar . '&amp;typeObjet=E&amp;refSrc=-1"', 'ajout', 'Ajout d\'une source') . "\n";
    }
    echo '</div>' . "\n";

    echo '</div>' . "\n"; //  <!-- panes -->

    // Affichge des boutons
    $lib_sup = '';
    if (($nbLiens == 0) and ($refPar != -1)) $lib_sup = $lib_Supprimer;
    if ($actualite) $lib_this = $LG_Event_New_This;
    else $lib_this = $LG_Event_Event_This;
    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, $lib_this, false);

    echo '</div>' . "\n";   //  <!-- tab container -->
    echo '</td></tr></table></div>' . "\n";
    echo '</form>' . "\n";
    // include(__DIR__ . '/../public/assets/js/gest_onglets.js');
    echo '<!-- On positionne l\'onglet par défaut -->' . "\n";
    echo '<script type="text/javascript">' . "\n";
    echo '	setupPanes("container1", "tab1", 40);' . "\n";
    echo '</script>' . "\n";

    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    echo $compl;
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
}
?>
</body>

</html>