<?php

//=====================================================================
// Notions de base de la généalogie
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/app/ressources/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Start'];         // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/app/ressources/gestion_pages.php');

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'Premiers_Pas_Genealogie', '');
?>

NB : les indications ci-dessous n'ont pas vocation à être exhaustives ;
elles permettront juste à l'utilisateur de faire ses premiers pas en généalogie.<br />

<br>
<table width="100%" align="left" cellspacing="1" cellpadding="3">
    <tr class="rupt_table">
        <td><b><?= LG_START_DEF; ?></b></td>
    </tr>
</table>
<br><br>


La généalogie a pour objet la recherche de l'origine et de la filiation des personnes et des familles.<br />

<br />Elle peut être de type :
<ul>
    <li>Ascendante :
        à partir d'un individu, on remonte dans le passé en identifiant les
        parents d'une personne puis les parents de ceux-ci et ainsi de suite.</li>
    <li>Descendante :
        à partir d'un individu, on descend vers le présent en identifiant les
        enfants d'une personne puis les enfants de ceux-ci et ainsi de suite. </li>
</ul>

La généalogie permet également de situer les individus et les familles dans leur contexte historique et social.<br />

<br>
<table width="100%" align="left" cellspacing="1" cellpadding="3">
    <tr class="rupt_table">
        <td><b><?= LG_START_SOURCES; ?></b></td>
    </tr>
</table>
<br><br>
La généalogie s'appuie principalement sur les sources d'information suivantes (pour la France) :<br />

<ul>
    <li>les renseignements recueillis dans la famille et l'entourage (papiers de
        famille, livrets divers, photos, témoignages) ;</li>
    <li>les registres paroissiaux (avant 1792) et les registres d'état civil (à partir de 1792) ;</li>
    <li>les tables décennales qui récapitulent pour une période de dix ans et par
        commune tous les actes de l'état civil (naissances, mariages et décès) en
        les classant par ordre alphabétique par tranche de 10 ans ; ces
        tables sont disponibles depuis 1793, voire 1802.
        Elles permettent un accès rapide à l'information des actes d'état civil ;</li>
    <li>les listes nominatives de la population (recensement), régulièrement établies
        depuis 1836 (sauf interruption en 1916 et 1941) (archives départementales et communales) ;</li>
    <li>les actes notariés généralement abondants après la révolution (archives
        départementales et communales).</li>
</ul>

<br>
<table width="100%" align="left" cellspacing="1" cellpadding="3">
    <tr class="rupt_table">
        <td><b><?= LG_START_CIVIL_REGISTRATION; ?></b></td>
    </tr>
</table>
<br><br>
Il recouvre 3 types d'actes (NMD) :<br />
<ul>
    <li>acte de Naissance<br />
        Il comporte :
        <ul type="circle">
            <li>La date de rédaction de l'acte, le nom et le(s) prénom(s) du nouveau-né, ses dates,
                heures et lieux de naissance ;</li>
            <li>Les noms et prénoms des parents, leur âge, puis à partir du 28 octobre 1922
                leurs dates et le lieu de naissance, leur profession, état matrimonial
                (mariés ou non) et lieu de résidence ;</li>
            <li>Des informations sur les déclarants ou les témoins ;</li>
            <li>Les éventuelles mentions marginales : date et lieu de mariage, date et lieu de décès...<br /><br /></li>
        </ul>
    </li>
    <li>acte de Mariage<br />
        Il comporte :
        <ul type="circle">
            <li>La date, l'heure et le lieu ;</li>
            <li>Les noms, prénoms, dates et lieux de naissance, situations, professions des époux ;</li>
            <li>Les références d'un éventuel contrat de mariage : depuis 1850, la date, le nom du notaire
                et le lieu de l'étude doivent être indiqués ;</li>
            <li>Les parents ;</li>
            <li>Les noms, prénoms, état matrimonial (mariés ou non), professions, lieu de domicile des parents ;</li>
            <li>Les noms, prénoms, âges, professions, domiciles et liens de parenté (pas toujours indiqué) des témoins ;</li>
            <li>Mentions possibles : légitimation par mariage d'enfants issus du couple : la date
                et le lieu de naissance sont indiqués.<br /><br /></li>
        </ul>
    </li>
    <li>acte de Décès<br />
        Il comporte :
        <ul type="circle">
            <li>La nature de l'acte : original (dans la commune où le décès a lieu) ou
                transcription légale (dans la commune où la personne est domiciliée) ;</li>
            <li>La date et l'heure du décès ;</li>
            <li>Les nom et prénom(s) du défunt ;</li>
            <li>L'âge et le lieu de naissance puis la date précise ;</li>
            <li>La profession ;</li>
            <li>Le domicile ;</li>
            <li>L'état matrimonial : célibataire, marié, divorcé ou veuf ; éventuellement le conjoint ;</li>
            <li>Les noms et prénoms des parents (éventuellement ; exactitude non garantie) ; </li>
            <li>Les noms et prénoms des déclarants et témoins, leurs âges, professions et domiciles.</li>
        </ul>
    </li>
</ul>

