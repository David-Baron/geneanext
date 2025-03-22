<?php

//=====================================================================
// User authentification
//=====================================================================
$nom_page = 'index.php';

require(__DIR__ . '/../app/ressources/fonctions.php');
require(__DIR__ . '/../app/Engine/AppUserAuthenticator.php');
require(__DIR__ . '/../app/Model/ConnexionModel.php');

$meta_title = ($settings['Nom'] !== '???' ? $settings['Nom'] : 'Site genealogique') . ' | S\'identifier';
$title = ($settings['Nom'] !== '???') ? $LG_index_welcome . ' ' . $settings['Nom'] : 'Site genealogique en construction';
$has_credencials_error = false;

if ($request->getMethod() === 'POST' && $session->get('antiflood', 0) < 5) {
    $appUserAuthenticator = new AppUserAuthenticator($session);

    if ($appUserAuthenticator->authenticate($request->request->get('username'), $request->request->get('plain_text_password'))) {
        // Authentification log
        $connexionModel = new ConnexionModel();
        $connexionModel->insert(['idUtil' => $session->get('user')['idUtil'], 'dateCnx' => (new DateTime())->format('Y-m-d h:m:s'), 'Adresse_IP' => $request->getClientIp()]);

        header('Location: ' . $root . '/');
        exit;
    }

    $has_credencials_error = true;
}

if ($session->get('antiflood', 0) >= 5) {
    // Add flash message banned for xxx time
    header('Location: ' . $root . '/');
    exit();
}
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-4">
        <?php if ($has_credencials_error) { ?>
            <div class="mb-3">
                <div class="alert alert-sm alert-danger alert-dismissible fade show" role="alert">
                    <?= $LG_index_connexion_error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        <?php } ?>

        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" name="username">
            </div>
            <div class="mb-3">
                <label for="plain_text_password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="plain_text_password" name="plain_text_password">
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-sm btn-primary">M'identifier</button>
            </div>
        </form>
    </div>
</div>
<?php
$body = ob_get_clean();

ob_start();
require(__DIR__ . '/_layout.php');
$view = ob_get_clean();
$response->setContent($view);
