<?php
//=====================================================================
// Fiche d'un nom de famille
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Name'];       // Titre pour META

$tab_variables = array('annuler', 'Horigine');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}
// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$contenu = 'Codage phonétique'; // Mots clés supplémentaires

// Recup de la variable passée dans l'URL : identifiant du nom de famille
$idNomFam = Recup_Variable('idNom', 'N');
$req_sel = 'SELECT * FROM ' . nom_table('noms_famille') . ' WHERE idNomFam =' . $idNomFam . ' limit 1';

$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/../app/ressources/gestion_pages.php');

    if ((!$enreg_sel) or ($idNomFam == -1)) Retour_Ar();

    else {

        require(__DIR__ . '/../app/Util/Phonetique.php');
        $codePho = new Phonetique();

        $compl = Ajoute_Page_Info(600, 150);
        if (IS_GRANTED('G')) {
            $compl = Affiche_Icone_Lien('href="' . $root . '/edition_nomfam?idNom=' . $idNomFam . '"', 'fiche_edition', $LG_Menu_Title['Name_Edit']) . '&nbsp;';
        }
        Insere_Haut($titre, $compl, 'Fiche_NomFam', $idNomFam);

        if ($idNomFam > -1) {
            $row = $enreg_sel;

            $r_nom = $row['nomFamille'];

            echo '<br />';
            echo '<table width="70%" class="table_form">' . "\n";
            echo '<tr><td class="label" width="25%"> ' . ucfirst(LG_NAME) . ' </td>';
            echo '<td class="value">' . $r_nom . '</td></tr>' . "\n";
            echo '<tr><td class="label" width="25%"> ' . ucfirst(LG_NAME_PHONETIC) . ' </td>';
            echo '<td class="value">' . $codePho->codeVersPhon($row['codePhonetique']) . '</td></tr>' . "\n";
            echo '</table>';

            //  ===== Affichage du commentaire
            if (Rech_Commentaire($idNomFam, 'O')) {
                if (($Commentaire != '') and (IS_GRANTED('P') or ($Diffusion_Commentaire_Internet == 'O'))) {
                    echo '<fieldset><legend>Note</legend>' . my_html($Commentaire) . '</fieldset><br>' . "\n";
                }
            }

            $sql = 'select 1 from ' . nom_table('noms_personnes') . ' where idNom = ' . $idNomFam . ' limit 1';
            $res = lect_sql($sql);
            $utilise = ($enreg = $res->fetch(PDO::FETCH_ASSOC));
            $res->closeCursor();
            if ($utilise) {
                $deb_lien = '<a href="' . $root . '/liste_pers2?Type_Liste=';
                $fin_lien = '&amp;idNom=' . $idNomFam . '&amp;Nom=' . $r_nom . '">';
                echo '<br />' . $deb_lien . 'P' . $fin_lien . LG_LPERS_OBJ_P . ' ' . $r_nom . '</a>';
                echo '<br />' . $deb_lien . 'p' . $fin_lien . LG_LPERS_OBJ_PC . ' ' . $r_nom . '</a>';
                echo '<br />' . "\n";
                if ((!$SiteGratuit) or ($Premium))
                    if (IS_GRANTED('C'))
                        echo '<br /><a href="' . $root . '/completude_nom?idNom=' . $idNomFam . '&amp;Nom=' . $r_nom . '">' . my_html($LG_Menu_Title['Name_Is_Complete']) . $r_nom . '</a><br />' . "\n";
            }

            // Recherche du nom sur les sites gratuits ; pas sur les sites gratuits non premium
            if ((!$SiteGratuit) or ($Premium)) {
                if ($r_nom != '') {
                    echo '<br /><a href="' . $adr_rech_gratuits . '?ok=ok&amp;NomP=' . $r_nom . '" target="_blank">' . my_html(LG_NAME_SEARCH) . '</a>' . "\n";
                }
            }

        }
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