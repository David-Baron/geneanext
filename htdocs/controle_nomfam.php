<?php
//=====================================================================
//	Vérification si un nom de famille existe ou non dans la base utilisé dans Edition_NomFam.php
//=====================================================================
//	Retourne :
//		Erreur si les paramètres sont mal passés
//		L'identifiant si le nom de famille existe
//		OK si le nom de famille n'existe pas
//=====================================================================

require(__DIR__ . '/../app/ressources/fonctions.php');

$x = Lit_Env();

//	Récupération du nom
$nom = $_POST['nom'];
$nom = Secur_Variable_Post($nom, 50, 'S');
if ($nom == '') {
    echo 'Erreur';
    return;
}
// Récupération de l'identifiant
$identifiant = $_POST['identifiant'];
$identifiant = Secur_Variable_Post($identifiant, 1, 'N');
if ($identifiant == 0) {
    echo 'Erreur';
    return;
}

//	Recherche en base
$sql = 'SELECT idNomFam FROM ' . nom_table('noms_famille') . ' WHERE nomFamille =\'' . $nom . '\' AND idNomFam <>' . $identifiant . ' limit 1';
$res = lect_sql($sql);
if ($res->rowCount() > 0) {
    $row = $res->fetch(PDO::FETCH_NUM);
    echo $row[0];
    return;
}
echo 'OK';
