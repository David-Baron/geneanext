<?php

/*
 * Geneanext Installer
 */

if (file_exists(__DIR__ . '/../setup/install.php')) {
    require(__DIR__ . '/../setup/install.php');
} else {
    header('Location: /');
}