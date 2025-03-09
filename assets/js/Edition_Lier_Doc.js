<script type="text/javascript">

// Javascript spécialisé pour la liaison d'un document avec un objet

<!--
var debug = false;

function updateDocs(type_doc) {
	var lien = 	'rpc_Document.php?type_doc=' + type_doc +
								'&typeObjet=' + document.getElementById('typeObjet').value +
								'&refObjet=' + document.getElementById('refObjet').value;
	if (debug) window.alert(lien);
	xhr.open('get', lien);
	xhr.onreadystatechange = handleResponse;
	xhr.send(null);
}

function handleResponse() {
	if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
		if (debug) window.alert(xhr.responseText);
		// Récupération de la liste des évènements
		var data = xhr.responseXML.getElementsByTagName('refDoc');
		document.getElementById('refDoc').innerHTML = '';
		if (debug) window.alert(data.length);
		for(var i=0;i<data.length;i++) {
			var option = document.createElement('option');
			option.setAttribute('value',data[i].getAttribute("id"));
			option.appendChild(document.createTextNode(data[i].firstChild.nodeValue));
			document.getElementById('refDoc').appendChild(option);
		}
		// Récupération du maxi des évènements
		var data2 = xhr.responseXML.getElementsByTagName('maxi');
		if (debug) window.alert(data2[0].firstChild.nodeValue);
		document.getElementById('maxi').value = data2[0].firstChild.nodeValue;
	}
}

function initForm() {
	document.getElementById('type_doc').selectedIndex = 0;
	updateDocs(document.getElementById('type_doc').value);
}

if (window.addEventListener) {
	window.addEventListener("load", initForm, false);
} else if (window.attachEvent){
	window.attachEvent("onload", initForm);
}

var xhr = getXMLHttpRequest();

//-->
</script>