<br />Les actes de l'état civil sont établis en deux exemplaires :
l'original et son double ; l'un est conservé à la mairie et l'autre déposé
dans les greffes puis remis aux archives départementales. Dans certains cas,
les communes versent leurs archives anciennes (plus de 100 ans) aux Archives
Départementales (AD).<br />

Si l'acte a moins de 100 ans, vous devez prouver votre lien de parenté direct avec la personne pour
obtenir une copie intégrale sauf pour les actes de décès sinon vous n'aurez
qu'un extrait de l'acte.<br />

Les communes n'ont aucune obligation de faire des recherches si vous ne connaissez pas la date
exacte d'un acte. Vous devez fournir des indications précises. Certaines mairies accepteront
de consulter les tables décennales pour retrouver la date précise
mais elles n'y sont pas obligées.<br />

En cas de demande d'un acte par courrier, il est recommandé de fournir une enveloppe timbrée pour la réponse.<br />

Pour certaines mairies, il est possible de faire une demande d'acte via Internet.<br />

<br>
<table width="100%" align="left" cellspacing="1" cellpadding="3">
    <tr class="rupt_table">
        <td><b><?= LG_START_CHURCH_RECORDS; ?></b></td>
    </tr>
</table>
<br><br>
Ils recouvrent 3 types d'actes (<a href="<?= $root; ?>/glossaire_gen.php#BMS">BMS</a>) :<br />
<ul>
    <li>acte de Baptême<br />
        Il comporte :
        <ul type="circle">
            <li>Les nom et prénom(s) (qui est éventuellement
                celui du parrain pour le garçon et celui de la marraine pour la fille) ;</li>
            <li>Les date et lieu du baptême. La date de
                naissance n'est pas toujours indiquée. L'enfant est généralement baptisé
                le jour même ("Né et baptisé le jour même") ou le lendemain ;</li>
            <li>Les noms et prénoms des parents, parfois la
                profession ou des mentions comme Honorables gens ...</li>
            <li>Les noms et prénoms du parrain et de la marraine. Le lien de parenté est quelquefois
                indiqué ; il n'est pas rare qu'il s'agisse d'un grand-père et d'une grand-mère de l'enfant.<br /><br /></li>
        </ul>
    </li>
    <li>acte de Mariage<br />
        Il comporte :
        <ul type="circle">
            <li>Les noms et prénoms et situation (majeur, mineur, veuf, veuve...) des époux ; leur âge (parfois) et lieu de
                naissance. La tradition veut que le mariage ait lieu dans la commune de
                l'épouse mais ce n'est pas une obligation ;</li>
            <li>Les noms et prénoms des parents avec la mention décédé(e) ou défunt(e) si c'est le cas ;</li>
            <li>Les noms et prénoms des témoins et éventuellement les liens avec les époux ;</li>
            <li>Les signatures, ou les marques des personnes ;</li>
            <li>Des mentions diverses : <a href="<?= $root; ?>/glossaire_gen.php#dispenseC">dispenses de
                    consanguinité</a>, 
                <a href="<?= $root; ?>/glossaire_gen.php#dispenseA">d'affinité</a>, reconnaissance d'un enfant né avant le mariage.<br /><br />
            </li>
        </ul>
    </li>
    <li>acte de Sépulture<br />
        Il comporte :
        <ul type="circle">
            <li>Les nom et prénoms du défunt ; </li>
            <li>L'âge, estimé, ou la date et le lieu de naissance (plus rare) ;</li>
            <li>La date et le lieu d'inhumation : la date du
                décès n'est pas toujours indiquée. Le lieu de l'inhumation est soit le
                cimetière, l'Eglise ou une chapelle. L'inhumation a lieu le jour du décès
                ou le lendemain en principe.</li>
            <li>Les noms et prénoms des personnes présentes, leur lien de parenté (parfois) ;</li>
            <li>Des mentions diverses : qualité de la personne, cause du décès...<br /></li>
        </ul>
    </li>
</ul>

Plus on remonte dans le temps, plus les actes sont parcellaires...<br />

<br>
<table width="100%" align="left" cellspacing="1" cellpadding="3">
    <tr class="rupt_table">
        <td><b><?= LG_START_YOUR_TURN; ?></b></td>
    </tr>
</table>
<br><br>

Commencez par déterminer de qui partira la généalogie ; il s'agit de votre
<a href="<?= $root; ?>/glossaire_gen.php#CUJUS">de cujus</a>.
Rassemblez un maximum de documents de famille, pensez à interroger les témoins, utilisez
les sources d'état civil pour enrichir votre généalogie,
utilisez Généamania pour organiser vos données et c'est parti...<br />
Soyez minutieux, ne négligez aucune piste ; l'expérience montre que des informations peuvent se révéler utiles après coup.<br />
Bonnes recherches...

<table cellpadding="0" width="100%">
    <tr>
        <td align="right">
            <?= $compl; ?>
            <a href="<?= $root; ?>/"><img src="<?= $root; ?>/assets/img/house.png" alt="Accueil" title="Accueil" /></a>
        </td>
    </tr>
</table>

</body>

</html>