<?php

//=====================================================================
// Ajout contribution
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

// Récupération des variables de l'affichage précédent
$tab_variables = array(
    'ok',
    'annuler',
    // variables pour le père
    'Nompere',
    'Prenomspere',
    'Ne_lepere',
    'CNe_lepere',
    'idNeZonepere',
    'Nepere',
    'Decede_lepere',
    'CDecede_lepere',
    'idDecedeZonepere',
    'Decedepere',
    // variables pour la mère
    'Nommere',
    'Prenomsmere',
    'Ne_lemere',
    'CNe_lemere',
    'idNeZonemere',
    'Nemere',
    'Decede_lemere',
    'CDecede_lemere',
    'idDecedeZonemere',
    'Decedemere',
    // variables pour le conjoint
    'Nomconj',
    'Prenomsconj',
    'Ne_leconj',
    'CNe_leconj',
    'idNeZoneconj',
    'Neconj',
    'Decede_leconj',
    'CDecede_leconj',
    'idDecedeZoneconj',
    'Decedeconj',
    // variables pour l'enfant 1
    'Nomenfant1',
    'Prenomsenfant1',
    'Ne_leenfant1',
    'CNe_leenfant1',
    'idNeZoneenfant1',
    'Neenfant1',
    'Decede_leenfant1',
    'CDecede_leenfant1',
    'idDecedeZoneenfant1',
    'Decedeenfant1',
    // variables pour  l'enfant 2
    'Nomenfant2',
    'Prenomsenfant2',
    'Ne_leenfant2',
    'CNe_leenfant2',
    'idNeZoneenfant2',
    'Neenfant2',
    'Decede_leenfant2',
    'CDecede_leenfant2',
    'idDecedeZoneenfant2',
    'Decedeenfant2',
    'mail',
    'message',
    'Horigine'
);

foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) {
        $$nom_variables = $_POST[$nom_variables];
        // Sécurisation des variables réceptionnées
        if (strpos($nom_variables, 'Nom')        === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
        if (strpos($nom_variables, 'Prenoms')    === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
        if (strpos($nom_variables, 'Ne_le')      === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
        if (strpos($nom_variables, 'Decede_le')  === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 10, 'S');
        if (strpos($nom_variables, 'NeZone')     === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
        if (strpos($nom_variables, 'DecedeZone') === 0) $$nom_variables = Secur_Variable_Post($$nom_variables, 50, 'S');
    } else $$nom_variables = '';
}
$Nepere        = Secur_Variable_Post($Nepere, 50, 'S');
$Decedepere    = Secur_Variable_Post($Decedepere, 50, 'S');
$Nemere        = Secur_Variable_Post($Nemere, 50, 'S');
$Decedemere    = Secur_Variable_Post($Decedemere, 50, 'S');
$Neconj        = Secur_Variable_Post($Neconj, 50, 'S');
$Decedeconj    = Secur_Variable_Post($Decedeconj, 50, 'S');
$Neenfant1     = Secur_Variable_Post($Neenfant1, 50, 'S');
$Decedeenfant1 = Secur_Variable_Post($Decedeenfant1, 50, 'S');
$Neenfant2     = Secur_Variable_Post($Neenfant2, 50, 'S');
$Decedeenfant2 = Secur_Variable_Post($Decedeenfant2, 50, 'S');

$ok        = Secur_Variable_Post($ok, strlen($lib_Okay), 'S');
$annuler   = Secur_Variable_Post($annuler, strlen($lib_Annuler), 'S');

$mail          = Secur_Variable_Post($mail, 80, 'S');
$message       = Secur_Variable_Post($message, 200, 'S');
$Horigine      = Secur_Variable_Post($Horigine, 100, 'S');

// Gestion standard des pages
$acces = 'M';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = 'Contribution';               // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
$niv_requis = 'I';                        // Les contributions sont ouvertes à tout le monde
$index_follow = 'NN';                    // NOINDEX NOFOLLOW demandé pour les moteurs
require(__DIR__ . '/../app/ressources/gestion_pages.php');          // Appel de la gestion standard des pages


// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Recup de la variable passée dans l'URL : référence de la personne
$Refer = Recup_Variable('Refer', 'N');

$entetePage = 'Ajout de contribution';
$larg_col = 17;

$at = '  ' . my_html(LG_AT) . ' &nbsp';

function ligne_contrib($ligne)
{
    global $fp, $cr, $Verif_Contenu, $Contenu;
    fwrite($fp, $ligne . $cr);
    // Présence d'un contenu dans la ligne ? On teste le dernier caractère de la ligne
    if (($Verif_Contenu) and (substr($ligne, 0, 1) != '#')) {
        $ligne2 = rtrim($ligne);
        if (substr($ligne2, strlen($ligne2) - 1, 1) != ':') $Contenu = true;
    }
}

function Aff_Pers($suffixe, $oblig)
{
    global $root, $enregP, $Icones, $Nom, $larg_col, $at;
    if (!$oblig) $style_z_oblig2 = '';
    else $style_z_oblig2 = ' class = "oblig" ';
    if (($suffixe == 'pere') or ($suffixe == 'mere')) {
        if ($suffixe == 'pere') $val = 'm';
        else                    $val = 'f';
        echo '<input type="hidden" name="Sexe' . $suffixe . '" value="' . $val . '"/>';
    }
    // En fonction du suffixe, accord sur le genre pour les libellés de colonnes
    switch ($suffixe) {
        case 'pere':
            $accord = '';
            $sexe_lib = 'm';
            break;
        case 'mere':
            $accord = 'e';
            $sexe_lib = 'f';
            break;
        default:
            $accord = '(e)';
            $sexe_lib = '';
    }
    echo '<table>';
    echo '<tr><td width="' . $larg_col . '%">' . LG_PERS_NAME . '</td>';
    echo '<td><input type="text" size="50" name="Nom' . $suffixe . '" id="Nom' . $suffixe . '" value="" ' . $style_z_oblig2 . '/> ';
    if ($oblig) {
        echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    }

    // Proposition du nom de la personne sauf pour le conjoint
    if ($suffixe != 'conj') {
        echo '<img id="copier_' . $suffixe . '" src="' . $root . '/assets/img/' . $Icones['copier'] . '" alt="' . LG_CONTRIBS_COPY_REF_NAME . '" title="' . LG_CONTRIBS_COPY_REF_NAME . '"' .
            ' onclick="document.getElementById(\'Nom' . $suffixe . '\').value = \'' . $Nom . '\'"/>';
    }

    echo '</td></tr>';
    echo '<tr><td width="' . $larg_col . '%">' . LG_PERS_FIRST_NAME . '</td>';
    echo '<td><input type="text" size="50" name="Prenoms' . $suffixe . '" value="" ' . $style_z_oblig2 . '/> ';
    if ($oblig) {
        echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/>';
    }
    echo '</td></tr>';
    if (($suffixe != 'pere') && ($suffixe != 'mere')) {
        echo '<tr><td width="' . $larg_col . '%">Sexe</td>';
        echo '<td><input type="radio" name="Sexe' . $suffixe . '" value="m"/>' . LG_SEXE_MAN . ' ';
        echo '    <input type="radio" name="Sexe' . $suffixe . '" value="f"/>' . LG_SEXE_WOMAN;
        echo '</td></tr>';
    }
    echo '<tr><td width="' . $larg_col . '%">' . ucfirst(lib_sexe_born($sexe_lib)) . '</td>';
    // col_titre_tab_noClass('Né'.$accord,$larg_col);
    // echo '<td><input type="text" readonly="readonly" size="25" name="Ne_le'.$suffixe.'" value=""/>'."\n";
    // Affiche_Calendrier('imgCalendN'.$suffixe,'Calendrier_Naissance(\''.$suffixe.'\')');
    // echo '<input type="hidden" name="CNe_le'.$suffixe.'" value=""/>'."\n";
    echo '<td colspan="2">';
    zone_date2('ANe_le' . $suffixe, 'Ne_le' . $suffixe, 'CNe_le' . $suffixe, '');
    echo '<input type="hidden" name="idNeZone' . $suffixe . '" value=""/>';
    echo $at . '<input type="text" readonly="readonly" name="Ne' . $suffixe . '" value=""/>';
    echo '<img src="' . $root . '/assets/img/' . $Icones['localisation'] . '" alt="Sélection ville" onclick="Appelle_Zone_Naissance(\'' . $suffixe . '\')"/>';
    echo '</td></tr>';
    echo '<tr><td width="' . $larg_col . '%">' . ucfirst(lib_sexe_dead($sexe_lib)) . '</td>';
    // echo '<td><input type="text" readonly="readonly" size="25" name="Decede_le'.$suffixe.'" value=""/>'."\n";
    // Affiche_Calendrier('imgCalendD'.$suffixe,'Calendrier_Deces(\''.$suffixe.'\')');
    // echo '<input type="hidden" name="CDecede_le'.$suffixe.'" value=""/>'."\n";
    echo '<td colspan="2">';
    zone_date2('ADecede_le' . $suffixe, 'Decede_le' . $suffixe, 'CDecede_le' . $suffixe, '');
    echo '<input type="hidden" name="idDecedeZone' . $suffixe . '" value=""/>';
    echo $at . '<input type="text" readonly="readonly" name="Decede' . $suffixe . '" value=""/>';
    echo '<img src="' . $root . '/assets/img/' . $Icones['localisation'] . '" alt="Sélection ville" onclick="Appelle_Zone_Deces(\'' . $suffixe . '\')"/>';
    echo '</td></tr>';
    echo '</table>';
}

// Affiche les données du formulaire
function Aff_Donnees($Refer)
{
    global $root, $chemin_images, $Comportement, $Icones, $Images, $style_z_oblig, $larg_col, $lib_Okay, $lib_Annuler;

    echo '<div id="content">';
    echo '<table id="cols"  border="0" cellpadding="0" cellspacing="0" >';
    echo '<tr>';
    echo '<td style="border-right:0px solid #9cb0bb">';
    echo '  <img src="' . $chemin_images . $Images['clear'] . '" width="520" height="1" alt="clear"/>';
    echo '</td></tr>';
    echo '<tr>';
    echo '<td class="left">';
    echo '<div class="tab-container" id="container1">';
    // Onglets
    echo '<ul class="tabs">';
    echo '<li><a href="#" onclick="return showPane(\'pnlParents\', this)" id="tab1">' . ucfirst(LG_PARENTS) . '</a></li>';
    echo '<li><a href="#" onclick="return showPane(\'pnlConjoint\', this)">' . ucfirst(LG_HUSB_WIFE) . '</a></li>';
    echo '<li><a href="#" onclick="return showPane(\'pnlEnfants\', this)">' . LG_CONTRIBS_CHILDREN . '</a></li>';
    // Captcha pour autoriser le OK
    echo '<li><a href="#" onclick="return showPane(\'pnlUnlock\', this)">' . LG_CONTRIBS_UNLOCK . ' ' . $lib_Okay . '</a></li>';
    echo '</ul>';

    echo '<div class="tab-panes">';
    // Onglet parents
    echo '<div id="pnlParents">';
    echo '  <fieldset>';
    echo '<legend>' . LG_FATHER . '</legend>' . "\n";
    Aff_Pers('pere', 0);
    echo '  </fieldset>';
    echo '  <fieldset>';
    echo '<legend>' . LG_MOTHER . '</legend>' . "\n";
    Aff_Pers('mere', 0);
    echo '  </fieldset>';
    echo '</div>';

    // Onglet conjoint
    echo '<div id="pnlConjoint">';
    Aff_Pers('conj', 0);
    echo '</div>';

    // Onglet enfants
    echo '<div id="pnlEnfants">';
    echo '<fieldset>';
    echo '<legend>' . LG_CONTRIBS_CHILD_1 . '</legend>' . "\n";
    Aff_Pers('enfant1', 0);
    echo '</fieldset>';
    echo '<fieldset>';
    echo '<legend>' . LG_CONTRIBS_CHILD_2 . '</legend>' . "\n";
    Aff_Pers('enfant2', 0);
    echo '</fieldset>';
    echo '</div>';

    // Onglet déverrouillage bouton OK
    echo '<div id="pnlUnlock">';
    echo '<table width="100%">';
    echo '<tr>';
    echo '<td>' . LG_CONTRIBS_UNLOCK_TIP1 . '<br />' . LG_CONTRIBS_UNLOCK_TIP2 . '</td>';
    echo '<td>';
    echo '<table>';
    echo '<tr>';
    echo '<td valign="middle"><input name="captcha" type="text" id="captcha" size="6"';
    echo ' onchange="if (this.value != \'\') document.getElementById(\'bouton_ok\').style.visibility = \'visible\';"/> </td>';
    echo '<td valign="top" align="center"><img style="border: 1px dashed #0064A4;" src="captcha_image_gen.php" alt="captcha"/>';
    echo '<br /><i><a href="http://software.patrick-b.fr/fr/scripts/php/spam-captcha.php" target="blank">' . LG_CONTRIBS_TRIBUTE . '</a></i></td>';
    echo '</tr>';
    echo '</table>';
    echo '</td>';
    echo '</tr>';
    echo '</table><br /><br />';
    echo '<table width="100%">';
    echo '<tr><td width="' . $larg_col . '%">' . LG_CONTRIBS_EMAIL . '</td>';
    echo '<td><input type="text" size="50" name="mail" id="mail" value="" class="oblig"/> ';
    echo '<img src="' . $root . '/assets/img/' . $Icones['obligatoire'] . '" alt="Zone obligatoire" title="Zone obligatoire"/></td>';
    echo '</tr>';
    echo '<tr><td width="' . $larg_col . '%">' . LG_CONTRIBS_MESSAGE . '</td>';
    echo '<td><textarea cols="50" rows="4" name="message"></textarea></td>';
    echo '</tr>';
    echo '</table>';
    echo '<br /><br />';
    echo '<i>' . LG_CONTRIBS_IP_RECORD . '</i>';
    echo '</div>';

    echo '</div> <!-- panes -->';

    bt_ok_an_sup($lib_Okay, $lib_Annuler, '', '', false);

    echo '</div> <!-- tab container -->';
    echo '</td></tr></table></div>';
}

//Demande de mise à jour ==> création si vérification OK du captcha
if ($bt_OK) {

    // Le code est OK, on peut stocker
    if ((isset($_SESSION['verifkey']) == TRUE) && (isset($_POST['captcha']) == TRUE) && ($_SESSION['verifkey'] == $_POST['captcha'])) {

        // Positionnement d'une contribution ene base

        // Initialisation
        $rubs = '';
        $cont = '';

        Ins_Zone_Req_Rub($Refer, 'N', 'Reference_Personne');
        Ins_Zone_Req_Rub($mail, 'A', 'Mail');
        Ins_Zone_Req_Rub('E', 'A', 'Statut');                                // 'E' : en cours de création
        Ins_Zone_Req_Rub(getenv("REMOTE_ADDR"), 'A', 'Adresse_IP');

        $req = 'insert into ' . nom_table('contributions') .
            ' (' . $rubs . ',Date_Creation,Date_Modification) values' .
            ' (' . $cont . ',current_timestamp,current_timestamp)';
        // Exécution de la requête
        $res = maj_sql($req);

        // Récupération du numéro de la dernière contribution que l'on pad sur 6 caractères à gauche pour éviter les problèmes de tri sur la liste
        $num_contrib = $connexion->lastInsertId();
        $num_contrib = str_pad($num_contrib, 6, '0', STR_PAD_LEFT);


        $nom_fic_base = 'contrib_' . $num_contrib . '.txt';
        $nom_fic = $chemin_contributions . $nom_fic_base;

        // Le fichier de contribution aura le numéro attribué
        if ($fp = fopen($nom_fic, 'w+')) {

            // Récupération de la date
            $temps = time();
            $jour = date('d', $temps);  //format numerique : 01->31
            $annee = date('Y', $temps); //format numerique : 4 chiffres
            $mois = date('m', $temps);
            $heure = date('H', $temps);
            $minutes = date('i', $temps);
            $date = "$jour/$mois/$annee à $heure h $minutes";

            // Top de vérification du contenu pour savoir si on doit envoyer le mail
            $Verif_Contenu = false;
            $Contenu = false;

            ligne_contrib('# contribution ' . $num_contrib);
            ligne_contrib('# ' . $date);
            ligne_contrib('# version Génémania : ' . $Version);
            ligne_contrib('# IP serveur : ' . $_SERVER['SERVER_ADDR']);
            ligne_contrib('# Nom serveur : ' . $_SERVER['SERVER_NAME']);
            ligne_contrib('# User agent : ' . $_SERVER['HTTP_USER_AGENT']);
            ligne_contrib('# IP utilisateur : ' . $_SERVER['REMOTE_ADDR']);
            ligne_contrib('# Mail : ' . $mail);

            // Modification du message ==> on retire les retours chariots
            $message = str_replace("\n", ' ', $message);
            ligne_contrib('# Message : ' . $message);
            ligne_contrib('Reference_Personne : ' . $Refer);

            if ($SiteGratuit) {
                $script = my_self();
                $pos = strpos($script, '/', 2);
                $sous_site = substr($script, 1, $pos - 1);
                ligne_contrib('# Site : ' . $sous_site);
            }

            $Verif_Contenu = true;

            ligne_contrib('# père');
            ligne_contrib('Nompere : ' . $Nompere);
            ligne_contrib('Prenomspere : ' . $Prenomspere);
            ligne_contrib('Ne_lepere : ' . $CNe_lepere);
            ligne_contrib('NeZonepere : ' . $idNeZonepere);
            ligne_contrib('Nepere : ' . $Nepere);
            ligne_contrib('Decede_lepere : ' . $CDecede_lepere);
            ligne_contrib('DecedeZonepere : ' . $idDecedeZonepere);
            ligne_contrib('Decedepere : ' . $Decedepere);

            ligne_contrib('# mère');
            ligne_contrib('Nommere : ' . $Nommere);
            ligne_contrib('Prenomsmere : ' . $Prenomsmere);
            ligne_contrib('Ne_lemere : ' . $CNe_lemere);
            ligne_contrib('NeZonemere : ' . $idNeZonemere);
            ligne_contrib('Nemere : ' . $Nemere);
            ligne_contrib('Decede_lemere : ' . $CDecede_lemere);
            ligne_contrib('DecedeZonemere : ' . $idDecedeZonemere);
            ligne_contrib('Decedemere : ' . $Decedemere);

            ligne_contrib('# conjoint');
            ligne_contrib('Nomconj : ' . $Nomconj);
            ligne_contrib('Prenomsconj : ' . $Prenomsconj);
            ligne_contrib('Ne_leconj : ' . $CNe_leconj);
            ligne_contrib('NeZoneconj : ' . $idNeZoneconj);
            ligne_contrib('Neconj : ' . $Neconj);
            ligne_contrib('Decede_leconj : ' . $CDecede_leconj);
            ligne_contrib('DecedeZoneconj : ' . $idDecedeZoneconj);
            ligne_contrib('Decedeconj : ' . $Decedeconj);

            ligne_contrib('# enfant 1');
            ligne_contrib('Nomenfant1 : ' . $Nomenfant1);
            ligne_contrib('Prenomsenfant1 : ' . $Prenomsenfant1);
            ligne_contrib('Ne_leenfant1 : ' . $CNe_leenfant1);
            ligne_contrib('NeZoneenfant1 : ' . $idNeZoneenfant1);
            ligne_contrib('Neenfant1 : ' . $Neenfant1);
            ligne_contrib('Decede_leenfant1 : ' . $CDecede_leenfant1);
            ligne_contrib('DecedeZoneenfant1 : ' . $idDecedeZoneenfant1);
            ligne_contrib('Decedeenfant1 : ' . $Decedeenfant1);

            ligne_contrib('# enfant 2');
            ligne_contrib('Nomenfant2 : ' . $Nomenfant2);
            ligne_contrib('Prenomsenfant2 : ' . $Prenomsenfant2);
            ligne_contrib('Ne_leenfant2 : ' . $CNe_leenfant2);
            ligne_contrib('NeZoneenfant2 : ' . $idNeZoneenfant2);
            ligne_contrib('Neenfant2 : ' . $Neenfant2);
            ligne_contrib('Decede_leenfant2 : ' . $CDecede_leenfant2);
            ligne_contrib('DecedeZoneenfant2 : ' . $idDecedeZoneenfant2);
            ligne_contrib('Decedeenfant2 : ' . $Decedeenfant2);

            // On ne met à jour la base et on envoye le mail que s'il y a un contenu dans la contribution
            if ($Contenu) {
                // On met à jour la contribution en base pour dire que l'on a créé le fichier
                $req = 'update ' . nom_table('contributions') . ' set Statut=\'F\' where Contribution =' . $num_contrib;
                // Exécution de la requête
                $res = maj_sql($req);

                $expediteur = $mail;

                $ajout = '';
                if ($SiteGratuit) $ajout = '# Site : ' . $sous_site;

                $message_texte = '# contribution ' . $num_contrib .
                    '# ' . $date .
                    '# version Génémania ' . $Version .
                    '# IP serveur : ' . $_SERVER['SERVER_ADDR'] .
                    '# Nom serveur : ' . $_SERVER['SERVER_NAME'] .
                    $ajout .
                    '# User agent : ' . $_SERVER['HTTP_USER_AGENT'] .
                    '# IP utilisateur : ' . $_SERVER['REMOTE_ADDR'] .
                    '# Mail : ' . $mail .
                    '# Message : ' . $message .
                    '# Reference_Personne : ' . $Refer;
                $destinataire = $Adresse_Mail;
                $sujet = 'Ajout de contribution Geneamania';

                // Inspiré de http://www.toutestfacile.com/php/cours/mail_2.php5

                //----------------------------------
                // Construction de l'entête
                //----------------------------------

                // Frontière aleatoire
                $boundary = "-----=" . md5(uniqid(rand()));
                // Entête
                if ($SiteGratuit) $de = 'contribs@geneamania.net';
                else $de = $destinataire; // Le destinataire et l'émetteur du mail sont le gestionnaire du site
                $header   = "MIME-Version: 1.0\r\n";
                $header .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";
                $header .= "From: " . $de . "\r\n";
                $header .= "Return-Path: " . $de . "\r\n";
                $header .= "Reply-To: " . $de . "\r\n";
                if ($SiteGratuit) $header .= "X-Sender: <www.geneamania.net>\r\n";
                $header .= "X-Mailer: PHP/" . phpversion() . "\r\n";
                $header .= "X-originating-IP: " . $_SERVER['REMOTE_ADDR'] . "\r\n";
                $header .= "X-Priority: 3 (Normal)\r\n";
                $header .= "\r\n";

                //--------------------------------------------------
                // Construction du message proprement dit
                //--------------------------------------------------

                // Pour le cas, où le logiciel de mail du destinataire n'est pas capable de lire le format MIME de cette version
                // Il est de bon ton de l'en informer
                // REM: Ce message n'apparaît pas pour les logiciels sachant lire ce format
                $msg = "Je vous informe que ceci est un message au format MIME 1.0 multipart/mixed.\r\n";

                //---------------------------------
                // 1ère partie du message
                // Le texte
                //---------------------------------
                // Chaque partie du message est séparé par une frontière
                $msg .= "--$boundary\r\n";

                // Et pour chaque partie on en indique le type
                $msg .= "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
                // Et comment il sera codé
                $msg .= "Content-Transfer-Encoding:8bit\r\n";
                // Il est indispensable d'introduire une ligne vide entre l'entête et le texte
                $msg .= "\r\n";
                // Enfin, on peut écrire le texte de la 1ère partie
                $msg .= $message_texte . "\r\n";
                $msg .= "\r\n";

                //---------------------------------
                // 2nde partie du message
                // Le fichier
                //---------------------------------
                // Tout d'abord lire le contenu du fichier
                $file = $nom_fic;
                $fp = fopen($file, "r");
                $attachment = fread($fp, filesize($file));
                fclose($fp);
                // puis convertir le contenu du fichier en une chaîne de caractère
                // certe totalement illisible mais sans caractères exotiques
                // et avec des retours à la ligne tout les 76 caractères
                // pour être conforme au format RFC 2045
                $attachment = chunk_split(base64_encode($attachment));

                // Ne pas oublier que chaque partie du message est séparé par une frontière
                $msg .= "--$boundary\r\n";
                // Et pour chaque partie on en indique le type
                $msg .= "Content-Type: text/plain; name=\"$file\"\r\n";
                // Et comment il sera codé
                $msg .= "Content-Transfer-Encoding: base64\r\n";
                $msg .= "Content-Disposition:attachment; filename=\"$nom_fic_base\"\r\n";
                // Il est indispensable d'introduire une ligne vide entre l'entête et le texte
                $msg .= "\r\n";
                // C'est ici que l'on insère le code du fichier lu
                $msg .= $attachment . "\r\n";
                $msg .= "\r\n\r\n";

                // voilà, on indique la fin par une nouvelle frontière
                $msg .= "--$boundary--\r\n";

                $reponse = $expediteur;
                if (mail($destinataire, $sujet, $msg, "Reply-to: $reponse\r\nFrom: $expediteur\r\n" . $header)) {
                    // Retour arrière
                    Retour_Ar();
                } else {
                    echo '<center><font color="red"><br><br><br><h2>' . LG_CONTRIBS_SEND_KO . '</h2></font></center>';
                }
            } else {
                echo '<center><font color="red"><br><br><br><h2>' . LG_CONTRIBS_EMPTY . '</h2></font></center>';
            }
        } else {
            echo '<center><font color="red"><br><br><br><h2>' . LG_CONTRIBS_FILE_KO . '</h2></font></center>';
        }
    } else {
        echo '<center><font color="red"><br><br><br><h2>' . LG_CONTRIBS_CTRL_KO . '</h2></font></center>';
    }
}

// Première entrée : affichage pour saisie
if ((!$bt_OK) && (!$bt_An)) {
    // include(__DIR__ . '/assets/js/Ajout_Contribution.js');
    $compl = '';
    if (Get_Nom_Prenoms($Refer, $Nom, $Prenoms)) {
        if (!$est_privilegie and $Diff_Internet_P != 'O') {
            Insere_Haut($entetePage . ' pour ?', $compl, 'Ajout_Contribution', $Refer);
            echo '<center><font color="red"><br><br><br><h2>Données non disponibles pour votre profil</h2></font></center><br />';
            echo '</body></html>';
            return;
        } else {
            $compl = Ajoute_Page_Info(600, 200);
            Insere_Haut($entetePage . ' pour ' . $Prenoms . ' ' . $Nom, $compl, 'Ajout_Contribution', $Refer);
            echo '<br>';
            echo '<form id="saisie" method="post" onsubmit="return verification_form(this,\'mail\')" action="' . $root . '/ajout_contribution.php?Refer=' . $Refer . '" >';
            echo '<input type="hidden" name="Refer" value="' . $Refer . '"/>';
            echo '<input type="hidden" name="Horigine" value="' . my_html($Horigine) . '"/>' . "\n";
            Aff_Donnees($Refer); // Affichage des données
            echo '</form>';
            include(__DIR__ . '/assets/js/gest_onglets.js'); ?>

            <script type="text/javascript">
                // On positionne l'onglet par défaut
                document.getElementById("bouton_ok").style.visibility = "hidden";
                setupPanes("container1", "tab1", 0);
            </script>
<?php }
    }
    echo '<table cellpadding="0" width="100%">';
    echo '<tr>';
    echo '<td align="right">';
    if ($compl != '') {
        echo $compl;
    }
    echo '<a href="' . $root . '/"><img src="' . $root . '/assets/img/house.png" alt="Accueil" title="Accueil" /></a>';
    echo "</td>";
    echo '</tr>';
    echo '</table>';
} else {
    echo "<body bgcolor=\"#FFFFFF\">";
}

?>
<script type="text/javascript">
    // Appel de la popup de sélection de ville
    function Appelle_Zone_Naissance(cible) {
        x = Zone_Geo('Ne' + cible, 'idNeZone' + cible, document.getElementsByName('idNeZone' + cible).value, 4);
    }

    function Appelle_Zone_Bapteme(cible) {
        x = Zone_Geo('Baptise' + cible, 'idBaptiseZone' + cible, document.getElementsByName('idBaptiseZone' + cible).value, 4);
    }

    function Appelle_Zone_Deces(cible) {
        x = Zone_Geo('Decede' + cible, 'idDecedeZone' + cible, document.getElementsByName('idDecedeZone' + cible).value, 4);
    }

    function Appelle_Zone_Union(cible) {
        x = Zone_Geo('Union' + cible, 'idUnionZone' + cible, document.getElementsByName('idUnionZone' + cible).value, 4);
    }

    // Ouverture d'une PopUp de saisie de zone géographique
    function Zone_Geo(zoneLib, zoneValue, valZone, valNiveau) {
        var h = 200;
        var w = 430;
        var chParam = "resizable=no, location=no, menubar=no, directories=no, scrollbars=no, status=no, ";
        PopupCentrer('sel_zone_geo.php?zoneLib=' + zoneLib + '&zoneValue=' + zoneValue + '&valZone=' + valZone + '&valNiveau=' + valNiveau, w, h, chParam);
    }
</script>
</body>

</html>