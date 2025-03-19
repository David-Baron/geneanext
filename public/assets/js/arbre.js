var canvas = document.getElementById("canvas6");
var context = canvas.getContext("2d");
var Ecart_Case = 40;
var radius = 5;
var gauche;

context.beginPath();
context.lineWidth="1";

context.font = '12px Calibri';
context.fillStyle = '#000000';
	
function ligne_haute(x, y, w, h) {
	context.moveTo(x+radius, y);
	gauche = x+radius+w-radius-radius;
	context.lineTo(gauche, y);
}	
function ligne_droite(x, y, w, h) {
	context.lineTo(gauche+radius, y+h-radius);	
}
function ligne_basse(x, y, w, h) {
	context.lineTo(x+radius, y+h);
}
function ligne_gauche(x, y, w, h) {
	context.lineTo(x, y+radius);
}

	
// Les paramètres hors radius sont considérés comme pour un rectangle à angles droits
// Types des coins :
// 		"CR" : croqués
// 		"LU" : comme les coins des beurres LU
// 		"LD" : comme les coins des beurres LU mais juste à droite
// 		"BO" : bombés (inverse de croqués)

function Rectangle(x, y, w, h, radius, type_coins) {
	// roundRect(x, y, LargCase, HautCase, 5, texte, type_coins);
	var r = x + w;
	var b = y + h;
	
	// Cf. https://www.w3schools.com/Tags/canvas_arc.asp
	// context.arc(x,y,r,sAngle,eAngle,counterclockwise);
	switch(type_coins) {
		// Angle haut droit "croqué"
		case 'CR': 	context.moveTo(x+radius, y);
					gauche = x+radius+w-radius-radius;
					context.lineTo(gauche, y);
					context.arc(gauche+radius,y, radius, 1*Math.PI,0.5*Math.PI, true); 
					context.lineTo(gauche+radius, y+h-radius);
					context.arc(gauche+radius,y+h, radius, 1.5*Math.PI,1*Math.PI, true); 
					context.lineTo(x+radius, y+h);
					context.arc(x,y+h, radius, 0*Math.PI,1.5*Math.PI, true);
					context.lineTo(x, y+radius);
					context.arc(x,y, radius, 0.5*Math.PI,0*Math.PI, true);
					break;
		// Angle haut droit "beurre LU"
		case 'LU': context.moveTo(x+radius, y);
					gauche = x+radius+w-radius-radius;
					context.lineTo(gauche, y);
					context.arc(gauche+radius,y, radius, 1*Math.PI,0.5*Math.PI, false); 
					context.lineTo(gauche+radius, y+h-radius);
					context.arc(gauche+radius,y+h, radius, 1.5*Math.PI,1*Math.PI, false); 
					context.lineTo(x+radius, y+h);
					context.arc(x,y+h, radius, 0*Math.PI,1.5*Math.PI, false);
					context.lineTo(x, y+radius);
					context.arc(x,y, radius, 0.5*Math.PI,0*Math.PI, false);
					break;
		// Angle haut droit "beurre LU, juste sur la partie droite"
		case 'LD': context.moveTo(x+radius, y);
					gauche = x+radius+w-radius-radius;
					context.lineTo(gauche, y);
					context.arc(gauche+radius,y, radius, 1*Math.PI,0.5*Math.PI, false); 
					context.lineTo(gauche+radius, y+h-radius);
					context.arc(gauche+radius,y+h, radius, 1.5*Math.PI,1*Math.PI, false); 
					context.lineTo(x+radius, y+h);
					context.arc(x+radius,y+h-radius, radius, 0.5*Math.PI,1*Math.PI, false);
					context.lineTo(x, y+radius);
					context.arc(x+radius,y+radius, radius, 1*Math.PI,1.5*Math.PI, false);
					break;
		// Angle haut droit "beurre LG"
		case 'LG': context.moveTo(x+radius, y);
					gauche = x+radius+w-radius-radius;
					context.lineTo(gauche, y);
					context.arc(gauche,y+radius, radius, 1.5*Math.PI,0*Math.PI, false); 
					context.lineTo(gauche+radius, y+h-radius);
					context.arc(gauche,y+h-radius, radius, 0*Math.PI,0.5*Math.PI, false);
					context.lineTo(x+radius, y+h);
					context.arc(x,y+h, radius, 0*Math.PI,1.5*Math.PI, false);
					context.lineTo(x, y+radius);
					context.arc(x,y, radius, 0.5*Math.PI,0*Math.PI, false);
					break;
		// Angle haut droit "bombé"
		case 'BO': 	context.moveTo(x+radius, y);
					gauche = x+radius+w-radius-radius;
					context.lineTo(gauche, y);
					context.arc(gauche,y+radius, radius, 1.5*Math.PI,0*Math.PI, false); 
					context.lineTo(gauche+radius, y+h-radius);
					context.arc(gauche,y+h-radius, radius, 0*Math.PI,0.5*Math.PI, false); 
					context.lineTo(x+radius, y+h);
					context.arc(x+radius,y+h-radius, radius, 0.5*Math.PI,1*Math.PI, false);
					context.lineTo(x, y+radius);
					context.arc(x+radius,y+radius, radius, 1*Math.PI,1.5*Math.PI, false);
					break;
	}
	context.stroke();
}

