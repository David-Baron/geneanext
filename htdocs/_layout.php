<!DOCTYPE html>
<html lang="<?= $locale; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= my_html($meta_title); ?></title>
    <meta name="description" content="<?= $LG_index_desc; ?>">
    <meta name="keywords" content="' . '$mots' . '">
    <!-- <meta name="robots" content="<?= $meta_robots; ?>"> -->
    <meta name="REVISIT-AFTER" content="7 days">
    <link rel="shortcut icon" href="<?= $root; ?>/assets/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row text-center">
            <h1><?= $title; ?></h1>
        </div>
        <?php require(__DIR__ . '/_menu_bs.php'); ?>
        <main class="container">
            <div class="row">
                <?= $body; ?>
                <!-- <table width="60%" align="center">
<tr><td><fieldset style="width:90%;"><legend>' . $LG_index_quick_search . ' <img src="' . $root . '/assets/img/' . $Icones['help'] . '" alt="' . $LG_index_tip_search . '" title="' . $LG_index_tip_search . '"></legend>
<table align="center">
<tr><td>
<fieldset><legend>' . $LG_index_menu_pers . '</legend>
<form method="post" action="' . $root . '/Recherche_Personne" >
<table>
<tr><td>' . LG_PERS_NAME . ' :</td><td><input type="text" size="30" name="NomP"/></td>
<td rowspan="2" valign="middle"><input type="submit" name="ok" value="' . $lib_Rechercher . '" style="background:url(' . $root . '/assets/img/' . $Icones['chercher'] . ') no-repeat;padding-left:18px;" /></td></tr>
<tr><td>' . LG_PERS_FIRST_NAME . ' :</td><td><input type="text" size="30" name="Prenoms"/></td></tr>
</table>
<input type="hidden" name="Sortie" value="e">
<input type="hidden" name="Son" value="o">
</form>
</fieldset>
</td>
<td valign="middle">
<fieldset><legend>' . $LG_index_menu_towns . '</legend>
<form method="post" action="' . $root . '/Recherche_Ville" >
<input type="text" size="30" name="NomV"/>
<input type="hidden" name="Sortie" value="e">
<input type="hidden" name="Code_Postal" value="">
<input type="hidden" name="Departement" value="-1">
<input type="submit" name="ok" value="' . $lib_Rechercher . '" style="background:url(' . $root . '/assets/img/' . $Icones['chercher'] . ') no-repeat;padding-left:18px;" />
</form>
</fieldset>
</td>
</tr>
</table>
</fieldset></td></tr></table> -->
            </div>

        </main>

        <footer class="container text-center">
            <?php if ($SiteGratuit) { ?>
                <div class="row justify-content-center"><?= $LG_index_responsability; ?></div>
            <?php } ?>
            <?php
            if ($settings['Date_Modification'] != '0000-00-00 00:00:00') { ?>
                <div class="row"><?= $LG_index_last_update; ?> <?= DateTime_Fr($settings['Date_Modification']); ?></div>
            <?php } ?>

        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>