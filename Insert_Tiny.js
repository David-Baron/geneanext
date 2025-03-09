<!-- Javascript d'appel du paramétrage de Tiny MCE pour les Text Area formattés -->
<script type="text/javascript" src="<?php echo $rep_Tiny; ?>tiny_mce.js"></script>
<script type="text/javascript">
<!--
tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	language : "fr",
	relative_urls : false,
	theme_advanced_path : false,
	theme_advanced_buttons1 : "bold,italic,underline,separator,strikethrough,justifyleft,justifycenter,justifyright, justifyfull,bullist,numlist,undo,redo,link,unlink,separator,forecolor,backcolor,image,charmap,fontsizeselect",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	forced_root_block : false,
	theme_advanced_statusbar_location : "none",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]"
});
//-->
</script>