function couple_sav(x,y) {
	var y_Base = y;
	var x_Base = x;
	var y_Base_Trait = y_Base+(HautCase / 2);
	roundRect(x_Base, y_Base, LargCase, HautCase, radius);
	context.moveTo(x_Base+LargCase, y_Base_Trait);
	context.lineTo(x_Base+LargCase+Ecart_Case, y_Base_Trait);
	context.stroke();
	x_Base += LargCase+Ecart_Case;
	roundRect(x_Base, y_Base, LargCase, HautCase, radius);
}

function aff_case(x, y, LargCase, HautCase, radius, texte, couleur, type_coins) {
	context.beginPath();
	context.strokeStyle = couleur;
	Rectangle(x, y, LargCase, HautCase, 5, type_coins);
	context.stroke();
	context.textAlign = "center";
	context.fillText(texte, x + (LargCase/2) + 5 , y + (HautCase / 2));
}

// x1 : abcisse rectangle 1
// x2 : abcisse rectangle 2
// y  : ordonnée rectangles
function couple(x1,x2,y, info1, info2) {
	
	var y_Base_Trait = y + (HautCase / 2);
	
	aff_case(x1, y, LargCase, HautCase, radius, info1, 'blue', "LD");
	aff_case(x2, y, LargCase, HautCase, radius, info2, 'red', "LG");
	
	context.beginPath();
	context.moveTo(x1+LargCase, y_Base_Trait);
	context.lineTo(x2, y_Base_Trait);
	context.stroke();
	
}

function deux_couples() {
	
	var abcisse = 10;
	var ordonnee = 10;
	var abs1 = abcisse;
	
	var x1a = abcisse;
	var x2a = x1a+LargCase+Ecart_Case;
	couple(x1a, x2a, ordonnee, Info_Pere_Pere, Info_Mere_Pere);
	
	var x1b = x1a + (LargCase*2) + Ecart_Case + 20;
	var x2b = x1b + LargCase + Ecart_Case;
	couple(x1b, x2b, ordonnee, Info_Pere_Mere, Info_Mere_Mere);
	
	ordonnee = ordonnee + (HautCase * 2);
	var x1c = (x2a-x1a+Ecart_Case)/2;
	var x2c = (x2b-x1b+Ecart_Case)/2 + x1b;
	couple(x1c, x2c, ordonnee, Info_Pere, Info_Mere);
	
	ordonnee = ordonnee + (HautCase * 2);
	//x1c ==> x2c
	var gauche = (x1c+x2c)/2;
	aff_case(gauche, ordonnee, LargCase, HautCase, radius, Info_Pers, 'blue', "CR");
}

function deux_couples_inv() {
	
	var abcisse = 10;
	var ordonnee = 10;
	var abs1 = abcisse;
	
	var x1a = abcisse;
	var x2a = x1a+LargCase+Ecart_Case;
	couple(x1a, x2a, ordonnee, Info_Pere_Pere, Info_Mere_Pere);
	
	var x1b = x1a + (LargCase*2) + Ecart_Case + 20;
	var x2b = x1b + LargCase + Ecart_Case;
	couple(x1b, x2b, ordonnee, Info_Pere_Mere, Info_Mere_Mere);
	
	ordonnee = ordonnee + (HautCase * 2);
	var x1c = (x2a-x1a+Ecart_Case)/2;
	var x2c = (x2b-x1b+Ecart_Case)/2 + x1b;
	couple(x1c, x2c, ordonnee, Info_Pere, Info_Mere);
	
	ordonnee = ordonnee + (HautCase * 2);
	//x1c ==> x2c
	var gauche = (x1c+x2c)/2;
	aff_case(gauche, ordonnee, LargCase, HautCase, radius, Info_Pers, 'blue', "CR");
}