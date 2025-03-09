<?php

//=====================================================================
// Notions de base de la généalogie
//=====================================================================

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$titre = $LG_Menu_Title['Start'];         // Titre pour META
$x = Lit_Env();                        // Lecture de l'indicateur d'environnement
require(__DIR__ . '/Gestion_Pages.php');

$compl = Ajoute_Page_Info(600, 150);
Insere_Haut($titre, $compl, 'Premiers_Pas_Genealogie', '');
?>

NB : les indications ci-dessous n'ont pas vocation &agrave; &ecirc;tre exhaustives ;
elles permettront juste &agrave; l'utilisateur de faire ses premiers pas en g&eacute;n&eacute;alogie.<br />

<?php $x = paragraphe(LG_START_DEF); ?>

La g&eacute;n&eacute;alogie a pour objet la recherche de l'origine et de la filiation des personnes et des familles.<br />

<br />Elle peut &ecirc;tre de type&nbsp;:
<ul>
    <li>Ascendante&nbsp;:
        &agrave; partir d&rsquo;un individu, on remonte dans le pass&eacute; en identifiant les
        parents d'une personne puis les parents de ceux-ci et ainsi de suite.</li>
    <li>Descendante&nbsp;:
        &agrave; partir d&rsquo;un individu, on descend vers le pr&eacute;sent en identifiant les
        enfants d'une personne puis les enfants de ceux-ci et ainsi de suite. </li>
</ul>

La g&eacute;n&eacute;alogie permet &eacute;galement de situer les individus et les familles dans leur contexte historique et social.<br />

<?php $x = paragraphe(LG_START_SOURCES); ?>

La g&eacute;n&eacute;alogie s&rsquo;appuie principalement sur les sources d'information suivantes (pour la France)&nbsp;:<br />

<ul>
    <li>les renseignements recueillis dans la famille et l'entourage (papiers de
        famille, livrets divers, photos, t&eacute;moignages) ;</li>
    <li>les registres paroissiaux (avant 1792) et les registres d'&eacute;tat civil (&agrave; partir de 1792) ;</li>
    <li>les tables d&eacute;cennales qui r&eacute;capitulent pour une p&eacute;riode de dix ans et par
        commune tous les actes de l'&eacute;tat civil (naissances, mariages et d&eacute;c&egrave;s) en
        les classant par ordre alphab&eacute;tique par tranche de 10 ans ; ces
        tables sont disponibles depuis 1793, voire 1802.
        Elles permettent un acc&egrave;s rapide &agrave; l&rsquo;information des actes d&rsquo;&eacute;tat civil ;</li>
    <li>les listes nominatives de la population (recensement), r&eacute;guli&egrave;rement &eacute;tablies
        depuis 1836 (sauf interruption en 1916 et 1941) (archives d&eacute;partementales et communales) ;</li>
    <li>les actes notari&eacute;s g&eacute;n&eacute;ralement abondants apr&egrave;s la r&eacute;volution (archives
        d&eacute;partementales et communales).</li>
</ul>

