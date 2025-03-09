<?php
//=====================================================================
// Fusion de villes
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'cible',
    'sel_ville'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
    // echo $nom_variables.' : '.$$nom_variables.'<br>';
}

// Sécurisation des variables postées
$ok       = Secur_Variable_Post($ok, strlen(LG_CH_FUSIONNER), 'S');
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');

// On retravaille le libellé du bouton pour être standard...
if ($ok == LG_CH_FUSIONNER) $ok = 'OK';

$acces = 'M';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Town_Merging'];
$niv_requis = 'C';

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$x = Lit_Env();
require(__DIR__ . '/Gestion_Pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

else {

    $Ident = Recup_Variable('Ident', 'N');    // Identifiant de la ville à fusionner

    if ($debug) {
        var_dump($Ident);
        var_dump($sel_ville);
        var_dump($cible);
    }

    if ($bt_OK) {
        Ecrit_Entete_Page($titre, $contenu, $mots);
        include(__DIR__ . '/assets/js/monSSG.js');
    }

    $compl = Ajoute_Page_Info(600, 150);
    Insere_Haut($titre, $compl, 'Fiche_Homonymes', '');

    // Demande de fusion
    if ($bt_OK) {

        // v_result : ville résultat de la fusion ; v_fusion : ville à fusionner, donc qui va disparaitre

        if ($cible == 'D') {
            $v_result = $sel_ville;
            $v_fusion = $Ident;
        } else {
            $v_result = $Ident;
            $v_fusion = $sel_ville;
        }

        // Traitement particulier des commentaires
        // S'il y a un commentaire, on regarde sur l'autre ville pour une fusion éventuelle
        // Si l'un des commentaires est non diffusible la cible le sera aussi
        $req_comment = '';
        $Existe_Commentaire = Rech_Commentaire($v_fusion, 'V');
        // var_dump($Existe_Commentaire);
        if ($Existe_Commentaire) {
            $Commentaire_Fusion = $Commentaire;
            $Diffusion_Commentaire_Internet_Fusion = $Diffusion_Commentaire_Internet;
            $Existe_Commentaire = Rech_Commentaire($v_result, 'V');
            if ($Existe_Commentaire) {
                $Commentaire_Result = $Commentaire_Fusion . '<br>' . $Commentaire;
                if (($Diffusion_Commentaire_Internet_Fusion == 'N') or ($Diffusion_Commentaire_Internet == 'N'))
                    $Diffusion_Commentaire_Internet = 'N';
                // Mise à jour du commentaire
                Aj_Zone_Req('Note', $Commentaire_Result, '', 'A', $req_comment);
                Aj_Zone_Req('Diff_Internet_Note', $Diffusion_Commentaire_Internet_Fusion, '', 'A', $req_comment);
                $req_comment = 'update ' . nom_table('commentaires') . ' set ' . $req_comment .
                    ' where Reference_Objet = ' . $v_result .
                    " and Type_Objet = 'V'";
            } else
                insere_commentaire($v_result, 'V', $Commentaire_Fusion, $Diffusion_Commentaire_Internet_Fusion, true);
            // var_dump($req_comment);
        }
        if ($req_comment != '')
            $res = maj_sql($req_comment);


        $sql_upd = 'UPDATE ' . nom_table('concerne_doc') . ' SET Reference_Objet = ' . $v_result . ' WHERE Reference_Objet = ' . $v_fusion . " and Type_Objet = 'V'";
        $res = maj_sql($sql_upd);
        $sql_upd = 'UPDATE ' . nom_table('concerne_objet') . ' SET Reference_Objet = ' . $v_result . ' WHERE Reference_Objet = ' . $v_fusion . " and Type_Objet = 'V'";
        $res = maj_sql($sql_upd);
        $sql_upd = 'UPDATE ' . nom_table('concerne_source') . ' SET Reference_Objet = ' . $v_result . ' WHERE Reference_Objet = ' . $v_fusion . " and Type_Objet = 'V'";
        $res = maj_sql($sql_upd);
        $sql_upd = 'UPDATE ' . nom_table('evenements') . ' SET Identifiant_zone = ' . $v_result . ' WHERE Identifiant_zone = ' . $v_fusion . " and Identifiant_Niveau = 4";
        $res = maj_sql($sql_upd);
        $sql_upd = 'UPDATE ' . nom_table('participe') . ' SET Identifiant_zone = ' . $v_result . ' WHERE Identifiant_zone = ' . $v_fusion . " and Identifiant_Niveau = 4";
        $res = maj_sql($sql_upd);
        $sql_upd = 'UPDATE ' . nom_table('personnes') . ' SET Ville_Naissance = ' . $v_result . ' WHERE Ville_Naissance = ' . $v_fusion;
        $res = maj_sql($sql_upd);
        $sql_upd = 'UPDATE ' . nom_table('personnes') . ' SET Ville_Deces = ' . $v_result . ' WHERE Ville_Deces = ' . $v_fusion;
        $res = maj_sql($sql_upd);
        $sql_upd = 'UPDATE ' . nom_table('subdivisions') . ' SET Zone_Mere = ' . $v_result . ' WHERE Zone_Mere = ' . $v_fusion;
        $res = maj_sql($sql_upd);
        $sql_upd = 'UPDATE ' . nom_table('unions') . ' SET Ville_Mariage = ' . $v_result . ' WHERE Ville_Mariage = ' . $v_fusion;
        $res = maj_sql($sql_upd);
        $sql_upd = 'UPDATE ' . nom_table('unions') . ' SET Ville_Notaire = ' . $v_result . ' WHERE Ville_Notaire = ' . $v_fusion;
        $res = maj_sql($sql_upd);
        $sql_upd = 'DELETE FROM ' . nom_table('villes') . ' WHERE Identifiant_zone = ' . $v_fusion;
        $res = maj_sql($sql_upd);

        $maj_site = true;
        if ($maj_site) maj_date_site();
    }

    echo '<form id="saisie" method="post" action="' . my_self() . '?' . Query_Str() . '">' . "\n";

    $ref1 = 0;
    $ref2 = 0;

    $csp = ' colspan="3"';
    $num_ligne = 0;

    echo '<br>';
    echo '<table width="85%" align="center" border="0" class="classic"  >' . "\n";

    echo '<tbody>';
    echo '<tr>';
    echo '<td class="rupt_table" >&nbsp;</td>';
    echo '<td class="rupt_table" width="40%">' . LG_CH_FUSION_TOWN1 . '</td>';
    echo '<td class="rupt_table" width="40%">' . LG_CH_FUSION_TOWN2 . '</td>';
    echo '</tr>';
    echo '</tbody>';

    include(__DIR__ . '/assets/js/Fusion_Villes.js');

    $n_ville = '';
    $cp = '';
    $dep = '';
    $Lat_V = '';
    $Long_V = '';
    $nom_villes = nom_table('villes');
    $nom_departements = nom_table('departements');
    $req_sel = 'SELECT v.*, d.Nom_Depart_Min FROM ' . $nom_villes . ' v, ' . $nom_departements . ' d' .
        ' WHERE v.Identifiant_zone = ' . $Ident .
        ' AND d.Identifiant_zone = v.Zone_Mere limit 1';
    if ($res = lect_sql($req_sel)) {
        if ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {
            // var_dump($enreg);
            $n_ville = $enreg['Nom_Ville'];
            $cp = $enreg['Code_Postal'];
            $dep = $enreg['Nom_Depart_Min'];
            $Lat_V = $enreg['Latitude'];
            $Long_V = $enreg['Longitude'];
            /*
			'Identifiant_zone' => int 108
			'Nom_Ville' => string 'Avremesnil' (length=10)
			'Code_Postal' => string '' (length=0)
			'Date_Creation' => string '2024-08-27 08:55:21' (length=19)
			'Date_Modification' => string '2024-08-27 08:55:21' (length=19)
			'Statut_Fiche' => string 'N' (length=1)
			'Zone_Mere' => int 0
			'Latitude' => float 0
			'Longitude' => float 0
			'Nom_Depart_Min' => string '' (length=0)
			*/
        }
    }

    echo '<tr><td>' . LG_ICSV_TOWN_NAME . '<td>' . $n_ville . '</td>';
    // Récup de la liste des villes
    echo '<td><select name="sel_ville" id="sel_ville" onchange="updateVille(this.value)">' . "\n";
    $req_sel = 'SELECT v.Identifiant_zone, v.Nom_Ville, v.Code_Postal, d.Nom_Depart_Min'
        . ' FROM ' . $nom_villes . ' v, ' . $nom_departements . ' d'
        . ' WHERE v.Identifiant_zone <> 0'
        . ' AND v.Identifiant_zone <> ' . $Ident
        . ' AND d.Identifiant_zone = v.Zone_Mere'
        . ' ORDER by v.Nom_Ville';
    var_dump($req_sel);
    if ($res_lv = lect_sql($req_sel)) {
        while ($row = $res_lv->fetch(PDO::FETCH_NUM)) {
            echo '<option value="' . $row[0] . '">' . $row[1] . '/' . $row[2] . '/' . $row[3] . '</option>' . "\n";
        }
    }
    echo "</select></td>\n";
    $ro = ' readonly = "readonly"';
    echo '<tr>';
    echo '<tr><td>' . LG_ICSV_TOWN_ZIP_CODE . '<td>' . $cp . '</td><td><input type="text" id="cp" value="-" ' . $ro . '></td>' . '<tr>';
    echo '<tr><td>' . LG_COUNTY . '<td>' . $dep . '</td><td><input type="text" id="dep" value="-" ' . $ro . '></td>' . '<tr>';
    echo '<tr><td>' . LG_ICSV_TOWN_ZIP_LATITUDE . '<td>' . $Lat_V . '</td><td><input type="text" id="Lat_V" value="-" ' . $ro . '></td>' . '<tr>';
    echo '<tr><td>' . LG_ICSV_TOWN_ZIP_LONGITUDE . '<td>' . $Long_V . '</td><td><input type="text" id="Long_V" value="-" ' . $ro . '></td>' . '<tr>';
    $deb_rad = '<input type="radio" name="cible" value="';
    echo '<tr><td>' . LG_CH_FUSION_TARGET . '<td>' . $deb_rad . 'G" checked="checked"/>' . '</td><td>' . $deb_rad . 'D" />' . '</td>' . '<tr>';


    echo '</table>';

    /*
	echo Affiche_Icone('tip',LG_TIP)
		.'&nbsp;'.LG_CH_FUSION_TIP1
		.'<br>'.LG_CH_FUSION_TIP2
		.'<br>'.LG_CH_FUSION_TIP3
		.'<br>'.LG_CH_FUSION_TIP4
		;
	*/

    echo '<br>' . "\n";

    // Formulaire pour le bouton retour
    //Bouton_Retour($lib_Retour,'?'.Query_Str());
    bt_ok_an_sup(LG_CH_FUSIONNER, $lib_Retour, '', '', false, false);

    Insere_Bas('');
}


?>
</body>

</html>