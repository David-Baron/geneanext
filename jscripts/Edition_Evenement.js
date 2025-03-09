<script type="text/javascript">

// Javascript spécialisé pour l'édition des évènements ou l'import CSV des évènements

<!--
function copieDate() {
	document.forms.saisie.dFinAff.value = document.forms.saisie.dDebAff.value;
	document.forms.saisie.dFinCache.value = document.forms.saisie.dDebCache.value;
}

function Appelle_Zone() {
	// Sélection d'une ville par défaut
	var niveau = 4;
	for (var i=0; i<document.forms.saisie.idNiveauF.length;i++) {
		if (document.forms.saisie.idNiveauF[i].checked) {
		niveau = document.forms.saisie.idNiveauF[i].value;
		}
	}
	if (niveau != 0) x = Zone_Geo('zoneAff','idZoneF',document.forms.saisie.idZoneF.value,niveau);
}

// Ouverture d'une PopUp de saisie de zone géographique
function Zone_Geo(zoneLib,zoneValue,valZone,valNiveau) {
	//var h=40; var w=430;
	var h=200; var w=430;
	var chParam="resizable=no, location=no, menubar=no, directories=no, scrollbars=no, status=no, ";
	PopupCentrer('sel_zone_geo.php?zoneLib='+zoneLib+'&zoneValue='+zoneValue+'&valZone='+valZone+'&valNiveau='+valNiveau,w,h, chParam);
}

function Appelle_Zone_Lect() {
	// Sélection d'une ville par défaut
	var niveau = 4;
	for (var i=0; i<document.forms.saisie.idNiveauF.length;i++) {
		if (document.forms.saisie.idNiveauF[i].checked) {
		niveau = document.forms.saisie.idNiveauF[i].value;
		}
	}
	if (niveau != 0) {
		var h=40; var w=430;
		var chParam="resizable=no, location=no, menubar=no, directories=no, scrollbars=no, status=no, ";
		PopupCentrer('sel_zone_geo.php?zoneLib=zoneAff&zoneValue=idZoneF&valZone='+document.forms.saisie.idZoneF.value+'&valNiveau='+niveau+'&modif=N',
						w,h, chParam);
	}
}

function cache_image_zone() {
	var img = document.getElementById("img_zone");
	img.style.visibility = 'hidden';
	img.style.display = 'none';
	document.getElementById("zoneAff"). value = "";
}

function montre_image_zone() {
	var img = document.getElementById("img_zone");
	img.style.visibility = 'visible';
	img.style.display = 'inline';
}
//-->
</script> 