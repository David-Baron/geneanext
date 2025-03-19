<script type="text/javascript">
<!--

// Javascript spécialisé pour l'édition des personnes

// Ajoute la ville saisie à la fin des listbox
function ajoute1() {
	inverse_div('id_div_ajout1');
	nouv_val = document.forms.saisie.nouvelle_ville1.value;
	document.forms.saisie.nouvelle_ville1.value = "";
	Insert_Sel_1_2("Ville_NaissanceP", "Ville_DecesP", nouv_val);
}

function ajoute2() {
	inverse_div('id_div_ajout2');
	nouv_val = document.forms.saisie.nouvelle_ville2.value;
	document.forms.saisie.nouvelle_ville2.value = "";
	Insert_Sel_1_2("Ville_DecesP", "Ville_NaissanceP", nouv_val);
}

// Ajoute le nom saisi dans la liste des noms de famille
function ajoute_nom() {
  nouv_text = document.forms.saisie.nouveau_nom.value;
  nouv_val = '0/' + nouv_text;
  document.forms.saisie.NomP.value = nouv_val;
  nouvel_element = new Option(nouv_text,nouv_val,false,true);
  //document.forms.saisie.idNomP.options[document.forms.saisie.idNomP.length] = nouvel_element;
  document.forms.saisie.NomSel.options[document.forms.saisie.NomSel.length] = nouvel_element;
  document.forms.saisie.nouveau_nom.value = "";
  inverse_div('id_div_ajout_nom');
}

// Reprend le nom saisi précédemment
function reprend_nom() {
	nouv_text = document.forms.saisie.Nom_Prec.value;
	document.forms.saisie.NomP.value = nouv_text;
	document.forms.saisie.NomSel.value = nouv_text;
}

// Reprend la ville de naissance saisie précédemment
function reprend_villeN() {
  document.forms.saisie.Ville_NaissanceP.value = document.forms.saisie.VilleN_Prec.value;
}

// Reprend la ville de décès saisie précédemment
function reprend_villeD() {
	document.forms.saisie.Ville_DecesP.value = document.forms.saisie.VilleD_Prec.value;
}

// Positionne le numéro 1 : decujus au sens Sosa
function decujus() {
	document.getElementById("NumeroP").value = "1";
}

// Met le nom en majuscules
function NomMaj() {
	document.forms.saisie.nouveau_nom.value = document.forms.saisie.nouveau_nom.value.toUpperCase();
}
 
//-->
</script> 