<?php $x = paragraphe(LG_START_CIVIL_REGISTRATION); ?>
Il recouvre 3 types d'actes&nbsp;(NMD) :<br />
<ul>
    <li>acte de Naissance<br />
        Il comporte :
        <ul type="circle">
            <li>La date de r&eacute;daction de l'acte, le nom et le(s) pr&eacute;nom(s) du nouveau-n&eacute;, ses dates,
                heures et lieux de naissance ;</li>
            <li>Les noms et pr&eacute;noms des parents, leur &acirc;ge, puis &agrave; partir du 28 octobre 1922
                leurs dates et le lieu de naissance, leur profession, &eacute;tat matrimonial
                (mari&eacute;s ou non) et lieu de r&eacute;sidence ;</li>
            <li>Des informations sur les d&eacute;clarants ou les t&eacute;moins ;</li>
            <li>Les &eacute;ventuelles mentions marginales : date et lieu de mariage, date et lieu de d&eacute;c&egrave;s...<br /><br /></li>
        </ul>
    </li>
    <li>acte de Mariage<br />
        Il comporte :
        <ul type="circle">
            <li>La date, l'heure et le lieu ;</li>
            <li>Les noms, pr&eacute;noms, dates et lieux de naissance, situations, professions des &eacute;poux ;</li>
            <li>Les r&eacute;f&eacute;rences d'un &eacute;ventuel contrat de mariage : depuis 1850, la date, le nom du notaire
                et le lieu de l'&eacute;tude doivent &ecirc;tre indiqu&eacute;s ;</li>
            <li>Les parents ;</li>
            <li>Les noms, pr&eacute;noms, &eacute;tat matrimonial (mari&eacute;s ou non), professions, lieu de domicile des parents ;</li>
            <li>Les noms, pr&eacute;noms, &acirc;ges, professions, domiciles et liens de parent&eacute; (pas toujours indiqu&eacute;) des t&eacute;moins ;</li>
            <li>Mentions possibles&nbsp;: l&eacute;gitimation par mariage d'enfants issus du couple : la date
                et le lieu de naissance sont indiqu&eacute;s.<br /><br /></li>
        </ul>
    </li>
    <li>acte de D&eacute;c&egrave;s<br />
        Il comporte :
        <ul type="circle">
            <li>La nature de l'acte&nbsp;: original (dans la commune o&ugrave; le d&eacute;c&egrave;s a lieu) ou
                transcription l&eacute;gale (dans la commune o&ugrave; la personne est domicili&eacute;e) ;</li>
            <li>La date et l'heure du d&eacute;c&egrave;s ;</li>
            <li>Les nom et pr&eacute;nom(s)&nbsp;du d&eacute;funt ;</li>
            <li>L'&acirc;ge et le lieu de naissance puis la date pr&eacute;cise ;</li>
            <li>La profession ;</li>
            <li>Le domicile ;</li>
            <li>L'&eacute;tat matrimonial : c&eacute;libataire, mari&eacute;, divorc&eacute; ou veuf ; &eacute;ventuellement le conjoint ;</li>
            <li>Les noms et pr&eacute;noms des parents (&eacute;ventuellement ; exactitude non garantie) ; </li>
            <li>Les noms et pr&eacute;noms des d&eacute;clarants et t&eacute;moins, leurs &acirc;ges, professions et domiciles.</li>
        </ul>
    </li>
</ul>

<br />Les actes de l'&eacute;tat civil sont &eacute;tablis en deux exemplaires :
l'original et son double ; l'un est conserv&eacute; &agrave; la mairie et l'autre d&eacute;pos&eacute;
dans les greffes puis remis aux archives d&eacute;partementales. Dans certains cas,
les communes versent leurs archives anciennes (plus de 100 ans) aux Archives
D&eacute;partementales (AD).<br />

Si l'acte a moins de 100 ans, vous devez prouver votre lien de parent&eacute; direct avec la personne pour
obtenir une copie int&eacute;grale sauf pour les actes de d&eacute;c&egrave;s sinon vous n'aurez
qu'un extrait de l'acte.<br />

Les communes n'ont aucune obligation de faire des recherches si vous ne connaissez pas la date
exacte d'un acte. Vous devez fournir des indications pr&eacute;cises. Certaines mairies accepteront
de consulter les tables d&eacute;cennales pour retrouver la date pr&eacute;cise
mais elles n'y sont pas oblig&eacute;es.<br />

En cas de demande d'un acte par courrier, il est recommand&eacute; de fournir une enveloppe timbr&eacute;e pour la r&eacute;ponse.<br />

Pour certaines mairies, il est possible de faire une demande d'acte via Internet.<br />

<?php $x = paragraphe(LG_START_CHURCH_RECORDS); ?>

