<script type="text/javascript">
<!--
<?php
echo "var def_enc = '".strtolower($def_enc)."';";
echo "var name_exists = '".LG_CTRL_NAME_EXISTS."';";
?>
var messages = new Array(
			"b<b>a<\/b>teau, voil<b>&agrave;<\/b>",				//	a
			"<b>&acirc;<\/b>me",									//	â
			"p<b>e<\/b>tit",										//	e
			"ann<b>&eacute;<\/b>e, <b>e<\/b>fficace",				//	é
			"m<b>&ecirc;<\/b>me, s<b>ei<\/b>ze, p<b>&egrave;<\/b>re, pal<b>ai<\/b>s, s<b>e<\/b>rvir",	//	ê, è
			"g<b>î<\/b>te, <b>i<\/b>nou<b>&iuml;<\/b>, g<b>y<\/b>pse, mou<b>ill<\/b>er",	//	i
			"m<b>o<\/b>de",										//	o
			"b<b>eau<\/b>, c<b>&ocirc;<\/b>te, <b>au<\/b>t<b>o<\/b>mate",		//	ô
			"m<b>u<\/b>sique, s<b>û<\/b>r",						//	u
			"n<b>eu<\/b>f, b<b>&oelig;u<\/b>f, <b>&oelig;<\/b>il, d<b>eu<\/b>x, acc<b>ue<\/b>il",	//	eu
			"v<b>en<\/b>t, <b>an<\/b>ge, ch<b>am<\/b>bre, tr<b>em<\/b>per",		//	an, en
			"b<b>on<\/b>jour, <b>om<\/b>bre",						//	on
			"d<b>ou<\/b>x, b<b>o<\/b>ire, g<b>o&ucirc;<\/b>ter",	//	ou
			"v<b>in<\/b>, cr<b>ain<\/b>tif, p<b>ein<\/b>dre, s<b>yn<\/b>dic",		//	in
			"parf<b>um<\/b>, br<b>un<\/b>",						//	un
			"<b>b<\/b>&eacute;<b>b<\/b>&eacute;",					//	b
			"<b>d<\/b>onc",										//	d
			"<b>f<\/b>oule, gra<b>ph<\/b>ique",					//	f
			"<b>g<\/b>omme, <b>gu<\/b>et",						//	g
			"<b>j<\/b>our, <b>j<\/b>u<b>g<\/b>e, r&eacute;<b>g<\/b>ion",					//	j
			"<b>c<\/b>oup, <b>k<\/b>oala, <b>qu<\/b>oi",			//	k
			"<b>l<\/b>oin",										//	l
			"<b>m<\/b>aison",									//	m
			"<b>n<\/b>on",										//	n
			"<b>p<\/b>our<b>p<\/b>re",							//	p
			"<b>r<\/b>ose",										//	r
			"pa<b>ss<\/b>e, <b>c<\/b>erise, fa<b>&ccedil;<\/b>on, atten<b>t<\/b>ion",	//	s
			"<b>t<\/b>aureau",									//	t
			"<b>v<\/b>oix",										//	v
			"<b>z<\/b>oo, ro<b>s<\/b>e, deu<b>x<\/b>i&egrave;me",	//	z
			"<b>ch<\/b>emin",									//	ch
			"a<b>gn<\/b>eau"										//	gn
			);
var tabSons = new Array();
var posCurseur = 0;

//	Affiche un message pour expliquer quel est le son qui correspond à un bouton
function afficheAide(numAide)
{
	texte = "";
	if (numAide >= 0) 
	{
		texte = messages[numAide];
	}
	document.getElementById('aide').innerHTML = texte;
}

//	Ajoute un son à la liste
function ajoute(parSon)
{
	tabSons.splice(posCurseur , 0 , parSon);
	posCurseur ++;
	afficherPhonetique();
}

//	Traitement du code reçu lors de l'affichage initial de la page
function traiteCodeRecu(code)
{
	tabSons = code.split("-");
	posCurseur = tabSons.length;
	afficherPhonetique();
}

