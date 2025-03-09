<?php
/* Step 1: Save this first part to a file named captcha_image.php*/

require(__DIR__ . '/app/bootstrap.php');
require(__DIR__ . '/fonctions.php');

$rand1 = rand(1, 9);
$rand2 = rand(1, 9);
$_SESSION['verifkey'] = $rand1 + $rand2;

$ResultStr = $rand1 . '+' . $rand2 . '=';

$im = imagecreatefromgif($chemin_images . 'background.gif'); // where background.gif is your background image
if ($im) {
    //$TextColor = imagecolorallocate($im,213,218,207); // To customise according to your background image
    $TextColor = imagecolorallocate($im, 155, 190, 207); // To customise according to your background image
    $LineColor = imagecolorallocate($im, 183, 172, 198); // To customise according to your background image

    imageline($im, 12, 10, 60, 40, $LineColor); // To customise according to your background image
    imageline($im, 8, 45, 60, 15, $LineColor); // To customise according to your background image

    imagestring($im, 5, 20, 20, $ResultStr, $TextColor);

    header('Content-Type: image/gif');
    imagegif($im);
    imagedestroy($im);
} else {
    echo 'failed';
}
/* End of captcha_image.php*/
