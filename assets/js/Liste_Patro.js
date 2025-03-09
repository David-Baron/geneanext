<script type="text/javascript">
<!--

// Cache tous les div
function Cache_Tous() {
	var NumDiv = 1;
	while (document.getElementById('div' + NumDiv)) {
		cache_div('div' + NumDiv);
		NumDiv++;
	}
	document.getElementById("memo_etat").value = "C";
}

// Montre tous les div
function Montre_Tous() {
	var NumDiv = 1;
	while (document.getElementById('div' + NumDiv)) {
		montre_div('div' + NumDiv);
		NumDiv++;
	}
	document.getElementById("memo_etat").value = "M";
}

function Inverse_Tous() {
	if (document.getElementById("memo_etat").value == "C") Montre_Tous();
	else                                                   Cache_Tous();
}

// En fonction du comportement autorisé, on va inverser la visibilité du div
function Survole_Clic_Div_Tous(evenement,comportement) {
  if (
      ((evenement == 'CL') && (comportement == 'C'))
      ||
      ((evenement == 'MO') && (comportement == 'S'))
     )
    Inverse_Tous();
}

//-->
</script>