<?php
//=====================================================================
// Edition d'un nom de famille
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    'supprimer',
    'nomFam',
    'AnomFam',
    'codePho',
    'AcodePho',
    'divers',
    'Adivers',
    'diffNote',
    'AdiffNote',
    'fusion',
    'anc_ident',
    'ident_courant',
    'Horigine'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
    //echo $nom_variables.' : '.$$nom_variables.'<br>';
}

// Sécurisation des variables postées
$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');
$supprimer = Secur_Variable_Post($supprimer, strlen($lib_Supprimer), 'S');
$Horigine  = Secur_Variable_Post($Horigine, 100, 'S');

// Recup de la variable passée dans l'URL : nom de famille
$idNomFam = Recup_Variable('idNom', 'N');
$Creation = ($idNomFam == -1) ? true : false;

$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
// Titre pour META
if ($Creation) $titre = $LG_Menu_Title['Name_Add'];
else $titre = $LG_Menu_Title['Name_Edit'];
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour arrière sur une demande d'annulation
if ($bt_An) Retour_Ar();

$nomFam        = Secur_Variable_Post($nomFam, 50, 'S');
$AnomFam       = Secur_Variable_Post($AnomFam, 50, 'S');
$codePho       = Secur_Variable_Post($codePho, 50, 'S');
$AcodePho      = Secur_Variable_Post($AcodePho, 50, 'S');
$fusion        = Secur_Variable_Post($fusion, 1, 'S');
$anc_ident     = Secur_Variable_Post($anc_ident, 1, 'N');
$ident_courant = Secur_Variable_Post($ident_courant, 1, 'N');
$divers        = Secur_Variable_Post($divers, 65535, 'S');
$Adivers       = Secur_Variable_Post($Adivers, 65535, 'S');
$diffNote      = Secur_Variable_Post($diffNote, 1, 'S');
$AdiffNote     = Secur_Variable_Post($AdiffNote, 1, 'S');

if ($debug) var_dump($codePho);

// Indicateur pour le nom de famille au format stocké
$stock = false;

// Type d'objet des évènements
$Type_Ref = 'O';

$n_noms_personnes = nom_table('noms_personnes');
$n_personnes = nom_table('personnes');
$n_noms_famille = nom_table('noms_famille');

// Demande de suppression
if ($bt_Sup) {
    // Suppression des commentaires associés
    if ($Adivers != '') {
        $req = req_sup_commentaire($refPar, $Type_Ref);
        $res = maj_sql($req);
    }
    // Suppression du nom
    $req = 'delete from ' . $n_noms_famille . ' where idNomFam = ' . $idNomFam;
    $res = maj_sql($req);
    maj_date_site();
    Retour_Ar();
}

require(__DIR__ . '/app/Phonetique.php');
//	Initialisation d'un objet de la classe
$objetCodePho = new Phonetique();

function celBouton($lettre, $numAide)
{
    echo '<td class="value">';
    if ($lettre != '')
        echo '<div align="center"><input type="button" name="bt' . $lettre . '" value="' . $lettre .
            '" style="width: 40px" onmouseover="afficheAide(' . $numAide .
            ')" onmouseout="afficheAide(-1)" onclick="ajoute(this.value)"/></div>';
    else
        echo '&nbsp;';
    echo '</td>' . "\n";
}

