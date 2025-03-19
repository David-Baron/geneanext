<script type="text/javascript">
<!--
// Vérification des zones obligatoires seulement sur le bouton ok
function verification_form(formulaire)
{
	var mes = '';
	if (formulaire.cache.value == 'ok')
	{
		if (formulaire.Titre.value == '')
		{
			mes = 'Veuillez renseigner le titre du document';
			formulaire.Titre.className='absent';
		}
		else
		{
			formulaire.Titre.className='oblig';
		}
		//	Le champ de type FILE ne permet pas d'afficher la valeur déjà présente
		//	On force l'ancienne valeur
		if (formulaire.nom_du_fichier.value == '')
		{
			formulaire.nom_du_fichier.value = formulaire.ANomFic.value;	
		}

		if ((formulaire.nom_du_fichier.value == '') && (formulaire.ANomFic.value == ''))
		{	
			formulaire.nom_du_fichier.className='absent';
			if (mes != '')
				mes += '\n';
			mes += "Veuillez choisir un fichier";
		}
		else
		{
			formulaire.nom_du_fichier.className='oblig';
		}
		if (mes != '')
		{
			window.alert(mes);
			return false;
		}
	}
	return true;
}

//-->
</script> 