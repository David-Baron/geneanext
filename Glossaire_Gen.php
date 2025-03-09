<?php

//=====================================================================
// Glossaire généalogique
//=====================================================================

session_start();

// Gestion standard des pages
include('fonctions.php');
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Glossary'];       // Titre pour META
$x = Lit_Env();
include('Gestion_Pages.php');

// Entête sur changement d'initiale
function Lettre($lettre)
{
    global $chemin_images_icones, $Icones, $Comportement, $num_lig;
    $num_lig = 0;
    echo '</table>';
    if ($lettre != 'A') echo '</div>';
    echo '<table width="95%" border="0" cellspacing="1" cellpadding="3" align="center">';
    echo '<tr><td align="center" colspan="2" class="rupt_table">' . "\n";
    echo '<b><a name="' . $lettre . '"></a>' . $lettre . '</b>' . "\n";
    echo '&nbsp;&nbsp;' . Affiche_Icone_Lien('href="#top"', 'page_haut', 'Haut de page');
    echo '&nbsp;&nbsp;<img id="ajout' . $lettre . '" src="' . $chemin_images_icones . $Icones['oeil'] . '" alt="Fl&egrave;che"' . Survole_Clic_Div('div' . $lettre) . '/>' . "\n";
    echo '</td></tr></table>' . "\n";
    echo '<div id="div' . $lettre . '"><table width="95%" border="0" cellspacing="1" cellpadding="3" align="center">' . "\n";
    return 0;
}

// Affiche la ligne avec le mot
function Mot($mot, $Ancre = '', $premier = false)
{
    global $num_lig;
    if (!$premier) echo '</tr>';
    if (pair($num_lig++)) $style = 'liste';
    else                $style = 'liste2';
    echo '<tr class="' . $style . '">' . "\n";
    echo '<td width="15%">';
    if ($Ancre != '') echo '<a name="' . $Ancre . '"></a>';
    echo '<b>' . my_html($mot) . '</b></td>' . "\n";
}

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'glossaire_gen', '');

$ent_table = '<table border="0" width="95%" cellspacing="1" cellpadding="3" align="center">' . "\n";
$ent_table = '<table border="0" width="95%" cellspacing="1" align="center">' . "\n";

$exclus_liens = 'JKWXYZ';
$alpha = '';
for ($nb = ord('A'); $nb <= ord('Z'); $nb++) {
    $car = chr($nb);
    if (strpos($exclus_liens, $car) === false) {
        $contenu = '<a ';
        if ($car == 'A') $contenu .= ' id="top" ';
        $contenu .= 'href="#' . $car . '">' . $car . '</a>';
    } else                                       $contenu = $car;
    $alpha .= '<td class="rupt_table" align="center">' . $contenu . '</td>';
}

echo $ent_table . '<tr>' .
    '<td colspan="26" align="center">' . Affiche_Icone('tip', 'Conseil') . '&nbsp;' . LG_GLOSS_TIP_1 . ' <font color="blue">' . LG_GLOSS_TIP_2 . '</font>, '
    . LG_GLOSS_TIP_3 . ' <img src="' . $chemin_images_icones . $Icones['loupe'] . '" border="0" alt="' . LG_GLOSS_MORE_INFO
    . '"/> ' . LG_GLOSS_TIP_4
    . '</td></tr><tr>' . $alpha . '</tr><tr><td colspan="26">&nbsp;</td></tr>' . "\n";

$x = Lettre('A');

