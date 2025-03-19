<script type="text/javascript">

// Javascript spécialisé pour la liaison d'un évènement avec une personne
<!--

var debug = false;

function updateEvts(types_evt) {
	//window.alert('rpc.php?type_evt=' + types_evt + '&amp;ref=' + document.getElementById('ref').value);
	xhr.open('get', 'rpc_Evenement.php?type_evt=' + types_evt);
	//xhr.open('get', 'rpc.php?type_evt=' + types_evt + '&ref=' + document.getElementById('ref').value);
	xhr.onreadystatechange = handleResponse;
	xhr.send(null);
}

function handleResponse() {
	if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
		if (debug) window.alert(xhr.responseText);
		// Récupération de la liste des évènements
		var data = xhr.responseXML.getElementsByTagName('evenements');
		document.getElementById('evenements').innerHTML = '';
		if (debug) window.alert(data.length);
		for(var i=0;i<data.length;i++) {
			var option = document.createElement('option');
			option.setAttribute('value',data[i].getAttribute("id"));
			option.appendChild(document.createTextNode(data[i].firstChild.nodeValue));
			document.getElementById('evenements').appendChild(option);
		}
		// Récupération du maxi des évènements
		var data2 = xhr.responseXML.getElementsByTagName('maxi');
		if (debug) window.alert(data2[0].firstChild.nodeValue);
		document.getElementById('maxi').value = data2[0].firstChild.nodeValue;
	}
}

function initForm() {
	if (document.getElementById('types_evt') != null) {
		document.getElementById('types_evt').selectedIndex = 0;
		updateEvts(document.getElementById('types_evt').value);
	}
}

if (window.addEventListener) {
	window.addEventListener("load", initForm, false);
} else if (window.attachEvent){
	window.attachEvent("onload", initForm);
}

function dDebPart() {
  x=Calendrier('dDebCache',document.forms.saisie.dDebCache.value,'dDebAff');
}

function dFinPart() {
  x=Calendrier('dFinCache',document.forms.saisie.dFinCache.value,'dFinAff');
}

function sel_der() {
  document.forms.saisie.refEveF.value = document.forms.saisie.refMax.value;
}

function copieDate() {
  document.forms.saisie.dFinAff.value = document.forms.saisie.dDebAff.value;
  document.forms.saisie.dFinCache.value = document.forms.saisie.dDebCache.value;
}

function Appelle_Zone() {
  // Sélection d'une ville par défaut
  niveau = 4;
  for (var i=0; i<document.forms.saisie.idNiveauF.length;i++) {
    if (document.forms.saisie.idNiveauF[i].checked) {
      niveau = document.forms.saisie.idNiveauF[i].value;
    }
  }
  if (niveau != 0) x = Zone_Geo('zoneAff','idZoneF',document.forms.saisie.idZoneF.value,niveau);
}

// Ouverture d'une PopUp de saisie de zone géographique
function Zone_Geo(zoneLib,zoneValue,valZone,valNiveau) {
	var h=200; var w=500;
	var chParam="resizable=no, location=no, menubar=no, directories=no, scrollbars=no, status=no, ";
	PopupCentrer('sel_zone_geo.php?zoneLib='+zoneLib+'&zoneValue='+zoneValue+'&valZone='+valZone+'&valNiveau='+valNiveau,w,h, chParam);

}

function cache_image_lieu(image) {
	var img = document.getElementById(image);
	img.style.visibility = 'hidden';
	img.style.display = 'none';
}

// Montre une image
function montre_image_lieu(image) {
	var img = document.getElementById(image);
	img.style.visibility = 'visible';
	img.style.display = 'inline';
}

var xhr = getXMLHttpRequest();

//-->
</script> 