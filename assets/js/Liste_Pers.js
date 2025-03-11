<script type="text/javascript">

<!--

function updatePersonnes(Nom) {
	xhr.open('get', 'rpc_Personne.php?idNomFam=' + Nom);
	xhr.onreadystatechange = handleResponse;
	xhr.send(null);
}

function updateLiensIcones(ref) {
	//window.alert(ref);
	la_page = get_filename(document.getElementById('page').value);
	if (la_page == 'Liste_Pers.php') {
		document.getElementById("icone_visu").href="Fiche_Fam_Pers.php?Refer="+ref;
		document.getElementById("icone_modif").href="Edition_Personne.php?Refer="+ref;
	}
	if (la_page == 'Liste_Docs_Branche.php') {
		document.getElementById("num_ref").value=ref;
	}
}

function handleResponse() {
	if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
		// Récupération de la liste des évènements
		var data = xhr.responseXML.getElementsByTagName('personnes');
		document.getElementById('personnes').innerHTML = '';
		for(var i=0;i<data.length;i++) {
			// window.alert(data[i].getAttribute("id"));
			var option = document.createElement('option');
			option.setAttribute('value',data[i].getAttribute("id"));
			option.appendChild(document.createTextNode(data[i].firstChild.nodeValue));
			document.getElementById('personnes').appendChild(option);
		}
		//if (data.length == 1) 
		updateLiensIcones(data[0].getAttribute("id"));
		//window.alert(document.getElementById('personnes').innerHTML);
	}
}

function initForm() {
	if (document.getElementById('creation').value == 'o') {
		document.getElementById('noms').selectedIndex = 0;
		updatePersonnes(document.getElementById('noms').value);
	}
}

if (window.addEventListener) {
	window.addEventListener("load", initForm, false);
} else if (window.attachEvent){
	window.attachEvent("onload", initForm);
}

var xhr = getXMLHttpRequest();

//-->
</script>