//	Appelle le script PHP pour calculer un code phonétique à partir d'un nom de famille
function calculer()
{
	var demandeDistante = null;
	//	Création de l'objet traitant la demande
	if(window.XMLHttpRequest)		// Firefox
		demandeDistante = new XMLHttpRequest();
	else if(window.ActiveXObject)	// Internet Explorer
		demandeDistante = new ActiveXObject("Microsoft.XMLHTTP");
	else
	{
		alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
		return true;
	}
	//	Exécution de la demande
	demandeDistante.open("POST", "codage_nomFam.php", false);
	demandeDistante.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
	nomSaisi = document.getElementById('nomFam').value;
	nomSaisi = escape(nomSaisi);
	//
	demandeDistante.send("nom=" + nomSaisi);
	//	Réponse à la demande
	traiteCodeRecu(demandeDistante.responseText);
}

//	Retire le dernier son de la liste
function efface()
{
	posCurseur -=1;
	tabSons.splice(posCurseur, 1);
	afficherPhonetique();
}

//	Déplacement du curseur vers la droite
function cursDroite()
{
	if (posCurseur < tabSons.length)
	{
		posCurseur ++;
		afficherPhonetique();
	}
}

//	Déplacement du curseur vers la gauche
function cursGauche()
{
	if (posCurseur > 0)
	{
		posCurseur -=1;
		afficherPhonetique();
	}
}

//	Affichage de la phonétique du nom de famille
function afficherPhonetique()
{
	//	Calcul de la position du curseur
	posCaractere = 0;
	for (indice = 0 ; indice < posCurseur ; indice++)
	{
		posCaractere += tabSons[indice].length + 1;
	}
	
	phonetique = tabSons.join('-');
	phonetique = phonetique.replace(/ /g , '_');
	//	Insertion du curseur
	phonetique = phonetique.substring(0 , posCaractere) + "|" + phonetique.substring(posCaractere , phonetique.length);
	document.getElementById('code').innerHTML = phonetique;
	// window.alert(phonetique);
	document.getElementById('codePho').value = phonetique.slice(0, -1);
}

// Met le nom en majuscules
function NomMaj()
{
	var zone = document.getElementById('nomFam');
	zone.value = zone.value.toUpperCase();
	zone.focus();
}
//	Vérification des zones obligatoires
//	Varification si le nom existe deja dans la base ou non
function verification_form_nomFam(formulaire,zones) {

	if (document.getElementById('cache').value == 'ok') {

		if (verification_form(formulaire,zones) == false) {
			return false;
		}
		//	Contrôle dans la base
		var demandeDistante = null;
		//	Création de l'objet traitant la demande
		if(window.XMLHttpRequest)		// Firefox
			demandeDistante = new XMLHttpRequest();
		else if(window.ActiveXObject)	// Internet Explorer
			demandeDistante = new ActiveXObject("Microsoft.XMLHTTP");
		else {
			alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");
			return true;
		}
		//	Paramètres de la demande
		var nomSaisi = document.getElementById('nomFam').value;
		nomSaisi = escape(nomSaisi);
		var identifiant = document.getElementById('ident_courant').value;
		var parametres = "nom=" + nomSaisi + "&identifiant=" + identifiant;
		var appli = "application/x-www-form-urlencoded;";
		//var appli = "application/x-www-form-urlencoded; charset='"+def_enc+"'";
		//	Exécution de la demande
		demandeDistante.open("POST", "controle_nomFam.php", false);
		demandeDistante.setRequestHeader('Content-type',appli);
		// demandeDistante.setRequestHeader('Content-length', parametres.length);
		// demandeDistante.setRequestHeader('Connection', 'close');
		demandeDistante.send(parametres);
		//    Réponse à la demande
		var reponse = demandeDistante.responseText;
		if (reponse == "OK") {
		   return true;
		}
		if (reponse == "Erreur") {
			return false;
		}
		document.getElementById('fusion').value = 'N';
		if (confirm(name_exists)) {
			document.getElementById('fusion').value = 'O';
			document.getElementById('anc_ident').value = reponse;
			return true;
		}
		else {
			document.getElementById('cache').value = '';
			document.getElementById('ok').value = '';
		}
		return false;
	}
}

//	Action = clic sur le bouton validation
function clicOK()
{
	var doc = document.forms.saisie;
	doc.cache.value='ok';
	doc.codePho.value = tabSons.join('-');
}
// -->
</script>