Ils recouvrent 3 types d'actes&nbsp;(<a href="<?php echo Get_Adr_Base_Ref(); ?>Glossaire_Gen.php#BMS">BMS</a>) :<br />
<ul>
    <li>acte de Bapt&ecirc;me<br />
        Il comporte :
        <ul type="circle">
            <li>Les nom et pr&eacute;nom(s) (qui est &eacute;ventuellement
                celui du parrain pour le gar&ccedil;on et celui de la marraine pour la fille) ;</li>
            <li>Les date et lieu du bapt&ecirc;me. La date de
                naissance n'est pas toujours indiqu&eacute;e. L'enfant est g&eacute;n&eacute;ralement baptis&eacute;
                le jour m&ecirc;me (&quot;N&eacute; et baptis&eacute; le jour m&ecirc;me&quot;) ou le lendemain ;</li>
            <li>Les noms et pr&eacute;noms des parents, parfois la
                profession ou des mentions comme Honorables gens ...</li>
            <li>Les noms et pr&eacute;noms du parrain et de la marraine. Le lien de parent&eacute; est quelquefois
                indiqu&eacute; ; il n'est pas rare qu'il s'agisse d'un grand-p&egrave;re et d'une grand-m&egrave;re de l'enfant.<br /><br /></li>
        </ul>
    </li>
    <li>acte de Mariage<br />
        Il comporte :
        <ul type="circle">
            <li>Les noms et pr&eacute;noms et situation (majeur, mineur, veuf, veuve&hellip;) des &eacute;poux ; leur &acirc;ge (parfois) et lieu de
                naissance. La tradition veut que le mariage ait lieu dans la commune de
                l'&eacute;pouse mais ce n'est pas une obligation ;</li>
            <li>Les noms et pr&eacute;noms des parents avec la mention d&eacute;c&eacute;d&eacute;(e) ou d&eacute;funt(e) si c'est le cas ;</li>
            <li>Les noms et pr&eacute;noms des t&eacute;moins et &eacute;ventuellement les liens avec les &eacute;poux ;</li>
            <li>Les signatures, ou les marques des personnes ;</li>
            <li>Des mentions diverses : <a href="<?php echo Get_Adr_Base_Ref(); ?>Glossaire_Gen.php#dispenseC">dispenses de
                    consanguinit&eacute;</a>,&nbsp;
                <a href="<?php echo Get_Adr_Base_Ref(); ?>Glossaire_Gen.php#dispenseA">d'affinit&eacute;</a>, reconnaissance d'un enfant n&eacute; avant le mariage.<br /><br />
            </li>
        </ul>
    </li>
    <li>acte de S&eacute;pulture<br />
        Il comporte :
        <ul type="circle">
            <li>Les nom et pr&eacute;noms du d&eacute;funt ; </li>
            <li>L'&acirc;ge, estim&eacute;, ou la date et le lieu de naissance (plus rare) ;</li>
            <li>La date et le lieu d'inhumation : la date du
                d&eacute;c&egrave;s n'est pas toujours indiqu&eacute;e. Le lieu de l'inhumation est soit le
                cimeti&egrave;re, l'Eglise ou une chapelle. L'inhumation a lieu le jour du d&eacute;c&egrave;s
                ou le lendemain en principe.</li>
            <li>Les noms et pr&eacute;noms des personnes pr&eacute;sentes, leur lien de parent&eacute; (parfois) ;</li>
            <li>Des mentions diverses&nbsp;: qualit&eacute; de la personne, cause du d&eacute;c&egrave;s...<br /></li>
        </ul>
    </li>
</ul>

Plus on remonte dans le temps, plus les actes sont parcellaires...<br />

<?php $x = paragraphe(LG_START_YOUR_TURN); ?>

Commencez par d&eacute;terminer de qui partira la g&eacute;n&eacute;alogie ; il s'agit de votre
<a href="<?php echo Get_Adr_Base_Ref(); ?>Glossaire_Gen.php#CUJUS">de cujus</a>.
Rassemblez un maximum de documents de famille, pensez &agrave; interroger les t&eacute;moins, utilisez
les sources d'&eacute;tat civil pour enrichir votre g&eacute;n&eacute;alogie,
utilisez G&eacute;n&eacute;amania pour organiser vos donn&eacute;es et c'est parti...<br />
Soyez minutieux, ne n&eacute;gligez aucune piste ; l'exp&eacute;rience montre que des informations peuvent se r&eacute;v&eacute;ler utiles apr&egrave;s coup.<br />
Bonnes recherches...

<?php Insere_Bas($compl); ?>

</body>

</html>