$x = Mot(LG_GLOSS_ACT, '', true); ?>
<td>
    &Eacute;crit constatant un fait (acte d'&eacute;tat civil) ou enregistrement une d&eacute;claration.
</td>
<?php $x = Mot(LG_GLOSS_ADULTERINE); ?>
<td>
    Enfant n&eacute; de relations hors mariage.
</td>
<?php $x = Mot(LG_GLOSS_AGNATIC, 'AG'); ?>
<td>
    Ascendant ou descendant par les hommes. Une g&eacute;n&eacute;alogie
    est agnatique lorsqu'elle ne prend en compte que les hommes. Voir
    aussi <a href="#CO">Cognat</a>.
</td>
<?php $x = Mot(LG_GLOSS_GRAND_FA_MOTHER); ?>
<td>
    D&eacute;signe le grand-p&egrave;re ou la grand-m&egrave;re.
</td>
<?php $x = Mot(LG_GLOSS_ANCESTORS); ?>
<td>
    Au pluriel, d&eacute;signe l'ensemble des anc&ecirc;tres.
</td>
<?php $x = Mot(LG_GLOSS_ELDER); ?>
<td>
    Celui qui est n&eacute; le premier. Voir aussi <a href="#BE">Benjamin</a>
    ou <a href="#PU">Pu&icirc;n&eacute;</a>.
</td>
<?php $x = Mot(LG_GLOSS_FIRSTBORN); ?>
<td>
    Priorit&eacute; d'&acirc;ge, principalement entre les enfants m&acirc;les d'une famille noble.<br />
    Droit d'a&icirc;nesse : droit qu'avait l'a&icirc;n&eacute; de prendre dans la succession des parents, une plus grande part que les autres enfants.
</td>
<?php $x = Mot(LG_GLOSS_RELATED); ?>
<td>
    Alli&eacute; par le mariage.
</td>
<?php $x = Mot(LG_GLOSS_ARMORIAL); ?>
<td>
    Recueil d'armoiries.
</td>
<?php $x = Mot(LG_GLOSS_ARMS, 'AR'); ?>
<td>
    Toujours au pluriel, d&eacute;signe l'ensemble de l'&eacute;cu et de ses ornements.
</td>
<?php $x = Mot(LG_GLOSS_REG_ITEM); ?>
<td>
    Unit&eacute; de classement d'archives (registres, pi&egrave;ces, ...).
</td>
<?php $x = Mot(LG_GLOSS_ANCESTOR); ?>
<td>
    Un anc&ecirc;tre direct
</td>
<?php $x = Mot(LG_GLOSS_COMMON_ANCESTOR); ?>
<td>
    Anc&ecirc;tre commun &agrave; plusieurs branches. On utilise souvent ce terme pour la noblesse.
</td>
</tr>
<?php $x = Lettre('B');
$x = Mot(LG_GLOSS_BANNS, '', true); ?>
<td>
    Proclamations publiques.
</td>
<?php $x = Mot(LG_GLOSS_BAPTISM); ?>
<td>
    Sacrement qui marque l'entr&eacute;e d'un enfant (ou d'un adulte) dans la vie chr&eacute;tienne.
</td>
<?php $x = Mot(LG_GLOSS_YOUNGEST); ?>
<td>
    Le plus jeune enfant de la famille. Le dernier n&eacute;. Voir
    aussi <a href="#PU">Pu&icirc;n&eacute;</a> ou <a href="#AI">A&icirc;n&eacute;</a>.
</td>
<?php $x = Mot(LG_GLOSS_GREAT_GRANDPARENT); ?>
<td>
    P&egrave;re, m&egrave;re de l'a&iuml;eul (arri&egrave;re grand-p&egrave;re, arri&egrave;re-grand-m&egrave;re).
</td>
<?php $x = Mot(LG_GLOSS_BLAZON); ?>
<td>
    Ensemble des pi&egrave;ces qui constituent un &eacute;cu h&eacute;raldique.
</td>
<?php $x = Mot(LG_GLOSS_BWB, 'BMS'); ?>
<td>
    Cette abr&eacute;viation d&eacute;signe les <a href="#RE">registres
        paroissiaux</a> dans lesquels les Bapt&ecirc;mes, Mariages et S&eacute;pultures &eacute;taient indiqu&eacute;s.
</td>
<?php $x = Mot(LG_GLOSS_BRANCH); ?>
<td>
    Partie d'un arbre g&eacute;n&eacute;alogique. Exemples : branche paternelle, branche cadette...
</td>
</tr>
<?php $x = Lettre('C');
$x = Mot(LG_GLOSS_CA, '', true); ?>
<td>
    Abr&eacute;viation du latin Circa qui signifie environ. Ainsi ca 1800 signifie vers 1800.
</td>
<?php $x = Mot(LG_GLOSS_LAND_REGISTRY); ?>
<td>
    Registre public sur lequel on indiquait la surface et la valeur
    des biens fonciers en vue de la perception de l'imp&ocirc;t.
</td>
<?php $x = Mot(LG_GLOSS_CADET); ?>
<td>
    N&eacute; en second. On parle aussi de la <a href="#BR">branche</a> cadette d'une famille.
</td>
<?php $x = Mot(LG_GLOSS_POLL_TAX); ?>
<td>
    Imp&ocirc;t, taxe par t&ecirc;te. La capitation fut &eacute;tablie &agrave; la fin de 1695.
</td>
<?php $x = Mot(LG_GLOSS_CARTULARY); ?>
<td>
    Ensemble de documents, chartes, contrats, actes de foi et d'hommage,
    donations..., d'une famille ou d'une institution comme une abbaye.
</td>
<?php $x = Mot(LG_GLOSS_CHARTER); ?>
<td>
    Document qui concerne les biens et les titres d'une famille ou
    d'une institution et prouvant ses droits. Le chartrier est l'ensemble
    des chartes de la famille ou de l'institution.
</td>
<?php $x = Mot(LG_GLOSS_BOURG_RECENS); ?>
<td>
    En Bourgogne, ce sont des &eacute;tats nominatifs des chefs de
    famille &eacute;tablis en vue de l'assiette d'impositions directes.
</td>
<?php $x = Mot(LG_GLOSS_COGNATIC, 'CO'); ?>
<td>
    Ascendant ou descendant par les femmes. Une g&eacute;n&eacute;alogie
    est cognatique lorsqu'elle ne prend en compte que les femmes.
    Voir aussi <a href="#AG">Agnat</a>.
</td>
<?php $x = Mot(LG_GLOSS_COLLATERAL); ?>
<td>
    Parent descendant d'un anc&ecirc;tre commun. Les collat&eacute;raux
    sont issus d'une autre <a href="#BR">branche</a>. Un cousin est
    par exemple un collat&eacute;ral.
</td>
<?php $x = Mot(LG_GLOSS_CONSANGUINEOUS, 'CONS'); ?>
<td>
    Parent du c&ocirc;t&eacute; paternel. Deux enfants issus d'un
    m&ecirc;me p&egrave;re mais de m&egrave;res diff&eacute;rentes
    sont dits fr&egrave;res consanguins. Voir aussi <a href="#UT">Ut&eacute;rin</a>.
</td>
<?php $x = Mot(LG_GLOSS_CONSANGUINITY); ?>
<td>
    Parent&eacute; proche entre deux conjoints. Voir dispense de consanguinit&eacute;.
</td>
<?php $x = Mot(LG_GLOSS_CONSCRIPTION); ?>
<td>
    Tous les ans, les jeunes gens ayant atteint l'&acirc;ge d'effectuer
    leur service militaire national s'inscrivent sur les r&ocirc;les militaires.
</td>
<?php $x = Mot(LG_GLOSS_REFERENCE); ?>
<td>
    Marque alphab&eacute;tique et /ou num&eacute;rique servant &agrave;
    classer des liasses d'archives ou des ouvrages de biblioth&egrave;ques.
</td>
<?php $x = Mot(LG_GLOSS_CURATORSHIP, 'CURAT'); ?>
<td>
    R&eacute;gime de protection des incapables majeurs plus souple que la <a href="#TUT">tutelle</a>, leur permettant
    d'accomplir certains actes administratifs.
</td>
</tr>
<?php $x = Lettre('D');
$x = Mot(LG_GLOSS_DECUJUS, 'CUJUS', 1); ?>
<td>
    La personne dont on &eacute;tablit la g&eacute;n&eacute;alogie.
    En latin : &quot;<i>de cujus successione agitur&quot; </i> ou
    &quot;<i>de cujus boni agitur&quot;</i>, ce qui signifie &quot;<i>de la succession de qui il s'agit</i>&quot;.
    Sert &agrave; d&eacute;signer par extension le point de d&eacute;part d'une g&eacute;n&eacute;alogie.
    Le de cujus porte le N&deg; <a href="#SOSA">Sosa</a>1.
</td>
<?php $x = Mot(LG_GLOSS_DEG_RELATIONSHIP); ?>
<td>
    Nombre permettant de mesurer la parent&eacute; entre deux membres
    d'une m&ecirc;me famille. Le degr&eacute; de parent&eacute; ne
    se mesure pas de la m&ecirc;me mani&egrave;re en droit civil et
    en droit canon. Voir aussi<a href="#dispense"> Dispenses de consanguinit&eacute;</a>.
</td>
<?php $x = Mot(LG_GLOSS_DESCENDANTS); ?>
<td>
    Ensemble des personnes issues d'un individu ou d'un couple (enfants, petits-enfants, ...).
</td>
<?php $x = Mot(LG_GLOSS_INBREEDING_EXEMPTIONS, 'dispenseC'); ?>
<td>
    Lorsque les &eacute;poux &eacute;taient parents, ils devaient demander une dispense de consanguinit&eacute; avant de pouvoir
    se marier. Voir aussi <a href="#IMP">Implexe</a>
</td>
<?php $x = Mot(LG_GLOSS_AFFINITY_EXEMPTIONS, 'dispenseA'); ?>
<td>
    Lors des mariages, elles &eacute;taient n&eacute;cessaires dans certains cas dont les principaux sont :
    <ul>
        <li> remariage avec un parent du conjoint d&eacute;funt (fr&egrave;re,
            soeur, oncle, tante, neveu, ni&egrave;ce, etc.) ;</li>
        <li> parent&eacute; avec l'&eacute;poux ou l'&eacute;pouse d'un ascendant du
            conjoint, mais ne figurant pas dans l'ascendance du dit conjoint ;</li>
        <li>parrain ou marraine &eacute;pousant filleule ou filleul ; on parle alors d&rsquo;affinit&eacute; spirituelle ;</li>
        <li>mariage des parrain et marraine d'un m&ecirc;me enfant.</li>
    </ul>
</td>
</tr>
<?php $x = Lettre('E');
$x = Mot(LG_GLOSS_ENDOGAMY, '', 1); ?>
<td>
    Mariage entre individus originaires du m&ecirc;me lieu.
</td>
<?php $x = Mot(LG_GLOSS_REGISTRATION); ?>
<td>
    Inscription sur un registre public des actes notari&eacute;s.
</td>
</tr>
<?php $x = Lettre('F');
$x = Mot(LG_GLOSS_ANNOUNCEMENT, '', 1); ?>
<td>
    Lettre ou billet qui annonce un &eacute;v&eacute;nement familial
    : naissance, bapt&ecirc;me, mariage, d&eacute;c&egrave;s...
</td>
<?php $x = Mot(LG_GLOSS_HOUSE, 'FE'); ?>
<td>
    Sous l'Ancien R&eacute;gime (avant la R&eacute;volution), cela
    d&eacute;signait l'ensemble des personnes vivant sous le m&ecirc;me
    toit et se r&eacute;unissant donc le soir autour du feu. Le feu
    ne comprend donc pas seulement la famille nucl&eacute;aire mais
    aussi les domestiques... Les <a href="#REC">recensements</a> se
    faisaient par feu et non par personne.
</td>
<?php $x = Mot(LG_GLOSS_FILIATION, 'FI'); ?>
<td>
    Lien de parent&eacute; unissant ascendants et descendants. Une
    filiation peut &ecirc;tre <a href="#AG">agnatique</a> ou <a href="#CO">cognatique</a>.
    Voir aussi ces mots.
</td>
<?php $x = Mot(LG_GLOSS_COLLECTION); ?>
<td>
    Ensemble des documents d'archives conserv&eacute;s dans un d&eacute;p&ocirc;t,
    des livres conserv&eacute;s dans une biblioth&egrave;que, ...
</td>
</tr>
<?php $x = Lettre('G');
$x = Mot(LG_GLOSS_GEDCOM, '', 1); ?>
<td>
    Norme d'&eacute;change de donn&eacute;es g&eacute;n&eacute;alogiques.
</td>
<?php $x = Mot(LG_GLOSS_GENERATION); ?>
<td>
    Chacun des degr&eacute;s successifs d'une <a href="#FI">filiation</a>
    : G&eacute;n&eacute;ration 1 : l'enfant, g&eacute;n&eacute;ration
    2 : les parents, g&eacute;n&eacute;ration 3 : les grands-parents...
    On &eacute;value &agrave; 30 ans en moyenne le temps qui s&eacute;pare deux g&eacute;n&eacute;rations.
</td>
<?php $x = Mot(LG_GLOSS_FULL_ORIGIN); ?>
<td>
    Fr&egrave;re germain, soeur germaine : issu du m&ecirc;me p&egrave;re et de la m&ecirc;me m&egrave;re.
    (voir <a href="#UT">ut&eacute;rin</a> et <a href="#CONS">consanguin</a>).
    Cousin germain, cousine germaine : issu d'un fr&egrave;re ou d'une soeur du p&egrave;re ou de la m&egrave;re.
</td>
<?php $x = Mot(LG_GLOSS_REGISTRY); ?>
<td>
    Lieu ou l'on classe et l'on conserve, sous la surveillance du greffier, les minutes des jugements, arr&ecirc;ts,
    rapports d'experts et ou l'on fait des d&eacute;clarations, des d&eacute;p&ocirc;ts.
</td>
</tr>
<?php $x = Lettre('H');
$x = Mot(LG_GLOSS_HERALDRY, '', 1); ?>
<td>
    Science des blasons et des <a href="#AR">armoiries</a>.
</td>
<?php $x = Mot(LG_GLOSS_HEIRS, 'HO'); ?>
<td>
    H&eacute;ritiers directs. On trouve la mention sans hoirs ou
    s.h. dans des g&eacute;n&eacute;alogies. Voir aussi <a href="#PO">post&eacute;rit&eacute;</a>.
</td>
</tr>
<?php $x = Lettre('I');
$x = Mot(LG_GLOSS_ILLEGITIMATE, '', 1); ?>
<td>
    N&eacute; hors mariage.
</td>
<?php $x = Mot(LG_GLOSS_IMPLEX, 'IMP'); ?>
<td>
    C'est le rapport entre le nombre th&eacute;orique des anc&ecirc;tres
    et leur nombre r&eacute;el. Voir aussi <a href="#dispense">dispenses
        de consanguinit&eacute;s</a>
</td>
<?php $x = Mot(LG_GLOSS_INDEX); ?>
<td>
    Table alphab&eacute;tique des noms cot&eacute;s dans un ouvrage
    ou un ensemble de documents.
</td>
<?php $x = Mot(LG_GLOSS_PUB_RECORD); ?>
<td>
    Inscription sur les registres du greffe du bailliage de la teneur
    essentielle des actes r&eacute;dig&eacute;s par les notaires.
</td>
<?php $x = Mot(LG_GLOSS_NO_WILL); ?>
<td>
    D&eacute;c&eacute;d&eacute; sans testament.
</td>
<?php $x = Mot(LG_GLOSS_INVENTORY); ?>
<td>
    Ouvrage qui analyse un fond d'archives et permet de l'exploiter
    plus facilement.
</td>
<?php $x = Mot(LG_GLOSS_POST_MORTEM_INVENTORY); ?>
<td>
    D&eacute;nombrement de tous les biens meubles et immeubles laiss&eacute;s par un d&eacute;funt et constituant sa succession.
    On trouve ces documents dans la s&eacute;rie B aux archives d&eacute;partementales.
</td>
</tr>
<?php $x = Lettre('L');
$x = Mot(LG_GLOSS_LEGITIMATION, 'LEGIT', 1); ?>
<td>
    Action de l&eacute;gitimer, de rendre l&eacute;gitime. La l&eacute;gitimation d'un enfant <a href="#NATUR">naturel</a>.
</td>
<?php $x = Mot(LG_GLOSS_BUNDLE); ?>
<td>
    Unit&eacute; de conservation d'archives.
</td>
<?php $x = Mot(LG_GLOSS_LINEAGE); ?>
<td>
    Ligne directe qui lie une personne &agrave; son anc&ecirc;tre. On dit encore lign&eacute;e ou ligne.
</td>
<?php $x = Mot(LG_GLOSS_HISTORY_BOOK); ?>
<td>
    Ouvrage manuscrit dans lequel ont &eacute;t&eacute; inscrits tous les &eacute;v&eacute;nements marquants de la vie d'une famille,
    parfois sur plusieurs g&eacute;n&eacute;rations.
</td>
<?php $x = Mot(LG_GLOSS_FAMILY_REC_BOOK); ?>
<td>
    Document d&eacute;livr&eacute; lors du mariage o&ugrave; sont not&eacute;s les renseignements relatifs &agrave; l'&eacute;tat civil.
</td>
<?php $x = Mot(LG_GLOSS_MILITARY_RECORD); ?>
<td>
    Document d&eacute;livr&eacute; lors du service militaire.
</td>
</tr>
<?php $x = Lettre('M');
$x = Mot(LG_GLOSS_FAMILY, '', 1); ?>
<td>
    Famille, s'emploie uniquement pour la noblesse.
</td>
<?php $x = Mot(LG_GLOSS_MATRONYM, 'MA'); ?>
<td>
    Nom de famille transmis par la m&egrave;re. Voir aussi <a href="#PA">Patronyme</a>.
</td>
<?php $x = Mot(LG_GLOSS_MARGINAL_MENTION); ?>
<td>
    Inscription faite en marge d'un acte d'&eacute;tat civil indiquant le contenu d'un autre acte.
</td>
<?php $x = Mot(LG_GLOSS_RECORD); ?>
<td>
    Acte original d&eacute;pos&eacute; au greffe ou conserv&eacute; chez un notaire,
    pour d&eacute;livrer des copies appel&eacute;es grosses ou exp&eacute;ditions.
</td>
</tr>
<?php $x = Lettre('N');
$x = Mot(LG_GLOSS_NATURAL, 'NATUR', 1); ?>
<td>
    Qui est n&eacute; hors mariage, par opposition &agrave; <a href="#LEGIT">l&eacute;gitime</a>. Enfant naturel.
    Qui est n&eacute; de la personne m&ecirc;me par opposition &agrave; adoptif
</td>
<?php $x = Mot(LG_GLOSS_NUMBERING); ?>
<td>
    Syst&egrave;me d'indexation des arbres g&eacute;n&eacute;alogiques permettant un inventaire et un rep&eacute;rage m&eacute;thodique de chaque individu.<br />
    Exemples : <a href="#SOSA">Sosa</a>-Stradonitz (ou encore Eytzinger), d'Aboville, Meurgey Tupigny.
</td>
<?php $x = Mot(LG_GLOSS_NOBLE_FAMILIES); ?>
<td>
    Ouvrage recensant les familles nobles.
</td>
</tr>
<?php $x = Lettre('O');
$x = Mot(LG_GLOSS_URGENT_BAPTISM, '', 1); ?>
<td>
    Bapt&ecirc;me r&eacute;alis&eacute; d'urgence en l'absence d'un eccl&eacute;siastique, lorsque l'on craint le d&eacute;c&egrave;s de l'enfant.
</td>
<?php $x = Mot(LG_GLOSS_ONOMASTICS); ?>
<td>
    Science qui &eacute;tudie les noms propres (noms de lieux, patronymes...).
</td>
</tr>
<?php $x = Lettre('P');
$x = Mot(LG_GLOSS_PALEOGRAPHY, '', 1); ?>
<td>
    &Eacute;tude des &eacute;critures anciennes.
</td>
<?php $x = Mot(LG_GLOSS_RELATIVES); ?>
<td>
    Ensemble des parents vivants d'un individu &agrave; un moment donn&eacute;.
</td>
<?php $x = Mot(LG_GLOSS_PARISH); ?>
<td>
    Territoire soumis &agrave; l'autorit&eacute; spirituelle d'un cur&eacute;.
</td>
<?php $x = Mot(LG_GLOSS_PATRONYMIC, 'PA'); ?>
<td>
    Nom de famille transmis par le p&egrave;re. Voir aussi <a href="#MA">Matronyme</a>.
</td>
<?php $x = Mot(LG_GLOSS_POSTERITY, 'PO'); ?>
<td>
    Ensemble des descendants d'une personne. Lorsque la personne n'a pas de descendants, on indique &quot;sans post&eacute;rit&eacute;&quot;
    ou &quot;s.p.&quot;. Voir aussi <a href="#HO">Hoirs</a>.
</td>
<?php $x = Mot(LG_GLOSS_YOUNGER, 'PU'); ?>
<td>
    N&eacute; apr&egrave;s. On parle par exemple d'un fr&egrave;re
    pu&icirc;n&eacute;. Voir aussi <a href="#HO">Hoirs</a> et <a href="#BE">Benjamin</a>.
</td>
</tr>
<?php $x = Lettre('Q');
$x = Mot(LG_GLOSS_LINEAGE_ANCESTOR, '', 1); ?>
<td>
    Mot ancien synonyme d'anc&ecirc;tre.
</td>
</tr>
<?php $x = Lettre('R');
$x = Mot(LG_GLOSS_CENSUS, 'REC', 1); ?>
<td>
    Op&eacute;ration administrative qui consiste &agrave; d&eacute;nombrer une population. Ces documents apportent de nombreux renseignements
    concernant les familles. Jusqu'au XVIIIe si&egrave;cle, les recensements sont faits par <a href="#FE">feu</a>.
</td>
<?php $x = Mot(LG_GLOSS_PARISH_REGISTERS, 'RE'); ?>
<td>
    Registres de la paroisse dans lesquels &eacute;taient indiqu&eacute;s
    les bapt&ecirc;mes, mariages et s&eacute;pultures. Voir aussi <a href="#BMS">BMS</a>.
</td>
</tr>
<?php $x = Lettre('S');
$x = Mot(LG_GLOSS_SOSA, 'SOSA', 1); ?>
<td>
    Num&eacute;ro que l'on attribue &agrave; un anc&ecirc;tre.
    <?php echo '<a href="' . Get_Adr_Base_Ref() . 'Glossaire_Sosa.php">'
        . '<img src="' . $chemin_images_icones . $Icones['loupe'] . '" alt="' . LG_GLOSS_MORE_INFO . '" border="0"/>'; ?>
    </a>
</td>
<?php $x = Mot(LG_GLOSS_SOURCES); ?>
<td>
    Documents consult&eacute;s pour l'&eacute;tablissement d'une g&eacute;n&eacute;alogie ou d'une histoire familiale.
</td>
<?php $x = Mot(LG_GLOSS_NICKNAME); ?>
<td>
    Nom donn&eacute; &agrave; une personne en plus de son nom v&eacute;ritable.
    Les surnoms du Moyen &Acirc;ge sont &agrave; l'origine de nos noms de famille actuels.
</td>
</tr>
<?php $x = Lettre('T');
$x = Mot(LG_GLOSS_10Y_TABLE, '', 1); ?>
<td>
    Registre r&eacute;capitulatif des actes d'&eacute;tat civil class&eacute;s
    par ordre alphab&eacute;tique et chronologique sur une p&eacute;riode
    de dix ans. Ces tables ont &eacute;t&eacute; institu&eacute;es en 1793.
</td>
<?php $x = Mot(LG_GLOSS_TABELLION); ?>
<td>
    Fonctionnaire autrefois charg&eacute; de mettre en grosse les actes dont les minutes
    avaient &eacute;t&eacute; dress&eacute;es par les notaires.
</td>
</tr>
<?php $x = Lettre('U');
$x = Mot(LG_GLOSS_COMMON, '', 1); ?>
<td>
    Ouvrage de consultation courante mis &agrave; la disposition
    des lecteurs dans un d&eacute;p&ocirc;t d'archives ou une biblioth&egrave;que.
</td>
<?php $x = Mot(LG_GLOSS_UTERINE, 'UT'); ?>
<td>
    Parent du c&ocirc;t&eacute; maternel. Deux enfants n&eacute;s d'une m&ecirc;me m&egrave;re mais de p&egrave;res diff&eacute;rents
    sont dits fr&egrave;res ut&eacute;rins. Voir aussi <a href="#CONS">Consanguin</a>.
</td>
</tr>
<?php $x = Lettre('V');
$x = Mot(LG_GLOSS_5PC_TAX, '', 1); ?>
<td>
    Imp&ocirc;t indirect de 5% de tous les revenus, &eacute;tabli en 1749 et
    aboli en 1786, destin&eacute; &agrave; l'amortissement de la dette du royaume.
</td>
</tr>
</table>
</div>
<table>
    <tr>
        <td>&nbsp;</td>
    </tr>
</table>
<?php

Insere_Bas($compl);
?>
</body>

</html>