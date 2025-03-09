<script type="text/javascript">
<!-- 

// Ajout d'un texte à la fin d'un TextArea
// Paramètres : le texte et le nom du TextArea
function AjoutTxt(LeTxt,nom_TxtArea) {
 document.forms[0].elements[nom_TxtArea].value = document.forms[0].elements[nom_TxtArea].value + LeTxt;
}

// Sur le clic sur l'image URL, on affiche début ou fin de balise dans le TextArea
// Le nom de la zone debUrl est fixe (un seul TextArea autorisé pour le moment...)
function Aj_URL(nom_TxtArea) {
  if (document.forms[0].debUrl.value == 0) {
    document.forms[0].debUrl.value = 1;
    AjoutTxt('[URL]',nom_TxtArea);
  }
  else {
    document.forms[0].debUrl.value = 0;
    AjoutTxt('[/URL]',nom_TxtArea);
  }
}

// On écrit le bloc début fin d'un coup...
function Aj_URL2(nom_TxtArea) {
    AjoutTxt('[URL]Insérez l\'adresse ici...[/URL]',nom_TxtArea);
}

// Affichage d'un message de confirmation sur une demande de suppression
function confirmer(quoi,nom_btsup) {
	//if(confirm("Etes-vous sûr(e) de vouloir supprimer "+quoi+" ?")) return true;
	//else {
	//  nom_btsup.value = '-';
	//  return false;
	//}
	if(confirm("Etes-vous sûr(e) de vouloir supprimer "+quoi+" ?")) {
		document.forms.saisie.supprimer.value = "Supprimer";
		return true;
	}
	else {
		document.forms.saisie.supprimer.value = '-';
		return false;
	}
 }

//ouverture popup centrée de saisie de date
function Calendrier(zone,contenu,zoneaff) {
	var h=450; var w=500;
	var pleft = (screen.width/2)-(w/2);
	var ptop = (screen.height/2)-(h/2);
	var chParam="resizable=no, location=no, menubar=no, directories=no, scrollbars=no, status=no, ";
	chParam+='width='+w+', height='+h+', left='+pleft+', top='+ptop;
	FenCalend=window.open('cal.php?zone='+zone+'&contenu='+contenu+'&zoneaff='+zoneaff, 'FenCalend', chParam);
}
function Calendrier2(zone,zoneaff) {
	var h=450; var w=500;
	var pleft = (screen.width/2)-(w/2);
	var ptop = (screen.height/2)-(h/2);
	var contenu = document.getElementById(zone).value;
	var chParam="resizable=no, location=no, menubar=no, directories=no, scrollbars=no, status=no, ";
	chParam+='width='+w+', height='+h+', left='+pleft+', top='+ptop;
	FenCalend=window.open('cal.php?zone='+zone+'&contenu='+contenu+'&zoneaff='+zoneaff, 'FenCalend', chParam);
}

// Vérification de la présence d'une zone
function verification_zone_oblig(zone) {
	var ko = false;
	//window.alert(zone.name);
	//window.alert(zone.value);
	//window.alert(zone.type);
	if (zone.type == 'select-one') {
		if (zone.value == '-1') {	
			zone.className='absent';
			ko = true;
		}
		else {
			zone.className='oblig';
		}		
	}
	else {
		if (zone.value == '') {	
			zone.className='absent';
			ko = true;
		}
		else {
			zone.className='oblig';
		}
	}
	return ko;
}

function verification_zone_oblig_sel(zone) {
	var ko = false;
	//window.alert(zone.value);
	if (zone.value == 0) {	
		zone.className='absent';
		ko = true;
	}
	else {
		zone.className='oblig';
	}
	return ko;
}

// Vérification des zones obligatoires seulement sur le bouton ok
function verification_form(formulaire,zones) {
	var retour = true;
	var absentes = 0;
	if (formulaire.cache.value == 'ok') {
		var LesZones = zones.split(',');
		for (num = 0; num < LesZones.length; num++) {
			//absentes += verification_zone_oblig(document.getElementById(LesZones[num]));
			absentes += verification_zone_oblig(formulaire[LesZones[num]]);
		}
		if (absentes > 0) {	
			window.alert('Pensez à remplir les champs obligatoires...');
			retour = false;
		}
	}
	return retour;
}

// Contrôle de numéricité d'une zone
function verification_num(zone) {
	var ko = isNaN(zone.value);
	if (ko) {	
		window.alert('Attention, zone non numérique...');
		zone.value = 0;
	}
}

function readURL(input,id_image) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			document.getElementById(id_image).src = e.target.result;
		};
		reader.readAsDataURL(input.files[0]);
	}
}

// Déclaration d'un objet pour Ajax
function getXMLHttpRequest() {
    var xhr = null;
    if (window.XMLHttpRequest || window.ActiveXObject) {
        if (window.ActiveXObject) {
            try {
                xhr = new ActiveXObject("Msxml2.XMLHTTP");
            } catch(e) {
                xhr = new ActiveXObject("Microsoft.XMLHTTP");
            }
        } else {
            xhr = new XMLHttpRequest(); 
        }
    } else {
        alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
        return null;
    }
    return xhr;
}

//-->
</script>