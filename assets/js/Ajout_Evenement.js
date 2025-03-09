<script type="text/javascript">
<!--

// Javascript spécialisé pour l'ajout rapide d'évènement
// appelé sur les boutons + et -

// Ajoute une ligne à la table des évènements
function addRowToTable() {

	var tbl = document.getElementById('tblSample');
	var lastRow = tbl.rows.length;
	// if there's no header row in the table, then iteration = lastRow + 1
	var iteration = lastRow - 1;
	var row = tbl.insertRow(lastRow);
	//row.class = "actif cotis-ok";
	var num_cell = -1;

	// Type
	num_cell++;
	var cell_2 = row.insertCell(num_cell);
	var sel = document.createElement('select');
	sel.name = 'Type_' + iteration;
	sel.id = 'Type_' + iteration;
	<?php
		// Dans les options, on met la liste des types disponibles
	    for ($nb=0;$nb<count($libelles_types);$nb++) {
	    	echo 'sel.options['.$nb.'] = new Option(\''.addslashes($libelles_types[$nb]).'\', \''.$id_types[$nb].'\');';
	    }
	?>
	cell_2.appendChild(sel);

	// Titre
	num_cell++;
	var cell_3 = row.insertCell(num_cell);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'Titre_' + iteration;
	el.id   = 'Titre_' + iteration;
	el.size = 50;
	cell_3.appendChild(el);

}

// Enlève une ligne de la table des évènements
function removeRowFromTable() {
  var tbl = document.getElementById('tblSample');
  var lastRow = tbl.rows.length;
  if (lastRow > 2) tbl.deleteRow(lastRow - 1);
}

//-->
</script>