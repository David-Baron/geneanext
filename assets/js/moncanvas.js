var canvas;
var ctx;
var lastend = 0;


function pie(suffixe,pieColor, pieData) {

	var pieTotal = 0;
	for (var i = 0; i < pieData.length; i++) {
		pieTotal += pieData[i];
	}
	
	canvas = document.getElementById("canvas"+suffixe);
	ctx = canvas.getContext("2d");

	ctx.clearRect(0, 0, canvas.width, canvas.height);

	var hwidth = ctx.canvas.width/2;
	var hheight = ctx.canvas.height/2;

	for (var i = 0; i < pieData.length; i++) {
		
		ctx.fillStyle = pieColor[i];
		ctx.beginPath();
		ctx.moveTo(hwidth,hheight);
		ctx.arc(hwidth,hheight,hheight,lastend,lastend+
			(Math.PI*2*(pieData[i]/pieTotal)),false);

		ctx.lineTo(hwidth,hheight);
		ctx.fill();

		//Labels on pie slices (fully transparent circle within outer pie circle, to get middle of pie slice)
		//ctx.fillStyle = "rgba(255, 255, 255, 0.5)"; //uncomment for debugging
		//          ctx.beginPath();
		//          ctx.moveTo(hwidth,hheight);
		//          ctx.arc(hwidth,hheight,hheight/1.25,lastend,lastend+
		//            (Math.PI*(pieData[i]/pieTotal)),false);  //uncomment for debugging 

		var radius = hheight/1.5; //use suitable radius
		var endAngle = lastend + (Math.PI*(pieData[i]/pieTotal));
		var setX = hwidth + Math.cos(endAngle) * radius;
		var setY = hheight + Math.sin(endAngle) * radius;
		if (pieData[i] > 0) {
			ctx.fillStyle = "#ffffff";
			// On écrit le texte en noir
			//ctx.fillStyle = '#000000';
			ctx.font = '14px Calibri';
			ctx.fillText(pieData[i],setX,setY);
		}
		//          ctx.lineTo(hwidth,hheight);
		//ctx.fill(); //uncomment for debugging

		lastend += Math.PI*2*(pieData[i]/pieTotal);
	}
}

// Affichage de la légende
function legende(suffixe,texte, pieData, labels, pourcentage) {
	
	if (pourcentage) {
		var pieTotal = 0;
		for (var i = 0; i < pieData.length; i++) {
			pieTotal += pieData[i];
		}
	}
		
	canvas = document.getElementById("canvas_leg"+suffixe);
	ctx_leg = canvas.getContext("2d");

	ctx_leg.clearRect(0, 0, canvas.width, canvas.height);

	var hwidth = ctx_leg.canvas.width/2;
	var hheight = ctx_leg.canvas.height/2;

	largeur = 20;
	hauteur = 20;

	x = 5;
	y = 5;

	ctx_leg.font = '14px Calibri';

	for (var i = 0; i < pieData.length; i++) {
		ctx_leg.fillStyle = pieColor[i];
		donnee = pieData[i];
		if (donnee > 0) {
			y = y + hauteur + 1;
			pourcent = '';
			if (pourcentage) {
				pourcent = ' ('+Math.round(donnee/pieTotal*100)+' %)';
			}
			ctx_leg.fillRect(x, y, largeur, hauteur);
			// On écrit le texte en noir
			ctx_leg.fillStyle = '#000000';
			ctx_leg.fillText(labels[i] + ' ' + texte + ' : ' + donnee + pourcent,x + largeur  + 10 ,y + 15);
		}
	}
}