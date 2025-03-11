<?php
//=====================================================================
// Complétude des personnes par génération avec pourcentage par rapport à la génération précédente
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                // Type d'accès de la page

$tab_variables = array('annuler', 'Horigine', 'Gen_D', 'Gen_F');
foreach ($tab_variables as $nom_variables) {
    if (isset($_POST[$nom_variables])) $$nom_variables = $_POST[$nom_variables];
    else $$nom_variables = '';
}

// Sécurisation des variables postées
$annuler  = Secur_Variable_Post($annuler, strlen($lib_Retour), 'S');
$Horigine = Secur_Variable_Post($Horigine, 100, 'S');

// On retravaille le libellé du bouton pour effectuer le retour...
if ($annuler == $lib_Retour) $annuler = $lib_Annuler;

$Gen_D = Secur_Variable_Post($Gen_D, 1, 'N');
$Gen_F = Secur_Variable_Post($Gen_F, 1, 'N');

$titre = $LG_Menu_Title['Gen_Is_Complete'];    // Titre pour META
$x = Lit_Env();
$niv_requis = 'P';                // Page accessible à partir du niveau privilégié
require(__DIR__ . '/app/ressources/gestion_pages.php');

// Retour sur demande d'annulation
if ($bt_An) Retour_Ar();

// Positionnement des générations de début et de fin par défaut
if ($Gen_D == 0) $Gen_D = 1;
if ($Gen_F == 0) {
    if ($Environnement == 'L') $Gen_F = 20;
    else $Gen_F = 10;
}

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'Stat_Base_Generations', '');

$n_personnes = nom_table('personnes');

$im_moins = '<img src="' . $chemin_images_icones . $Icones['moins'] . '" alt="' . LG_STAT_GEN_DEC . '" title="' . LG_STAT_GEN_DEC . '" border="0" ';
$im_plus = '<img src="' . $chemin_images_icones . $Icones['plus'] . '" alt="' . LG_STAT_GEN_INC . '" title="' . LG_STAT_GEN_INC . '" border="0" ';

echo '<form  id="saisieG" action="' . my_self() . '" method="post">' . "\n";
echo '<table border="0" width="60%" align="center">' . "\n";
echo '<tr align="center">';

echo '<td class="rupt_table">' . my_html(LG_STAT_GEN_FIRST_GEN) . '&nbsp;:&nbsp;' . "\n";
echo $im_moins . 'onclick="if (document.forms.saisieG.Gen_D.value>1) {document.forms.saisieG.Gen_D.value--;}"/>' . "\n";
echo '<input type="text" class="oblig" name="Gen_D" id="Gen_D" value="' . $Gen_D . '" size="3" onchange="verification_num(this);"/>' . "\n";
echo $im_plus . 'onclick="var deb=parseInt(document.forms.saisieG.Gen_D.value); var fin=parseInt(document.forms.saisieG.Gen_F.value); if (deb<fin) {document.forms.saisieG.Gen_D.value++;}"/>&nbsp;' . "\n";
echo '</td>';

echo '<td class="rupt_table">' . my_html(LG_STAT_GEN_LAST_GEN) . '&nbsp;:&nbsp;' . "\n";
echo $im_moins . 'onclick="var deb=parseInt(document.forms.saisieG.Gen_D.value); var fin=parseInt(document.forms.saisieG.Gen_F.value); if ((fin>1) && (fin>deb)) {document.forms.saisieG.Gen_F.value--;}"/>' . "\n";
echo '<input type="text" class="oblig" name="Gen_F" id="Gen_F" value="' . $Gen_F . '" size="3" onchange="verification_num(this);"/>' . "\n";
echo $im_plus . 'onclick="document.forms.saisieG.Gen_F.value++;"/>&nbsp;' . "\n";
echo '</td>';

echo '<td class="rupt_table"><input type="submit" value="' . my_html($LG_display_list) . '"/></td>';

echo '</tr>';
echo '</table>';
echo '</form>' . "\n";

echo '<table width="60%" border="0" class="classic" cellspacing="1" align="center" >' . "\n";
echo '<tr><th>' . LG_STAT_GEN_GEN . '</th>';
echo '<th>' . LG_STAT_GEN_RESULT . '</th>';
echo '<th>' . LG_STAT_GEN_RESULT_RELATED . '</th></tr>' . "\n";

$cpt = 1;

$precNb = -1;
for ($nb_generation = $Gen_D - 1; $nb_generation < $Gen_F; $nb_generation++) {

    $nb_sosa = pow(2, $nb_generation);

    $cpt += 1;
    $bbb = fmod($cpt, 2);
    $style = 'liste';
    $affich_gen = $nb_generation + 1;
    if ($bbb == 0) $style = 'liste2';

    echo '<tr class="' . $style . '" align="center"><td>' . $affich_gen . '</td>';

    $nb_fin = $nb_sosa * 2;
    $nb_debut = $nb_sosa - 1;

    $sql = 'select count(*) from ' . $n_personnes . ' where Numero > ' . $nb_debut . ' AND Numero < ' . $nb_fin .
        ' and length(convert(Numero, unsigned integer)) = length(Numero) ';

    $res = lect_sql($sql);
    $enreg = $res->fetch(PDO::FETCH_NUM);
    $nb_pers = $enreg[0];

    $pourcent_N = $nb_pers / $nb_sosa * 100;
    echo '<td align="left">&nbsp;' . $nb_pers . ' ' . LG_STAT_GEN_ON . ' ' . $nb_sosa . ' ' . LG_STAT_GEN_POSSIBLE . pluriel($nb_sosa) . ' ( ' . sprintf("%01.1f %%", $pourcent_N) . ' )</td>';
    //echo '<td align="left" width="35%">&nbsp;'.$nb_pers.' sur '.$nb_sosa.' possible'.pluriel($nb_sosa).' ( '.sprintf("%01.1f %%",$pourcent_N).' )</td>';
    echo '<td align="left">';
    if ($precNb != -1) {
        $pourcent_P = 0;
        if ($precNb > 0)
            $pourcent_P = $nb_pers / $precNb * 50;
        $nbMissng = $precNb * 2 - $nb_pers;
        echo '&nbsp;' . sprintf("%01.1f %%", $pourcent_P);
        if ($nbMissng) echo '&nbsp;&nbsp;(&nbsp;' . $nbMissng . ' ' . LG_STAT_GEN_PERSONS . pluriel($nbMissng) . ' ' . LG_STAT_GEN_MISSING . pluriel($nbMissng) . ' )';
    }
    $precNb = $nb_pers;
    echo "</td></tr>\n";
}

echo "</table>\n";

$res->closeCursor();

echo '<a href="' . $root . '/liste_pers_gen.php?mq=O">' . my_html($LG_Menu_Title['Pers_Gen']) . '</a>';

// Formulaire pour le bouton retour
Bouton_Retour($lib_Retour, '');

Insere_Bas($compl);
?>
</body>

</html>