//	Mise à jour
if ($bt_OK) {

    if ($debug) {
        echo 'code phonétique : ';
        var_dump($codePho);
        echo 'ancien code phonétique : ';
        var_dump($AcodePho);
    }

    // Valeur par défaut
    if ($diffNote == '') $diffNote = 'N';

    $req_comment = '';
    $maj_site = false;

    // L'ancien code est reçu transformé et peut générer à tort une différence
    $AcodePho = my_html($AcodePho);
    if (($nomFam != $AnomFam) or ($codePho != $AcodePho)) {
        $sql = '';

        //	Traduction phonétique vers code phonétique ==> pour le format de stockage
        // Les variables reçues sont au format d'affichage
        // Si l'utilisateur n'a pas cliqué sur le bouton calculer, on fait le calcul à sa place + format de stockage
        if ($codePho == '')  $FS_codePho = $objetCodePho->calculer($nomFam);
        // Sinon, on calcule le format de base
        else                 $FS_codePho = $objetCodePho->phonVersCode($codePho);
        // Calcule du format de stockage pour l'ancienne valeur
        if (!$Creation) $FS_AcodePho = $objetCodePho->phonVersCode($AcodePho);
        else {
            if ($fusion == 'N') $FS_AcodePho = $FS_codePho;
            // Pour la création, en cas de fusion, on efface l'ancien code stocké sinon on ne modifiera pas
            // On modifiera peut-être à tort, mais moins gênant que de ne pas modifier alors qu'on le devrait
            else $FS_AcodePho = '';
        }

        //	Cas de mise à jour
        if (!$Creation) {
            // Cas de la fusion, on bascule tout sur le nouveau nom
            if ($fusion == 'O') {
                // Bascule sur l'ancien nom dans la table des noms de personnes
                $sql = 'UPDATE '    . $n_noms_personnes . ' SET idNom =' . $ident_courant . ' WHERE idNom =' . $anc_ident;
                $res = maj_sql($sql);
                // Bascule sur l'ancien nom dans la table des noms de personnes
                $sql = 'UPDATE '    . $n_personnes . ' SET idNomFam =' . $ident_courant . ' WHERE idNomFam =' . $anc_ident;
                $res = maj_sql($sql);
                // Suppression de l'ancien enregistrement dans la table des personnes
                $sql = 'DELETE FROM ' . $n_noms_famille . ' WHERE idNomFam=' . $anc_ident;
                $res = maj_sql($sql);
                $req = req_sup_commentaire($anc_ident, $Type_Ref);
                $res = maj_sql($req);
            }
            // Mise à jour
            $sql = '';
            Aj_Zone_Req('nomFamille', $nomFam, $AnomFam, 'A', $sql);
            Aj_Zone_Req('codePhonetique', $FS_codePho, $FS_AcodePho, 'A', $sql);
            $sql = 'UPDATE ' . $n_noms_famille . ' SET ' . $sql . ' WHERE idNomFam =' . $ident_courant;
            $res = maj_sql($sql);
            // Mise à jour du nom dans la table des personnes
            if ($nomFam != $AnomFam) {
                $Rub = $nomFam;
                $Rub = addslashes($Rub);
                $sql = 'UPDATE ' . $n_personnes . ' SET Nom = \'' . $Rub . '\' WHERE idNomFam = ' . $ident_courant;
                $res = maj_sql($sql);
                $maj_site = true;
            }
            // Traitement des commentaires
            maj_commentaire($ident_courant, $Type_Ref, $divers, $Adivers, $diffNote, $AdiffNote);
        }

        // Cas de création
        else {
            // Pas de fusion, on crée
            if ($fusion == 'N') {
                Ins_Zone_Req($nomFam, 'A', $sql);
                Ins_Zone_Req($FS_codePho, 'A', $sql);
                $sql = 'insert into ' . $n_noms_famille . '(nomFamille,codePhonetique)  values(' . $sql . ')';
                $res = maj_sql($sql);
                $maj_site = true;
                // Création d'un enregistrement dans la table commentaires
                if ($divers != '') {
                    insere_commentaire($connexion->lastInsertId(), $Type_Ref, $divers, $diffNote);
                }
            }
            // Fusion, on met à jour l'ancien enregistrement, uniquement sur le code phonétique
            else {
                // Mise à jour
                Aj_Zone_Req('codePhonetique', $FS_codePho, $FS_AcodePho, 'A', $sql);
                if ($sql != '') {
                    $sql = 'UPDATE ' . $n_noms_famille . ' SET ' . $sql . ' WHERE idNomFam =' . $anc_ident;
                    $res = maj_sql($sql);
                    $maj_site = true;
                }
                // Traitement des commentaires
                maj_commentaire($anc_ident, $Type_Ref, $divers, $Adivers, $diffNote, $AdiffNote);
            }
            // Mise à jour de la date du site
            maj_date_site();
        }
    }

    // Que traitement du commentaire du commentaire
    if (!$Creation) {
        if (($nomFam == $AnomFam) and ($codePho == $AcodePho)) {
            maj_commentaire($idNomFam, $Type_Ref, $divers, $Adivers, $diffNote, $AdiffNote);
        }
    }

    // Exécution de la requête sur les commentaires
    if ($req_comment != '') {
        $res = maj_sql($req_comment);
        $maj_site = true;
    }

    // Mise à jour de la date de modification du site
    if ($maj_site) maj_date_site();

    Retour_Ar();
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {

    include(__DIR__ . '/assets/js/Insert_Tiny.js');
    include(__DIR__ . '/assets/js/Edition_NomFam.js');

    $compl = Ajoute_Page_Info(600, 400);
    if ($idNomFam != -1)
        $compl .= Affiche_Icone_Lien('href="' . $root . '/fiche_nomfam.php?idNom=' . $idNomFam . '"', 'page', LG_NAME_REC) . '&nbsp;';

    Insere_Haut($titre, $compl, 'Edition_NomFam', $idNomFam);

    // Accès aux données du nom
    if (!$Creation) {
        $nomFam = '';
        //
        $sql = 'SELECT * FROM ' . $n_noms_famille . ' WHERE idNomFam =' . $idNomFam . ' limit 1';
        $res = lect_sql($sql);
        if ($res->RowCount() > 0) {
            $row = $res->fetch(PDO::FETCH_NUM);
            $nomFam  = $row[1];
            $codePho = $objetCodePho->codeVersPhon($row[2]);
        }

        // Le nom est-il utilisé ? Si oui, on ne pourra pas le supprimer
        $sql = 'select 1 from ' . $n_noms_personnes . ' where idNom =' . $idNomFam . ' limit 1';
        $resN = lect_sql($sql);
        $utilise = ($enregN = $resN->fetch(PDO::FETCH_NUM));
        $resN->closeCursor();
    }

    $larg_titre = '20';
    $ent_table = '<table width="60%" class="table_form" align="center">' . "\n";

    echo '<form id="saisie" method="post" onsubmit="return verification_form_nomFam(this,\'nomFam\')" action="' . my_self() . '?idNom=' . $idNomFam . '" >' . "\n";
    echo '<input type="' . $hidden . '" name="ident_courant" id="ident_courant" value="' . $idNomFam . '"/>' . "\n";
    echo $ent_table;

    //	Zone de saisie du nom
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . LG_NAME . '&nbsp;</td><td class="value">';
    echo '<input type="text" size="50" name="nomFam" id="nomFam" value="' . $nomFam . '" class="oblig"/>&nbsp;' . "\n";
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    echo '&nbsp;<img id="majuscule" src="' . $root . '/assets/img/' . $Icones['majuscule'] . '" alt="' . LG_NAME_TO_UPCASE . '" title="' . LG_NAME_TO_UPCASE . '"' .
        ' onclick="NomMaj();document.getElementById(\'nomFam\').focus();"/>' . "\n";
    echo '<input type="' . $hidden . '" name="AnomFam" value="' . $nomFam . '"/></td></tr>' . "\n";
    //	Code phonétique
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . LG_NAME_PRONUNCIATION . '&nbsp;</td><td class="value">';
    echo '<span id="code" style="border-width: 1px; font-size: 15pt; border-style: solid; border-color: #000000;padding: 1pt 4pt;">&nbsp;</span>&nbsp;' . "\n";
    echo '<input type="' . $hidden . '" name="codePho" id="codePho" value="' . $codePho . '"/>' . "\n";
    echo '<input type="' . $hidden . '" name="AcodePho" value="' . $codePho . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";

    echo $ent_table;
    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    //echo '<p><table border="1" cellpadding="3" cellspacing="0">' . "\n";
    echo '<tr><td rowspan="2" valign="middle" width="' . $larg_titre . '" class="label">' . my_html(LG_NAME_VOWELS) . '</td>' . "\n";
    celBouton('a', 0);
    celBouton('&acirc;', 1);
    celBouton('e', 2);
    celBouton('&eacute;', 3);
    celBouton('&egrave;', 4);
    celBouton('i', 5);
    celBouton('o', 6);
    celBouton('&ocirc;', 7);
    celBouton('u', 8);
    echo '</tr><tr>' . "\n";
    celBouton('eu', 9);
    celBouton('en', 10);
    celBouton('on', 11);
    celBouton('ou', 12);
    celBouton('in', 13);
    celBouton('un', 14);
    celBouton('', -1);
    celBouton('', -1);
    celBouton('', -1);
    echo '</tr> <tr>' . "\n" . '<td rowspan="2" valign="middle" class="label">' . my_html(LG_NAME_CONSONANTS) . '</td>' . "\n";
    celBouton('b', 15);
    celBouton('d', 16);
    celBouton('f', 17);
    celBouton('g', 18);
    celBouton('j', 19);
    celBouton('k', 20);
    celBouton('l', 21);
    celBouton('m', 22);
    celBouton('n', 23);
    echo '</tr><tr>' . "\n";
    celBouton('p', 24);
    celBouton('r', 25);
    celBouton('s', 26);
    celBouton('t', 27);
    celBouton('v', 28);
    celBouton('z', 29);
    celBouton('', -1);
    celBouton('ch', 30);
    celBouton('gn', 31);
    echo '</tr>';
    echo '<tr><td class="label" width="20%">&nbsp;' . ucfirst(LG_NAME_SAMPLE) . '&nbsp;</td>';
    echo '<td colspan="10" align="center" class="value"><div id="aide"></div></td></tr>' . "\n";
    echo '<tr class="value"><td colspan="10" align="center">' . "\n";
    echo '<input type="button" name="proposer" value="' . my_html(LG_NAME_PRONUNCIATION_CALC) . '" onclick="javascript:calculer();"/>' . "\n";
    echo '&nbsp; &nbsp; &nbsp;' . "\n";
    echo '<input type="button" name="espace" value="' . my_html(LG_NAME_SPACE) . '" onclick="javascript:ajoute(\' \');"/>' . "\n";
    echo '&nbsp; &nbsp; &nbsp;' . "\n";
    echo '<input type="button" name="effacer" value="' . my_html(LG_NAME_BACKSPACE) . '" onclick="efface()"/>' . "\n";
    echo '&nbsp; &nbsp; &nbsp;' . "\n";
    echo '<input type="button" name="effacer" value="<--" onclick="cursGauche()"/>' . "\n";
    echo '&nbsp; &nbsp; &nbsp;' . "\n";
    echo '<input type="button" name="effacer" value="-->" onclick="cursDroite()"/>' . "\n";
    //echo '</td></tr></table>' . "\n";
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";
    //	Mise en place du code correspondant au nom
    if (!$Creation) {
        if ($res->RowCount() > 0) {
            echo '<script type = "text/javascript">' . "\n";
            echo 'traiteCodeRecu(\'' . $codePho . '\');' . "\n";
            echo '</script>' . "\n";
        }
    }
    echo '<input type="' . $hidden . '" name="Horigine" value="' . $Horigine . '"/>' . "\n";
    // La zone suivante sera valorisée à 'O' en cas de demande de fusion ; la zone suivante contiendra l'identifiant du nom
    echo '<input type="' . $hidden . '" name="fusion" id= "fusion" value="N"/>' . "\n";
    echo '<input type="' . $hidden . '" name="anc_ident" id="anc_ident" value="0"/>' . "\n";

    // === Commentaires
    echo $ent_table;
    echo '<tr><td colspan="2">&nbsp;</td></tr>';
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . LG_CH_COMMENT . '&nbsp;</td><td class="value">';
    // Accès au commentaire
    $Existe_Commentaire = Rech_Commentaire($idNomFam, $Type_Ref);
    echo '<textarea cols="50" rows="4" name="divers">' . $Commentaire . '</textarea>' . "\n";
    echo '<input type="' . $hidden . '" name="Adivers" value="' . my_html($Commentaire) . '"/>';
    echo '</td></tr>';
    // Diffusion Internet commentaire
    echo '<tr><td class="label" width="' . $larg_titre . '%">&nbsp;' . LG_CH_COMMENT_VISIBILITY . '&nbsp;</td><td class="value">';
    echo '<input type="checkbox" name="diffNote" value="O"';
    if ($Diffusion_Commentaire_Internet == 'O') echo ' checked="checked"';
    echo "/>\n";
    echo '<input type="' . $hidden . '" name="AdiffNote" value="' . $Diffusion_Commentaire_Internet . '"/>' . "\n";
    echo '</td></tr>' . "\n";
    echo '</table>' . "\n";

    echo $ent_table;
    //ligne_vide_tab_form(1);
    echo '<tr align="center"><td>';
    $lib_sup = '';
    if ((!$Creation) and (! $utilise)) $lib_sup = $lib_Supprimer;
    bt_ok_an_sup($lib_Okay, $lib_Annuler, $lib_sup, 'ce nom de famille', false);
    echo '</td></tr>';
    echo '</table>' . "\n";

    echo '</form>' . "\n";

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