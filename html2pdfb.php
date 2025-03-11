<?php

// TODO: will be in vendor dir with last version

//HTML2PDF par Clément Lavoillotte
//ac.lavoillotte@noos.fr
//webmaster@streetpc.tk
//http://www.streetpc.tk

require(__DIR__ . '/exlibs/FPDF.php');

// fonction hex2dec
// retourne un tableau associatif (clés : R,V,B) à
// partir d'un code html de couleur hexa (ex : #3FE5AA)
function hex2dec($couleur = "#000000")
{
    $R = substr($couleur, 1, 2);
    $rouge = hexdec($R);
    $V = substr($couleur, 3, 2);
    $vert = hexdec($V);
    $B = substr($couleur, 5, 2);
    $bleu = hexdec($B);
    $tbl_couleur = array();
    $tbl_couleur['R'] = $rouge;
    $tbl_couleur['V'] = $vert;
    $tbl_couleur['B'] = $bleu;
    return $tbl_couleur;
}

//conversion pixel -> millimètre en 72 dpi
function px2mm($px)
{
    return $px * 25.4 / 72;
}

function txtentities($html)
{
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}
////////////////////////////////////

class PDF_HTML extends FPDF
{
    //variables du parseur html
    var $B;
    var $I;
    var $U;
    var $HREF;
    var $fonts = array('arial', 'times', 'courier', 'helvetica', 'symbol');
    var $issetfont;
    var $issetcolor;

    function __construct($orientation = 'P', $unit = 'mm', $format = 'A4')
    {
        parent::__construct($orientation, $unit, $format);
        // $this->FPDF($orientation,$unit,$format);
        $this->B = 0;
        $this->I = 0;
        $this->U = 0;
        $this->HREF = '';
        // $this->fontlist=array('arial', 'times', 'courier', 'helvetica', 'symbol');
        $this->issetfont = false;
        $this->issetcolor = false;
    }

    function WriteHTML($html)
    {
        //Parseur HTML
        //$html=strip_tags($html,"<b><u><i><a><img><p><br><strong><em><font><tr><blockquote>"); //supprime tous les tags sauf ceux reconnus
        $html = strip_tags($html, "<b><u><i><a><img><p><br><strong><em><font><tr><blockquote><li>"); //supprime tous les tags sauf ceux reconnus
        $html = str_replace("\n", ' ', $html); //remplace retour à la ligne par un espace
        $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE); //éclate la chaîne avec les balises
        foreach ($a as $i => $e) {
            if ($i % 2 == 0) {
                //Texte
                if ($this->HREF)
                    $this->PutLink($this->HREF, $e);
                else
                    // $this->Write(5,$e);
                    $this->Write(5, txtentities($e));
                // $this->Write(5,stripslashes(txtentities($e)));
            } else {
                //Balise
                if ($e[0] == '/')
                    $this->CloseTag(strtoupper(substr($e, 1)));
                else {
                    //Extraction des attributs
                    $a2 = explode(' ', $e);
                    $tag = strtoupper(array_shift($a2));
                    $attr = array();
                    foreach ($a2 as $v) {
                        if (preg_match('/([^=]*)=["\']?([^"\']*)/', $v, $a3))
                            $attr[strtoupper($a3[1])] = $a3[2];
                    }
                    $this->OpenTag($tag, $attr);
                }
            }
        }
    }

    function OpenTag($tag, $attr)
    {
        //Balise ouvrante
        switch ($tag) {
            case 'STRONG':
                $this->SetStyle('B', true);
                break;
            case 'EM':
                $this->SetStyle('I', true);
                break;
            case 'B':
            case 'I':
            case 'U':
                $this->SetStyle($tag, true);
                break;
            case 'A':
                $this->HREF = $attr['HREF'];
                break;
            case 'IMG':
                if (isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if (!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if (!isset($attr['HEIGHT']))
                        $attr['HEIGHT'] = 0;
                    $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                }
                break;
            // Ajout
            case 'LI':
                $this->Ln(6);
                //$this->SetTextColor(190,0,0);
                $this->Write(5, '     » ');
                //$this->mySetTextColor(-1);
                break;
            // Ajout
            case 'TR':
            case 'BLOCKQUOTE':
            case 'BR':
                $this->Ln(5);
                break;
            case 'P':
                $this->Ln(10);
                break;
            case 'FONT':
                if (isset($attr['COLOR']) && $attr['COLOR'] != '') {
                    $coul = hex2dec($attr['COLOR']);
                    $this->SetTextColor($coul['R'], $coul['V'], $coul['B']);
                    $this->issetcolor = true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fonts)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont = true;
                }
                break;
        }
    }

    function CloseTag($tag)
    {
        //Balise fermante
        if ($tag == 'STRONG')
            $tag = 'B';
        if ($tag == 'EM')
            $tag = 'I';
        if ($tag == 'B' || $tag == 'I' || $tag == 'U')
            $this->SetStyle($tag, false);
        if ($tag == 'A')
            $this->HREF = '';
        if ($tag == 'FONT') {
            if ($this->issetcolor == true) {
                $this->SetTextColor(0);
            }
            if ($this->issetfont) {
                $this->SetFont('arial');
                $this->issetfont = false;
            }
        }
    }

    function SetStyle($tag, $enable)
    {
        //Modifie le style et sélectionne la police correspondante
        $this->$tag += ($enable ? 1 : -1);
        $style = '';
        foreach (array('B', 'I', 'U') as $s) {
            if ($this->$s > 0)
                $style .= $s;
        }
        $this->SetFont('', $style);
    }

    function PutLink($URL, $txt)
    {
        //Place un hyperlien
        $this->SetTextColor(0, 0, 255);
        $this->SetStyle('U', true);
        $this->Write(5, $txt, $URL);
        $this->SetStyle('U', false);
        $this->SetTextColor(0);
    }

    function Footer()
    {
        //Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        //Police Arial italique 8
        $this->SetFont('Arial', 'I', 8);
        //$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
        $temps = time();
        $jour = date('j', $temps);  //format numerique : 1->31
        $annee = date('Y', $temps); //format numerique : 4 chiffres
        $mois = date('m', $temps);
        $this->Cell(0, 10, 'Geneamania - ' . $jour . '/' . $mois . '/' . $annee, 0, 0, 'C');
    }

    // Repris de http://www.fpdf.org/fr/script/script7.php, Auteur : Maxime Delorme
    function RoundedRectH($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));

        $this->_ArcH($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_ArcH($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_ArcH($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        $this->_ArcH($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _ArcH($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c ',
            $x1 * $this->k,
            ($h - $y1) * $this->k,
            $x2 * $this->k,
            ($h - $y2) * $this->k,
            $x3 * $this->k,
            ($h - $y3) * $this->k
        ));
    }
}
