<script type="text/javascript">
<!--

// JavaScript pour Demarrage_Rapide et Prop_Design
// qui ont les fonctions communes de pré-visualisation

function change_barre(col_barre) {
	document.getElementById('barre').style.backgroundImage = "url('"+col_barre+"')";
	document.getElementById('image_copie_barres').src = col_barre;
}

function change_fond(col_fond) {
	document.getElementById('fond').style.backgroundImage="url('"+col_fond+"')";
	document.getElementById('image_copie_fonds').src = col_fond;
	document.getElementById('Sel_fonds').value = col_fond;
}

function change_lettre(col_lettre) {
	document.getElementById('lettre').src = col_lettre;
	document.getElementById('image_copie_lettres').src = col_lettre;
	document.getElementById('Sel_lettres').value = col_lettre;
}

function change_couleur(element,couleur) {
	document.getElementById(element).style.backgroundColor = couleur;
}

// Remet la couleur d'origine
function remet() {
	var valeur = document.forms.saisie.CouleurAff.value;
	document.forms.saisie.LaCouleur.style.backgroundColor = valeur;
	document.forms.saisie.LaCouleur.value = valeur;
}

function remet_coul(nouvelle,ancienne) {
	var valeur = document.getElementById(ancienne).value;
	document.getElementById(nouvelle).style.backgroundColor = valeur;
	document.getElementById(nouvelle).value = valeur;
}

function copie_code_coul(cible,valeur) {
	var la_val = valeur.substr(1,6);
	document.getElementById(cible).value = la_val;
}

function remet_code_coul(cible) {
	var la_source = 'Anc_'+cible;
	var la_cible = 'Nouv_'+cible;
	//window.alert(la_source+' > '+' > '+cible);
	var la_val = document.getElementById(la_source).value;
	document.getElementById(la_cible).style.backgroundColor = la_val;
	la_val = la_val.substr(1,6);
	//window.alert(la_source+' > '+la_val+' > '+cible);
	document.getElementById(la_cible).value = la_val;
}

//-->
</script> 

