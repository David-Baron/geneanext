<?php

//=====================================================================
// Converstisseur de nombres romains dans les 2 sens
// (c) JLS
// UTF-8
//=====================================================================

session_start();

include('fonctions.php');
$acces = 'L';                          // Type d'accès de la page : (M)ise à jour, (L)ecture
$x = Lit_Env();
$objet = $LG_Menu_Title['Convert_Roman_To_Arabic'];
$titre = $objet;
$mots = 'convertisseur';
include('Gestion_Pages.php');

?>
<script type="text/javascript">
<!--

// Blocage - débloquage des touches de chiffres
function bloque_chiffres(romain_arabe) {
  switch (romain_arabe) {
    case 'A' : b1 = false; b2 = true; break;
    case 'R' : b1 = true;  b2 = false; break;
    case 'X' : b1 = false; b2 = false; break;
  }
  document.forms.calculette.T0.disabled = b1;
  document.forms.calculette.T1.disabled = b1;
  document.forms.calculette.T2.disabled = b1;
  document.forms.calculette.T3.disabled = b1;
  document.forms.calculette.T4.disabled = b1;
  document.forms.calculette.T5.disabled = b1;
  document.forms.calculette.T6.disabled = b1;
  document.forms.calculette.T7.disabled = b1;
  document.forms.calculette.T8.disabled = b1;
  document.forms.calculette.T9.disabled = b1;
  document.forms.calculette.TI.disabled = b2;
  document.forms.calculette.TV.disabled = b2;
  document.forms.calculette.TX.disabled = b2;
  document.forms.calculette.TL.disabled = b2;
  document.forms.calculette.TC.disabled = b2;
  document.forms.calculette.TD.disabled = b2;
  document.forms.calculette.TM.disabled = b2;
}

function aj_car(chaine,romain_arabe) {
  document.forms.calculette.zone_saisie.value += chaine;
  bloque_chiffres(romain_arabe);
}

function n_car(car,nombre) {
  var ret = '';
  for (var pos = 1; pos <= nombre; pos++) ret = ret + car;
  return ret;
}

// Donne la correspondance en romain d'un chiffre arabe
function chiffre_romain(niveau, chiffre) {
	var Chiffre_1, Chiffre_5, CHiffre_10;
	switch (niveau) {
		case 0 : Chiffre_1 = 'I'; Chiffre_5 = 'V'; Chiffre_10 = 'X'; break;
		case 1 : Chiffre_1 = 'X'; Chiffre_5 = 'L'; Chiffre_10 = 'C'; break;
		case 2 : Chiffre_1 = 'C'; Chiffre_5 = 'D'; Chiffre_10 = 'M'; break;
		case 3 : Chiffre_1 = 'M'; Chiffre_5 = '-'; Chiffre_10 = '-'; break;
	}
	switch (chiffre) {
		case 1  :
		case 2  :
		case 3  : return n_car(Chiffre_1,chiffre); break;
		case 4  : return Chiffre_1 + Chiffre_5; break;
		case 5  : return Chiffre_5; break;
		case 6  :
		case 7  :
		case 8  : return Chiffre_5 + n_car(Chiffre_1,chiffre-5); break;
		case 9  : return Chiffre_1 + Chiffre_10; break;
		default : return ''; break;
	}
}

function vers_romain(saisie) {
	var saisie = document.forms.calculette.zone_saisie.value;
	var borne_maxi = parseInt(saisie);
	if (parseInt(saisie) > 3999)
	document.forms.calculette.resultat.value = '<?php echo $LG_Ch_Calc_Max;?> : 3999';
	else {
		var lsaisie = saisie.length;
		var pos;
		var nombre = '';
		var LeChiffre = 0;
		for (pos = 0; pos < lsaisie; pos++) {
			LeChiffre = parseInt(saisie.charAt(pos));
			nombre = nombre + chiffre_romain(lsaisie-pos-1, LeChiffre);
		}
		document.forms.calculette.zone_saisie.value = nombre;
		document.forms.calculette.resultat.value = saisie + ' = ' + nombre;
	}
	return false;
}

function vers_arabe(saisie) {
// Pour calculer, on balaye les chiffres ;
// si le chiffre à gauche est inférieur au chiffre à droite, il doit venir en déduction
// 'I'.charCodeAt(0) est remplacé par le caractère ASCII

  val = new Array(255);
  val['I'.charCodeAt(0)] = 1;
  val['V'.charCodeAt(0)] = 5;
  val['X'.charCodeAt(0)] = 10;
  val['L'.charCodeAt(0)] = 50;
  val['C'.charCodeAt(0)] = 100;
  val['D'.charCodeAt(0)] = 500;
  val['M'.charCodeAt(0)] = 1000;

  var nombre = 0;
  var precedent = 0;

  saisie = saisie.toUpperCase();

  for(var pos = saisie.length - 1; pos >= 0; pos-=1) {
    var Chiffre = val[saisie.charCodeAt(pos)];
    if (Chiffre == NaN)
      return 1;
    if (Chiffre >= precedent)
      nombre += Chiffre;
    else
      nombre -= Chiffre;
    precedent = Chiffre;
  }
  document.forms.calculette.zone_saisie.value = nombre;
  document.forms.calculette.resultat.value = saisie + ' = ' + nombre;
}

