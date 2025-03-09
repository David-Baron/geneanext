<script type="text/javascript">

// Javascript spécialisé pour l'édition d'une filiation

<!--
// Reprise du dernier couple saisi
function sel_der() {
  document.forms.saisie.PereP.value = document.forms.saisie.MaxConjoint_1.value;
  document.forms.saisie.MereP.value = document.forms.saisie.MaxConjoint_2.value;
}

function sel_col() {
  mavar = document.forms.saisie.CollatP.value;
  posi  = mavar.indexOf("/");
  if (posi > -1) {
    document.forms.saisie.PereP.value = mavar.substring(0,posi);
    document.forms.saisie.MereP.value = mavar.substring(posi+1,mavar.length);
  }
}

// Reprise de la dernière personne saisie en fonction du sexe
function sel_derP(sexe) {
	if (sexe == 'm') document.forms.saisie.PereP.value = document.forms.saisie.MaxConjoint_1S.value;
	if (sexe == 'f') document.forms.saisie.MereP.value = document.forms.saisie.MaxConjoint_2S.value;
}

// Expurge de la liste des pères les hommes qui ne portent pas le nom
function removeP(nom) {
	var longueur = document.forms.saisie.PereP.length;
	for(i = longueur-1; i > 0; i-=1) {
		var valeur = document.forms.saisie.PereP.options[i].text;
		var posi = valeur.indexOf(nom, 0);
		if (posi != 0) {
			document.forms.saisie.PereP.options[i] = null;
		}
	}
	document.forms.saisie.RP.style.visibility = 'hidden' ;
}

// Expurge de la liste des mères les femmes qui ne portent pas le nom
function removeM(nom) {
	var longueur = document.forms.saisie.MereP.length;
	for(i = longueur-1; i > 0; i-=1) {
		var valeur = document.forms.saisie.MereP.options[i].text;
		var posi = valeur.indexOf(nom, 0);
		if (posi != 0) {
			document.forms.saisie.MereP.options[i] = null;
		}
	}
	document.forms.saisie.RM.style.visibility = 'hidden' ;
}
//-->
</script>