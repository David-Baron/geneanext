<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="<?= $root; ?>/liste_pers?Type_Liste=P"><?= $LG_index_menu_pers; ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $root; ?>/liste_nomfam"><?= $LG_index_menu_names; ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $root; ?>/liste_villes?Type_Liste=V"><?= $LG_index_menu_towns; ?></a></li>
                <li class="nav-item"><a class="nav-link" href="<?= $root; ?>/stat_base"><?= $LG_Menu_Title['Statistics']; ?></a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Dropdown
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
            </ul>
            <div class="d-flex">
                <?php if ($session->has('user')) { ?>
                    <a class="btn btn-sm btn-secondary" href="<?= $root; ?>/logout">Se déconnecter</a>
                <?php } else { ?>
                    <a class="btn btn-sm btn-secondary" href="<?= $root; ?>/login">S'identifier</a>
                <?php } ?>
            </div>
        </div>
    </div>
</nav>