function conversion() {
  // Débranchement en fonction du premier caractère saisi
  var saisie = document.forms.calculette.zone_saisie.value;
  var car1 = saisie.substring(0,1);
  if (isNaN(car1) == true) vers_arabe(saisie);
  else                     vers_romain(saisie);
  efface_entree();
}

function backspace() {
  curvalue = document.forms.calculette.zone_saisie.value;
  curlength = curvalue.length;
  curvalue = curvalue.substring(0,curlength-1);
  document.forms.calculette.zone_saisie.value = curvalue;
}

function efface_entree() {
  document.forms.calculette.zone_saisie.value = "";
  bloque_chiffres("X");
}

//-->
</script>

<?php

function touche_chiffre($chiffre,$romain_arabe) {
	if (($chiffre >= '0') and ($chiffre <= '9')) $classe = 'BoutonCalc_1';
	else $classe = 'BoutonCalc_2';
	echo '<td>'.
	  '<input class="'.$classe.'" onclick="aj_car(\''.$chiffre.'\',\''.$romain_arabe.'\')" type="button" value="'.$chiffre.'" name="T'.$chiffre.'" id="T'.$chiffre.'"/>'.
	  '</td>'."\n";
}

$compl = Ajoute_Page_Info(600,200);

Insere_Haut(my_html($objet),$compl,'Conv_Romain','');

echo '<br />';

echo '<form action="'.my_self().'" id="calculette" method="post">'."\n";
echo '<table cellspacing="3" cellpadding="0" bgcolor="'.$fond_calc.'" border="0" width="300" align="center">'."\n";
echo "<tbody>\n";
echo "<tr>\n";
echo '<td colspan="6" valign="middle">'."\n";
echo '<input class="calc_read" style="FONT-SIZE: 14pt; WIDTH: 97%; HEIGHT: 30px; " size="25" name="resultat" readonly="readonly"/>'."\n";
echo "</td></tr>\n";
echo '<tr>'."\n";
echo '<td colspan="5" valign="middle" align="center"><font color="#0000ff" size="3">'."\n";
echo '<input onkeydown="if (event.keyCode==13) {enter.click();}" style="FONT-WEIGHT: bold; FONT-SIZE: 10pt; WIDTH: 100%; HEIGHT: 25px" size="15" name="zone_saisie"/></font>'."\n";
echo "</td>\n";
echo '<td bgcolor="'.$fond_calc.'" align="center" valign="middle">&nbsp;<img id="efface" src="'.$chemin_images_icones.$Icones['efface']
	.'" alt="'.$LG_Ch_Calc_Clear.'" title="'.$LG_Ch_Calc_Clear.'" onclick="efface_entree();"/>';
echo "</td></tr>\n";

echo '<tr align="center">'."\n";
$x = touche_chiffre('7','A');
$x = touche_chiffre('8','A');
$x = touche_chiffre('9','A');
$x = touche_chiffre('L','R');
$x = touche_chiffre('M','R');
echo '</tr>'."\n";
echo '<tr align="center">'."\n";
$x = touche_chiffre('4','A');
$x = touche_chiffre('5','A');
$x = touche_chiffre('6','A');
$x = touche_chiffre('X','R');
$x = touche_chiffre('D','R');
echo '</tr>'."\n";
echo '<tr align="center">'."\n";
$x = touche_chiffre('1','A');
$x = touche_chiffre('2','A');
$x = touche_chiffre('3','A');
$x = touche_chiffre('V','R');
$x = touche_chiffre('C','R');
echo '</tr>'."\n";
echo '<tr align="center">'."\n";
$x = touche_chiffre('0','A');
echo '<td colspan="2">&nbsp;</td>'."\n";
$x = touche_chiffre('I','R');
echo '<td>&nbsp;</td>';
echo "</tr>\n";
echo "<tr>\n";
echo '<td colspan="6" align="center">'."\n";

echo '<div id="boutons">'."\n";
echo '<br />';
echo '<table border="0" cellpadding="0" cellspacing="0">'."\n";
echo '<tr><td>';
echo '<div class="buttons">';
echo '<button type="submit" class="positive" '.
	   	 	'onclick="conversion();return false;"> '.
	        '<img src="'.$chemin_images_icones.$Icones['conversion'].'" alt=""/>Conversion</button>';echo '</div>';
echo '</td></tr>';
echo '</table>'."\n";
echo '</div>'."\n";

echo "</td></tr>\n";
echo '</tbody>';
echo "</table>\n";
echo "</form>\n";

Insere_Bas($compl);
?>
</body>
</html>