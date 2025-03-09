<?php
//	Codage phonétique des noms de personnes
//	Totalement écrit par Gérard KESTER
//	Février 2009
//
//	Dans les commentaires, je place les lettres entre guilemets ("a")
//		et les sons entre barres obliques (/a/).
//
//	Pour élargir la recherche, vous pouvez faire une comparaison en mettant le code phonétique en minuscules ou en majuscules.
//	Cela permet de rapprocher les sons "a" et "â", les sons "é" et "è",
//		"o" et "ô", "in" et "un", "en" et "on"
class phonetique
{
	//	Mot en cours de traduction
	private $mot;
	//	Code calculé
	private $code;
	//	pointeur sur le mot à coder
	private $indMot;
	//	tableau de lettres
	private $tab1c,$tab2c,$tab3c,$tab4c;
	private $finale2 , $finale3 , $finale4, $finale5;
	private $tabMots;
	private $voyelles , $lettres1;
	private $aou , $eiy;
	//	tableau pour traduction phonétique
	private $phon , $phonR;
	//
	private $trouve;
	//
	private $indesirables;
	public function __construct()
	{
		//	Tableaux de correspondaces lettres => sons
		$this->tab1c = array(
			'a'=>'a','à'=>'a','â'=>'A','ä'=>'E','b'=>'b','c'=>'k','ç'=>'s','d'=>'d','e'=>'E','é'=>'e',
			'è'=>'E','ê'=>'E','ë'=>'E','f'=>'f','g'=>'g','h'=>'' ,'i'=>'i','î'=>'i','ï'=>'i','j'=>'j',
			'k'=>'k','l'=>'l','m'=>'m','n'=>'n','ñ'=>'N','o'=>'O','ö'=>'o','ö'=>'c','ô'=>'o','p'=>'p',
			'q'=>'q','r'=>'r','s'=>'s','t'=>'t','u'=>'u','ù'=>'u','û'=>'u','ü'=>'u','v'=>'v','w'=>'v',
			'y'=>'i','ÿ'=>'i','z'=>'z','á'=>'a','í'=>'i','ó'=>'o','ú'=>'u','ß'=>'s'
			);
		$this->tab2c = array(
			'ae'=>'E' ,'aë'=>'a' ,'ai'=>'E','aî'=>'E' ,'am'=>'q','an'=>'q','au'=>'o'  ,'ch'=>'x','ck'=>'k',
			'ei'=>'E' ,'em'=>'q' ,'en'=>'q','eu'=>'c' ,'eû'=>'c','ey'=>'E','gn'=>'N'  ,'oe'=>'c','oi'=>'wa',
			'oî'=>'wa','oi'=>'Oi','om'=>'Q','on'=>'Q' ,'ou'=>'w','oû'=>'w','oy'=>'wai','ph'=>'f','qu'=>'k',
			'uë'=>'u' ,'um'=>'Y' ,'un'=>'Y','xc'=>'ks','ym'=>'y','yn'=>'y'
			); 
		$this->tab3c = array(
			'aen'=>'q','aën'=>'q','aie'=>'E'  ,'ain'=>'y' ,'aon'=>'q','cch'=>'k','cqu'=>'k','ean'=>'q',
			'eau'=>'o','eim'=>'y','ein'=>'y','gua'=>'gwa','oei'=>'ci','oeu'=>'c','sch'=>'x'
			);
		$this->tab4c = array(
			'eing'=>'y','eint'=>'y','euil'=>'ci','oest'=>'Est'
			);
		//
		//
		$this->finale2 = array(
			'am'=>'am','ap'=>'A','as'=>'A' ,'âs'=>'A','at'=>'A' ,'ât'=>'A','aw'=>'o','ay'=>'E','az'=>'a',
			'be'=>'b' ,'bs'=>'b','ce'=>'s' ,'ck'=>'k','cq'=>'k' ,'cs'=>'k','de'=>'d','ds'=>'d','dt'=>'t',
			'ed'=>'e' ,'ée'=>'e','ef'=>'ef','en'=>'y','er'=>'Er','es'=>'@','és'=>'e','ès'=>'E','et'=>'E',
			'èt'=>'E' ,'ey'=>'E','ez'=>'ez','fe'=>'f','ge'=>'j' ,'gg'=>'g','gs'=>'g','gt'=>' ','ie'=>'i',
			'ïe'=>'i' ,'il'=>'i','is'=>'i' ,'ïs'=>'i','it'=>'i' ,'ît'=>'i','ks'=>'k','le'=>'l','ls'=>'l',
			'me'=>'m' ,'ms'=>'m','ne'=>'n' ,'ns'=>'n','oc'=>'o' ,'od'=>'o','op'=>'o','os'=>'o','ot'=>'o',
			'oy'=>'wa','ôt'=>'o','pe'=>'p' ,'ps'=>'p','qs'=>'k' ,'rc'=>'r','rd'=>'r','re'=>'r','rf'=>'r',
			'rs'=>'r' ,'rt'=>'r','se'=>'z' ,'sh'=>'x','ss'=>'s' ,'te'=>'t','th'=>'t','ts'=>'t','tt'=>'t',
			'ue'=>'u' ,'ul'=>'u','us'=>'u' ,'ûs'=>'u','ut'=>'u' ,'ût'=>'u','ve'=>'v','ye'=>'i','ze'=>'z'
			);
		$this->finale3 = array(
			'acs'=>'A' ,'aid'=>'E' ,'aie'=>'E' ,'ail'=>'ai','aim'=>'y' ,'ais'=>'E' ,'ait'=>'E' ,'aît'=>'E',
			'aix'=>'E' ,'amp'=>'q' ,'anc'=>'q' ,'and'=>'q' ,'ang'=>'q' ,'ans'=>'q' ,'ant'=>'q' ,'aon'=>'q',
			'ard'=>'ar','ars'=>'ar','art'=>'Ar','ats'=>'A' ,'ats'=>'a' ,'aud'=>'o' ,'aus'=>'o' ,'aut'=>'o',
			'aux'=>'o' ,'bbe'=>'b' ,'bes'=>'b' ,'ces'=>'s' ,'cgs'=>'k' ,'che'=>'x' ,'chs'=>'ks','cks'=>'ks',
			'des'=>'d' ,'ect'=>'E' ,'eds'=>'e' ,'ées'=>'e' ,'efs'=>'e' ,'eil'=>'Ei','ein'=>'y' ,'emp'=>'q',
			'end'=>'q' ,'eng'=>'q' ,'ens'=>'q' ,'ent'=>'q' ,'ers'=>'e' ,'ets'=>'E' ,'èts'=>'E' ,'eue'=>'c',
			'eus'=>'c' ,'eux'=>'c' ,'fes'=>'f' ,'ffe'=>'f' ,'ges'=>'j' ,'ggs'=>'g' ,'gne'=>'N' ,'gue'=>'g',
			'hie'=>'i' ,'his'=>'i' ,'ien'=>'iy','ier'=>'ie','ies'=>'i' ,'ïes'=>'i' ,'ils'=>'i' ,'inq'=>'y',
			'ins'=>'y' ,'int'=>'y' ,'înt'=>'y' ,'its'=>'i' ,'khs'=>'k' ,'les'=>'l' ,'lle'=>'l' ,'mes'=>'m',
			'mme'=>'m' ,'mne'=>'m' ,'nes'=>'n' ,'nne'=>'n' ,'oeu'=>'c' ,'oid'=>'wa','ois'=>'wa','oit'=>'wa',
			'oix'=>'wa','omb'=>'Q' ,'omp'=>'Q' ,'oms'=>'Q' ,'onc'=>'Q' ,'ond'=>'Q' ,'omg'=>'Q' ,'ons'=>'Q',
			'ont'=>'Q' ,'ots'=>'o' ,'oud'=>'w' ,'oue'=>'w' ,'oul'=>'w' ,'oup'=>'w' ,'ous'=>'w' ,'out'=>'w',
			'oût'=>'w' ,'oux'=>'w' ,'pes'=>'p' ,'phe'=>'f' ,'ppe'=>'p' ,'que'=>'k' ,'rcs'=>'r' ,'rds'=>'r',
			'rdt'=>'r' ,'res'=>'r' ,'rfs'=>'r' ,'rre'=>'r' ,'rts'=>'r' ,'ses'=>'z' ,'sse'=>'s' ,'the'=>'t',
			'ths'=>'tQ','tte'=>'t' ,'tts'=>'t' ,'ues'=>'u' ,'uls'=>'u' ,'ums'=>'Y' ,'uns'=>'Y' ,'unt'=>'Y',
			'uts'=>'u' ,'ves'=>'v' ,'yes'=>'i' ,'zes'=>'z'
			);
		$this->finale4 = array(
			'aids'=>'E' ,'aies'=>'E' ,'aims'=>'y' ,'ainc'=>'y' ,'aing'=>'y' ,'ains'=>'y' ,'aint'=>'y',
			'aits'=>'E' ,'amps'=>'q' ,'ancs'=>'q' ,'ands'=>'q' ,'angs'=>'q' ,'ants'=>'q' ,'aons'=>'q',
			'auds'=>'o' ,'auld'=>'o' ,'ault'=>'o' ,'aulx'=>'o' ,'auts'=>'o' ,'bbes'=>'b' ,'bent'=>'bq',
			'cent'=>'sq','ches'=>'x' ,'dent'=>'dq','eaux'=>'o' ,'ects'=>'E' ,'eins'=>'y' ,'eint'=>'y' ,
			'emps'=>'q' ,'empt'=>'q' ,'engs'=>'q' ,'ents'=>'q' ,'eues'=>'c' ,'fent'=>'fq','ffes'=>'f' ,
			'gent'=>'jq','gnes'=>'N' ,'gues'=>'g' ,'hies'=>'i' ,'ille'=>'i' ,'inct'=>'y' ,'ingt'=>'y' ,
			'lent'=>'lq','lles'=>'l' ,'ment'=>'mq','mmes'=>'m' ,'mnes'=>'m' ,'nent'=>'nq','nnes'=>'n' ,
			'oeux'=>'c', 'oids'=>'wa','oies'=>'wa','oigt'=>'wa','oint'=>'wy','oist'=>'wa','oits'=>'wa',
			'ombs'=>'Q', 'ompt'=>'Q' ,'oncs'=>'Q' ,'onds'=>'Q' ,'ongs'=>'Q' ,'onts'=>'Q' ,'oues'=>'w' ,
			'ould'=>'w' ,'oult'=>'w' ,'oups'=>'w' ,'oûts'=>'w' ,'pent'=>'pq','phes'=>'f' ,'ppes'=>'p' ,
			'ques'=>'k' ,'rent'=>'rq','rres'=>'r' ,'sent'=>'zq','sses'=>'s' ,'thes'=>'t' ,'ttes'=>'t' ,
			'uent'=>'uq','unts'=>'Y' ,'vent'=>'vq','xent'=>'sq','yent'=>'y','zent'=>'zq'
			);
		$this->finale5 = array(
			'aient'=>'Eq','aille'=>'ai','aincs'=>'y' ,'chent'=>'xq','eault'=>'o' ,'eaulx'=>'o',
			'eints'=>'y' ,'empts'=>'q' ,'ffent'=>'fq','gnent'=>'Nq','guent'=>'gq','illes'=>'i',
			'incts'=>'y' ,'ingts'=>'y' ,'llent'=>'lq','nnent'=>'nq','oeufs'=>'c' ,'oient'=>'waiq',
			'ouent'=>'wq','ppent'=>'pq','quent'=>'kq','rrent'=>'rq','rrhes'=>'r' ,'ssent'=>'sq',
			'ttent'=>'tq'
			);
		//
		$this->eiy = 'eéèêëiîïyÿ';
		$this->aou = 'aàâäoôöuùûü';
		$this->voyelles = $this->eiy . $this->aou;
		$this->lettres1 = $this->voyelles . 'hmn';
		//
		$this->phon = array(
			'A'=>'a','a'=>'a','b'=>'b','c'=>'eu','d'=>'d','e'=>'&eacute;','E'=>'&egrave;','f'=>'f',
			//'g'=>'g','N'=>'gn','i'=>'i','j'=>'j','k'=>'k','l'=>'l','m'=>'m','n'=>'n','o'=>'ô',
			'g'=>'g','N'=>'gn','i'=>'i','j'=>'j','k'=>'k','l'=>'l','m'=>'m','n'=>'n','o'=>'&ocirc;',
			'O'=>'o','p'=>'p','q'=>'en','Q'=>'on','r'=>'r','s'=>'s','t'=>'t','u'=>'u','v'=>'v',
			'w'=>'ou','x'=>'ch','y'=>'in','Y'=>'un','z'=>'z','@'=>'e'
		);
		$this->phonR = array_flip($this->phon);
		$this->phonR['é'] = 'e';
		$this->phonR['è'] = 'E';
		//
		$this->tabMots = array(
			'ce' => 'c@','de' => 'd@','des' => 'de','je' => 'j@','le' => 'l@','me' => 'm@','ne' => 'n@','se' => 's@',
			'te' => 't@'
		);
		$this->indesirables = array(';','.','(',')','[',']','{','}','?','\'','\"','&','/','#','%','€','@','$','-','_',',');
	}
	public function calculer($parTexte)
	{
		$this->code = '';
		//	Suppression des '/' de codage
		$parTexte = stripslashes($parTexte);
		// S'il n'y a pas de texte, on sort immédiatement
		if ($parTexte == '') return '';
		// On met tout en minuscule
		$parTexte = mb_strtolower($parTexte,"iso-8859-1");
		//	Remplacement des caractères indésirables par des espaces
		$parTexte = str_replace($this->indesirables , ' ' , $parTexte);
		//	Suppression des espaces de début et de fin
		$parTexte = trim($parTexte);
		//	Compactage des espaces
		$parTexte = preg_replace('/\s{2,}/', ' ', $parTexte); 
 		//	Remplacement des ligatures
		$parTexte = str_replace('æ' , 'ae' , $parTexte);
		$parTexte = str_replace('œ' , 'oe' , $parTexte);
		// Si la chaîne ne fait qu'un seul caractère, on sort avec
		if (strlen($parTexte) == 1 ) return $parTexte;
		//	Boucle pour chacun des mots contenus dans le texte
		$tabMots = explode(' ' , $parTexte);
		for ($indice = 0 ; $indice < count($tabMots) ; $indice++)
		{
			if ($indice > 0)
			{
				$this->priseEnCompte(' ',0);
			}
			$this->mot = $tabMots[$indice];
			$this->calculerUnMot();
		}
		return $this->code;
	}
	private function calculerUnMot()
	{
		//
		$this->indMot = 0;
		$longMot = strlen($this->mot);
		//	Traitement des mots complets
		if (array_key_exists($this->mot,$this->tabMots))
		{
			$this->priseEnCompte($this->tabMots[$this->mot],strlen($this->mot));
			return;
		}
		//		
		do
		{
			$carPre2 = '';	//	2 caractère avant celui pointé
			$carPre = '';	//	caractère précédent celui pointé
			$car1 = substr($this->mot,$this->indMot,1);	//	caractère pointé
			$car2 = '';		//	caractère suivant celui pointé
			$car3 = '';		//	3e caractère suivant celui pointé
			$car4 = '';		//	4e caractère suivant celui pointé
			$car5 = '';		//	5e caractère suivant celui pointé
			if ($this->indMot < $longMot - 1)
				$car2 = substr($this->mot,$this->indMot + 1,1);
			if ($this->indMot < $longMot - 2)
				$car3 = substr($this->mot,$this->indMot + 2,1);
			if ($this->indMot < $longMot - 3)
				$car4 = substr($this->mot,$this->indMot + 3,1);
			if ($this->indMot < $longMot - 4)
				$car5 = substr($this->mot,$this->indMot + 4,1);
			if ($this->indMot > 0)
				$carPre = substr($this->mot,$this->indMot-1,1);
			if ($this->indMot > 1)
				$carPre2 = substr($this->mot,$this->indMot-2,1);
			$this->trouve = false;
			//
			//	===== Traitement des lettres en double =====
			if ($car1 == $car2 AND $this->existeChaine('abdfglmnprst' , $car1))
				$this->priseEnCompte('',1);
			//	===== Test des finales =====
			if ($this->indMot == $longMot - 6)
			{
				$w = substr($this->mot,$this->indMot,6);
				if ($w == 'euille')
					$this->priseEnCompte('ci',6);
				if ($w == 'illent')
					$this->priseEnCompte('iq',6);
			}
			//
			if ($this->indMot == $longMot - 5)
			{
				$w = substr($this->mot,$this->indMot,5);
				if ($this->existeTableau($this->finale5 , $w))
					$this->priseEnCompte($this->finale5[$w],5);
			}
			//
			if ($this->indMot == $longMot - 4)
			{
				$w = substr($this->mot,$this->indMot,4);
				if ($this->existeTableau($this->finale4 , $w))
					$this->priseEnCompte($this->finale4[$w],4);
			}
			//
			if ($this->indMot == $longMot - 3)
			{
				$w = substr($this->mot,$this->indMot,3);
				if ($this->existeTableau($this->finale3,$w))
					$this->priseEnCompte($this->finale3[$w],3);
			}
			if ($this->indMot == $longMot - 2)
			{
				$w = substr($this->mot,$this->indMot,2);
				//
				if ($w == 'ch')
				{
					if ($carPre == 'a' OR $carPre == 'o')
						$this->priseEnCompte('k',2);
					else
						$this->priseEnCompte('x',2);
				}
				if ($this->existeTableau($this->finale2 , $w))
					$this->priseEnCompte($this->finale2[$w],2);
			}
			if ($this->trouve) continue;
			//	===== Cas particuliers =====
			if ($this->existeChaine('aeiouy' , $car1))
			{
				//	"am", "an", "em", "en", "im", "in", "om", "on", "um", "un", "ym", "yn"
				if ($this->existeChaine('nm' , $car2) AND $this->existeChaine($this->lettres1 , $car3))
				{
					if ($car1 != 'y')
						$this->priseEnCompte($car1,1);
					else
						$this->priseEnCompte('i',1);
				}
			}
			if ($this->existeChaine('ae' , $car1))
			{
				//	"aim", "ain", "eim", "ein"
				if ($car2 == 'i')
				{
					if ($this->existeChaine('nm' , $car3) AND $this->existeChaine($this->lettres1 , $car4))
						$this->priseEnCompte('E',2);
				}
			}
			if ($car1 == 'i' AND $car2 == 'e' AND $this->existeChaine('mn' , $car3)
				AND $this->existeChaine($this->lettres1 , $car4))
			{
				//	"iem", "ien"
				$this->priseEnCompte('i',2);
			}
			if ($car1 == 'o' AND $car2 == 'i' AND $this->existeChaine('mn' , $car3)
				AND $this->existeChaine($this->lettres1 , $car4))
			{
				//	"oim", "oin"
				$this->priseEnCompte('wa',2);
			}
			if ($car1 == 'y' AND  $this->existeChaine('mn' , $car2)
				AND $this->existeChaine($this->lettres1 , $car3))
			{
				//	"ym", "yn"
				$this->priseEnCompte('i',1);
			}
			if ($this->trouve) continue;
			//
			switch($car1)
			{
			case 'a':
				//	"a" puis son /z/
				if (($car2 == 's' AND $this->existeChaine($this->voyelles , $car3)) OR $car2 == 'z')
					$this->priseEnCompte('A',1);
				//	"aw"
				if ($car2 == 'w' AND $this->existeChaine($this->voyelles , $car3))
					$this->priseEnCompte('o',2);
				//	"ay"
				if ($car2 == 'y')
				{
					if ($this->existeChaine($this->voyelles , $car3))
						$this->priseEnCompte('Ei',2);
					else
						$this->priseEnCompte('E',2);
				}
				//	"acc"
				if ($car2 == 'c' AND $car3 == 'c')
				{
					if ($this->existeChaine($this->aou , $car4))
						$this->priseEnCompte('ak',3);
					else
						$this->priseEnCompte('aks',3);
				}
				break;
			case 'c':
				//	"ca", "co" ou "cu"
				if ($this->existeChaine($this->aou , $car2))
				{
					$this->priseEnCompte('k',1);
					break;
				}
				//	"ce", "ci" ou "cy"
				if ($this->existeChaine($this->eiy , $car2))
				{
					$this->priseEnCompte('s',1);
					break;
				}
				if ($car2 == 'c')
				{
					//	"ccu"
					if ($car3 == 'u')
					{
						if ($this->existeChaine($this->voyelles , $car4))
							$this->priseEnCompte('k',3);
						else
							$this->priseEnCompte('ku',3);
						break;
					}
					//	"cca", "cco" ou "ccu"
					if ($this->existeChaine($this->aou , $car2))
					{
						$this->priseEnCompte('k',2);
						break;
					}
					//	"cce", "cci" ou "ccy"
					if ($this->existeChaine($this->eiy , $car2))
					{
						$this->priseEnCompte('ks',2);
						break;
					}
					//	"cc" puis consonne
					if (!$this->existeChaine($this->voyelles , $car2))
						$this->priseEnCompte('k',1);
				}
				break;
			case 'e':
				//	"e" suivie de 2 consonnes en milieu de mot
				if (!$this->existeChaine($this->voyelles , $car2))
				{
					if (!$this->existeChaine($this->voyelles , $car3) AND $car4 != '')
						$this->priseEnCompte('E',1);
					break;
				}
				//	"ex" en début de mot et suivi d'une voyelle ou de "h"
				if ($carPre == '' AND $car2 == 'x' AND ($this->existeChaine($this->voyelles , $car3) OR $car3 == 'h') )
						$this->priseEnCompte('gz',2);
				break;
			case 'g':
				if ($car2 == 'u')
				{
					//	"gue", "gui", "guy" ou ("gu" puis consonne)
					if ($this->existeChaine($this->eiy , $car3))
					{
						$this->priseEnCompte('g',2);
						break;
					}
				}
				if ($car2 == 'e')
				{
					//	"gea", "geo", "geu" 
					if ($this->existeChaine($this->aou , $car3))
					{
						$this->priseEnCompte('j',2);
						break;
					}
				}
				//	"ga", "go", "gu" 
				if ($this->existeChaine($this->aou , $car2))
				{
					$this->priseEnCompte('g',1);
					break;
				}
				//	"ge", "gi", "gy" 
				if ($this->existeChaine($this->eiy , $car2))
					$this->priseEnCompte('j',1);
				break;
			case 'i':
				//	"ie" puis consonne
				if ($car2 == 'e')
				{
					if (!$this->existeChaine($this->voyelles , $car3))
					{
						$this->priseEnCompte('iE',2);
						break;
					}
				}
				//	"il"
				if ($car2 == 'l')
				{
					if ($car3 == 'l')
					{
						//	"ill"
						if ($this->existeChaine($this->voyelles,$carPre))
						{
							//	quill, guill
							if ($carPre != 'u' AND ($carPre2 != 'q' OR $carPre2 != 'g'))
								$this->priseEnCompte('i',3);
							else
								$this->priseEnCompte('il',3);
						}
						break;
					}
					//	"il"
					if ($this->existeChaine($this->voyelles,$carPre))
					{
						//	quil, guil
						if ($carPre != 'u' AND ($carPre2 != 'q' OR $carPre2 != 'g'))
						$this->priseEnCompte('i',2);
					else
						$this->priseEnCompte('il',2);
					}
					break;
				}
				//	"im" ou "in"
				if ($car2 == 'm' OR $car2 == 'n')
				{
					if (!$this->existeChaine($this->voyelles , $car3))
						$this->priseEnCompte('y',2);
				}
				break;
			case 'î':
				//	"în"
				if ($car2 == 'n')
				{
					if (!$this->existeChaine($this->voyelles , $car3))
						$this->priseEnCompte('y',2);
				}
				break;
			case 'o':
				//	"os" ou "oz" suivi d'une voyelle
				if (($car2 == 's' OR $car2 == 'z') AND $this->existeChaine($this->voyelles, $car3))
				{
					$this->priseEnCompte('o',1);
				}
				//	"oe" suivi d'une voyelle sauf "oest"
				if ($car2 == 'e' AND !$this->existeChaine($this->voyelles,$car3))
				{
					if ($car3 == 's' AND $car4 == 't')
						break;
					$this->priseEnCompte('e',2);
				}
				//	"oin" puis consonne
				if ($car2 == 'i' AND $car3 == 'n' AND !$this->existeChaine($this->voyelles,$car4))
				{
					$this->priseEnCompte('wy',3);
				}
				break;
			case 's':
				//	"sc"
				if ($car2 == 'c')
				{
					if ($this->existeChaine ($this->aou , $car3))
						$this->priseEnCompte('sk',2);
					else
						$this->priseEnCompte('s',2);
					break;
				}
				//	Début de mot
				if ($carPre == '')
					$this->priseEnCompte('s',1);
				else
				{
					//	Milieu de mot suivi d'une consonne
					if (!$this->existeChaine($this->voyelles,$car2))
						$this->priseEnCompte('s',1);
				}
				//	Entre 2 voyelles
				if ($this->existeChaine($this->voyelles,$carPre) AND $this->existeChaine($this->voyelles,$car2))
					$this->priseEnCompte('z',1);
				break;
			case 't':
				//	"ti"
				if ($car2 == 'i')
				{
					//	"tion" en fin de mot
					if ($car3 == 'o' AND $car4 == 'n' AND $car5 == '')
					{
						if ($this->existeChaine($this->voyelles , $carPre))
							$this->priseEnCompte('siQ',4);
						else
							$this->priseEnCompte('tiQ',4);
						break;
					}
				}
				break;
			case 'x':
				//	"x" en début de mot
				if ($carPre == '')
					$this->priseEnCompte('gz',1);
				else
					$this->priseEnCompte('ks',1);
			}
			if ($this->trouve) 
			{
				continue;
			}
			//	===== Test des groupes de lettres =====
			if ($this->indMot < $longMot - 3)
			{
				$w = substr($this->mot,$this->indMot,4);
				if (array_key_exists($w,$this->tab4c))
				{
					$this->priseEnCompte($this->tab4c[$w],4);
					continue;
				}
			}
			if ($this->indMot < $longMot - 2)
			{
				$w = substr($this->mot,$this->indMot,3);
				if (array_key_exists($w,$this->tab3c))
				{
					$this->priseEnCompte($this->tab3c[$w],3);
					continue;
				}
			}
			if ($this->indMot < $longMot - 1)
			{
				$w = substr($this->mot,$this->indMot,2);
				if (array_key_exists($w,$this->tab2c))
				{
					$this->priseEnCompte($this->tab2c[$w],2);
					continue;
				}
			}
			if ($this->indMot < $longMot)
			{
				if (array_key_exists($car1,$this->tab1c))
				{
					$this->priseEnCompte($this->tab1c[$car1],1);
					continue;
				}
			}
			//
			echo 'Caractere inconnu >' . $car1 . '< (' . ord($car1) . ') - mot=' . $this->mot . ' - position=' . $this->indMot . '<br>';
			$this->priseEnCompte('',1);				
		}
		while ($this->indMot < $longMot);
	}
	//	Cette fonction retourne TRUE si la valeur recherchée ($chRecherchee) 
	//		est la première de la liste testée ($chaine)
	private function existeChaine($chaine,$chRecherchee)
	{
		if ($chRecherchee == "")	return false;
		if (strpos($chaine,$chRecherchee) !== false) return true;
		return false;
	}
	//	Détermine si la chaîne $chRecherchee existe dans le tableau $tableau
	//		en tant que clé
	private function existeTableau($tableau , $chRecherchee)
	{
		if ($chRecherchee == "")	return false;
		if (array_key_exists($chRecherchee , $tableau))return true;
		return false;
	}
	//	Prise en compte d'une séquence de caractères
	//	$parCode = code correspondant à la séquence
	//	$parIndice = valeur à ajouter au pointeur sur le mot à traiter
	private function priseEnCompte($parCode,$parIndice)
	{
		if ($parCode != '')
				$this->code .= $parCode;
		$this->indMot += $parIndice;
		$this->trouve = true;
	}
	//	Renvoie un texte correspondant à la phonétique du code
	public function codeVersPhon($parCode)
	{
		if (strlen($parCode) == 0)
			return '';
		$tabW = str_split($parCode);
		$retour = [];
		for ($indice = 0 ; $indice < count($tabW) ; $indice++)
		{
			if ($tabW[$indice] == ' ')
			{
				$retour[] = '_';
				continue;
			}
			if (array_key_exists($tabW[$indice] , $this->phon))
				$retour[] = $this->phon[$tabW[$indice]];
			else
				echo 'Caractère non traduisible >>' . $tabW[$indice] . '<< <br>';
		}
		return implode('-' , $retour);
	}
	//	Renvoie le code correspondant à un texte phonétique
	public function phonVersCode($parCode)
	{
		$tabW = explode('-',$parCode);
		$retour = [];
		for ($indice = 0 ; $indice < count($tabW) ; $indice++)
		{
			if ($tabW[$indice] == '_')
			{
				$retour[] = ' ';
				continue;
			}
			$retour[] = $this->phonR[$tabW[$indice]];
		}
		return implode('' , $retour);
	}
}
?>
