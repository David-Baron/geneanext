<script type="text/javascript">
<!--
// Ouverture d'une fenêtre
function PopupCentrer(page,largeur,hauteur,options) {
	var top=(screen.height-hauteur)/2;
	var left=(screen.width-largeur)/2;
	window.open(page,"","top="+top+",left="+left+",width="+largeur+",height="+hauteur+","+options);
}

function ret_prec() {
  location.href="<?php echo $Horigine ?>";
}

function checkUncheckAll(theElement) {
	var theForm = theElement.form;
	for (i = 0; i < theForm.elements.length; i++) {
		if (theForm.elements[i].type == 'checkbox' && theForm.elements[i].name != 'selTous') {
			theForm.elements[i].checked = theElement.checked;
		}
	}
}

// Cache ou affiche un div
function inverse_div(id_div) {
	if (document.getElementById(id_div).style.visibility == 'hidden') montre_div(id_div);
	else cache_div(id_div);
}

// En fonction du comportement autorisé, on va inverser la visibilité du div
function Survole_Clic_Div(id_div,evenement,comportement) {
	if (
		((evenement == 'CL') && (comportement == 'C'))
		||
		((evenement == 'MO') && (comportement == 'S'))
		)
		inverse_div(id_div);
}

function cache_div(id_div) {
	document.getElementById(id_div).style.visibility = 'hidden';
	document.getElementById(id_div).style.display='none';
}

function montre_div(id_div) {
	document.getElementById(id_div).style.visibility = 'visible';
	document.getElementById(id_div).style.display='block';
}

// Un nombre est-il pair ?
function pair(nombre) {
	return ((nombre-1)%2);
}

// Etend le numéro Sosa s'il commence par '='
// Fonctions supportées : père, mère, enfant ; le tout en ligne directe d'ascendance
function etend_num_sosa() {
	var contenu = document.getElementById("NumeroP").value;
	var l_contenu = contenu.length;
	if (l_contenu > 1) {
	var num2 = 0;
	var car1 = contenu.charAt(0);
	if (car1 == "=") {
		car1 = contenu.charAt(1);
		car1 = car1.toUpperCase();
		var le_num = parseInt(contenu.substring(2,l_contenu));
		if (le_num != 'NaN') {
			switch(car1) {
				case 'P' : num2 = le_num * 2; break;
				case 'M' : num2 = le_num * 2 + 1; break;
				case 'E' : num2 = parseInt(le_num / 2); break;
				case 'C' : if (pair(le_num)) num2 = le_num +1;
				           else              num2 = le_num -1;
				           break;
				default:  break;
			}
			if (num2 != 0) document.getElementById("NumeroP").value = num2;
		}
    }
  }
}

// Ajout d'une valeur dans 2 select
// Dans le 1er, la valeur est ajoutée à la fin, dans le 2ème, la valeur est insérée triée
// Pas réussi à trier dans le 1er en affichant la valeur en retour ; select OK mais valeur non affichée dans FF 20 ;-(
function Insert_Sel_1_2(NomSel1, NomSel2, nouv_val) {
	var n_sel = document.getElementById(NomSel1);
	n_sel.options[n_sel.length] = new Option(nouv_val,nouv_val,false,true);
	var n_sel = document.getElementById(NomSel2);
	var i = n_sel.length, j;
	var L_Nouv = nouv_val.toLowerCase();
	/*while( imoinsmoins- && ( n_sel[i].text.toLowerCase() > L_Nouv || i + 1 === n_sel.length ) ) {*/
	while( i && ( n_sel[i-1].text.toLowerCase() > L_Nouv || i === n_sel.length ) ) {
		i -= 1;
		j = n_sel[i];
		n_sel[i + 1] = new Option( j.text, j.value, j.defaultSelected, j.selected );
	}
	n_sel[i + 1] = new Option( nouv_val, nouv_val, false, false );
}

function get_filename(fullname) {
    return fullname.split('\\').pop().split('/').pop();
}

function efface_entree_date() {
	document.getElementById("decal_Mois" ).value = "00";
	document.getElementById("decal_Annee" ).value = "0000";
	document.getElementById("rMois").value = "";
	document.getElementById("rAnnee").value = "";
}

function moins_date() {
	var dMois = parseInt(document.getElementById("dMois" ).value,10);
	var dAnnee = parseInt(document.getElementById("dAnnee" ).value,10);
	var decal_Mois = parseInt(document.getElementById("decal_Mois" ).value,10);
	var decal_Annee = parseInt(document.getElementById("decal_Annee" ).value,10);
	var rAnnee = dAnnee - decal_Annee;
	if (dMois > decal_Mois) {
		var rMois = dMois - decal_Mois;
	} else {
		rAnnee -= 1;
		dMois += 12;
		var rMois = dMois - decal_Mois;
	}
	document.getElementById("rMois" ).value = rMois;
	document.getElementById("rAnnee" ).value = rAnnee;
	return(true);
}

function plus_date() {
	var dMois = parseInt(document.getElementById("dMois" ).value,10);
	var dAnnee = parseInt(document.getElementById("dAnnee" ).value,10);
	var decal_Mois = parseInt(document.getElementById("decal_Mois" ).value,10);
	var decal_Annee = parseInt(document.getElementById("decal_Annee" ).value,10);
	var rAnnee = dAnnee + decal_Annee;
	var rMois = dMois + decal_Mois;
	if (rMois > 12) {
		rMois -= 12;
		rAnnee += 1;
	}
	document.getElementById("rMois" ).value = rMois;
	document.getElementById("rAnnee" ).value = rAnnee;
	return(true);
}

function bascule_image(image) {
	var etat = document.getElementById(image).style.display;
	var img = document.getElementById(image);
	if (etat == 'inline') {
		img.style.visibility = 'hidden';
		img.style.display = 'none';
	}
	else {
		img.style.visibility = 'visible';
		img.style.display = 'inline';
	}
}

// Cache une image
// function cache_image(image) {
	// var img = document.getElementById(image);
	// img.style.visibility = 'hidden';
	// img.style.display = 'none';
// }

// Montre une image
// function montre_image(image) {
	// var img = document.getElementById(image);
	// img.style.visibility = 'visible';
	// img.style.display = 'inline';
// }

// Appel d'une carte OpenStreetMap
function aff_openstreetmap(latitude,longitude) {
	var h=500; var w=700;
	var chParam="resizable=yes, location=no, menubar=no, directories=no, scrollbars=yes, status=no, ";
	chParam+='width='+w+', height='+h;
	Fenetre=window.open('http://www.openstreetmap.org/?lat='+latitude +'&lon='+longitude +'&mlat='+latitude +'&mlon='+longitude +
						'&zoom=10',
						'_blank', chParam);
}

// Appel d'une carte OpenStreetMap si la latitude ou la longitude sont renseignées
function apelle_carte(zLat, zlong) {
	var latitude = document.getElementById(zLat).value;
	var longitude = document.getElementById(zlong).value;
	if ((longitude != 0) || (latitude != 0)) {
		aff_openstreetmap(latitude,longitude);
	}
}	
//-->
</script>
