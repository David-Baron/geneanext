<?php

//=====================================================================
// Accès unitaire MatchId pour récupérer la date de décès éventuel d'une personne
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$ref = Recup_Variable('ref', 'N');

$acces = 'L';                                // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['MatchId_Sch'];
$x = Lit_Env();
// On ne chargera pas la page dans la liste des pages afin de ne pas rompre le fil des pages d'appel
$not_memo = false;
require(__DIR__ . '/../app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 250);
if ($bt_OK) Ecrit_Entete_Page($titre, $contenu, $mots);
Insere_Haut($titre, $compl, 'Recherche_MatchId_Unitaire', '');

// Récupération des informations de la personne
$sql = 'SELECT Nom, Prenoms, Sexe, Ne_le, Nom_Ville'
    . ' FROM ' . nom_table('personnes') . ', ' . nom_table('villes')
    . ' WHERE Reference = ' . $ref
    . ' AND Identifiant_zone = Ville_Naissance'
    . ' LIMIT 1';
$res = lect_sql($sql);
if ($enreg = $res->fetch(PDO::FETCH_ASSOC)) {

    $d_nai_id = '';
    if (strlen($enreg['Ne_le']) == 10)
        $d_nai_id = substr($enreg['Ne_le'], 6, 2) . '%2F' . substr($enreg['Ne_le'], 4, 2) . '%2F' . substr($enreg['Ne_le'], 0, 4);

    echo '<br>';
    echo '<table width="70%" class="table_form" align="center">' . "\n";
    echo '<tr><td class="label" width="30%">' . ucfirst($LG_Name) . '</td><td class="value">' . $enreg['Nom'] . '</td></tr>';
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_FIRST_NAME) . '</td><td class="value">' . $enreg['Prenoms'] . '</td></tr>';
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_SEXE) . '</td><td class="value">';
    switch ($enreg['Sexe']) {
        case 'm':
            echo LG_SEXE_MAN;
            break;
        case 'f':
            echo LG_SEXE_WOMAN;
            break;
        default:
            echo '?';
    }
    echo '</td></tr>' . "\n";
    echo '<tr><td class="label" width="30%">' . ucfirst(lib_sexe_born($enreg['Sexe'])) . '</td><td class="value">' . Etend_date($enreg['Ne_le']) . '</td></tr>';
    echo '<tr><td class="label" width="30%">' . ucfirst(LG_AT) . '</td><td class="value">' . $enreg['Nom_Ville'] . '</td></tr>';
    echo '</table>';

    // Construction pour l'appel de l'API
    $url = $url_matchid_sch
        . '?firstName=' . UnPrenom($enreg['Prenoms'])
        . '&lastName=' . $enreg['Nom']
        . '&sex=' . strtoupper($enreg['Sexe'])
        . '&birthDate=' . $d_nai_id;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json'));
    curl_setopt($ch, CURLOPT_USERAGENT, 'Php Curl Genemania');

    $json = curl_exec($ch);
    $erreur = curl_error($ch);
    $affiche = false;

    if ($erreur != '') {
        echo '<br>' . LG_SCH_MATCH_ERROR . ' ' . $erreur . '<br>';
        $erreur = strtoupper($erreur);
        if (strpos($erreur, 'COULD NOT RESOLVE HOST') !== false)
            echo '<img src="' . $root . '/assets/img/' . $Icones['tip'] . '" alt="Information" title="Information"> ' . LG_SCH_MATCH_NO_INTERNET;
    } else {
        $affiche = true;
        echo '<h2>' . LG_SCH_MATCH_ANSWER . '</h2><br>';
        $sortie = json_decode($json, true);
        curl_close($ch);

        $nb_pers = count($sortie['response']['persons']);
        for ($nb = 0; $nb < $nb_pers; $nb++) {
            $num_pers = $nb + 1;
            echo LG_SCH_MATCH_PERS . ' ' . $num_pers . '<br>';
            echo $LG_Name . ' ' . $sortie['response']['persons'][$nb]['name']['last'] . '<br>';;
            echo LG_FIRST_NAME . ' ';
            $nb_prenoms = count($sortie['response']['persons'][$nb]['name']['first']);
            for ($nb_p = 0; $nb_p < $nb_prenoms; $nb_p++) {
                echo $sortie['response']['persons'][$nb]['name']['first'][$nb_p] . ' ';
            }
            $sex = strtolower($sortie['response']['persons'][$nb]['sex']);
            $d_nai = $sortie['response']['persons'][$nb]['birth']['date'];
            echo '<br>' . ucfirst(lib_sexe_born($sex)) . ' ' . Etend_date($d_nai . 'GL') . ' ' . LG_AT . ' ';
            affiche_lieu($sortie['response']['persons'][$nb]['birth']['location']);
            $d_dec = $sortie['response']['persons'][$nb]['death']['date'];
            $date_deces = retourne_date($d_dec);
            echo '<br>' . ucfirst(lib_sexe_dead($sex)) . ' ' . Etend_date($d_dec . 'GL') . ' (' . $date_deces . ') ' . LG_AT . ' ';
            affiche_lieu($sortie['response']['persons'][$nb]['death']['location']);
            echo ' <img src="' . $root . '/assets/img/' . $Icones['copie_calend'] . '" alt="' . LG_SCH_MATCH_COPY_DATE . '" title="' . LG_SCH_MATCH_COPY_DATE . '" onclick="copyTextToClipboard(' . $date_deces .');">';
            echo '<hr>';
        }

        echo ' <img src="' . $root . '/assets/img/' . $Icones['oeil'] . '" alt="le json" title="le json" ' . Survole_Clic_Div('div_json') . '/>';
        echo ' ' . LG_SCH_MATCH_SHOW_JSON;
        echo '<div id="div_json">';
        echo $json . '<br>';
        echo '</div>';
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

// Reformatte une date retourné par MatchId ; format initial AAAAMMJJ
function retourne_date($la_date)
{
    if (strlen($la_date) == 8) {
        return substr($la_date, 6, 2) . '/' . substr($la_date, 4, 2) . '/' . substr($la_date, 0, 4);
    } else
        return $la_date;
}

// Affiche un lieu tel que retourné par MatchId
function affiche_lieu($tableau)
{
    if (is_array($tableau['city'])) {
        $nb_villes = count($tableau['city']);
        for ($nb = 0; $nb < $nb_villes; $nb++)
            echo ' ' . $tableau['city'][$nb];
    } else
        echo $tableau['city'];
    if (array_key_exists('codePostal', $tableau)) {
        $nb_cp = count($tableau['codePostal']);
        for ($nb = 0; $nb < $nb_cp; $nb++)
            echo ' ' . $tableau['codePostal'][$nb];
    }
    echo ' ' . $tableau['country'];
}

// On masque par défaut le json résultat
if ($affiche) {
?>
    <script type="text/javascript">
        cache_div("div_json");
        // On aurait pu utiliser navigator.clipboard, mais l'API ne fonctionne qu'en HTTPS :-(
        function copyTextToClipboard(text) {
            // On crée un textarea à la volée pour copier le texte
            var textArea = document.createElement("textarea");
            //
            // *** This styling is an extra step which is likely not required. ***
            //
            // Why is it here? To ensure:
            // 1. the element is able to have focus and selection.
            // 2. if element was to flash render it has minimal visual impact.
            // 3. less flakyness with selection and copying which **might** occur if
            //    the textarea element is not visible.
            //
            // The likelihood is the element won't even render, not even a flash,
            // so some of these are just precautions. However in IE the element
            // is visible whilst the popup box asking the user for permission for
            // the web page to copy to the clipboard.
            //
            // Place in top-left corner of screen regardless of scroll position.
            textArea.style.position = 'fixed';
            textArea.style.top = 0;
            textArea.style.left = 0;
            // Ensure it has a small width and height. Setting to 1px / 1em
            // doesn't work as this gives a negative w/h on some browsers.
            textArea.style.width = '2em';
            textArea.style.height = '2em';
            // We don't need padding, reducing the size if it does flash render.
            textArea.style.padding = 0;
            // Clean up any borders.
            textArea.style.border = 'none';
            textArea.style.outline = 'none';
            textArea.style.boxShadow = 'none';
            // Avoid flash of white box if rendered for any reason.
            textArea.style.background = 'transparent';
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';
                console.log('Copying text command was ' + msg);
            } catch (err) {
                console.log('Oops, unable to copy');
            }
            document.body.removeChild(textArea);
        }
    </script>
<?php
}
?>
</body>

</html>