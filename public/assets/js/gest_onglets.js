<script type="text/javascript">
    <!--
    // Gestion des onglets fortement inspirée de http://jon.hedley.net/html-tabbed-dialog-widget
    var panes = new Array();
    function setupPanes(containerId, defaultTabId, ajHaut) {
        // go through the DOM, find each tab-container
        // set up the panes array with named panes
        // find the max height, set tab-panes to that height
        panes[containerId] = new Array();
    var maxHeight = 0; var maxWidth = 0;
    var container = document.getElementById(containerId);
    var paneContainer = container.getElementsByTagName("div")[0];
    var paneList = paneContainer.childNodes;
    for (var i=0; i < paneList.length; i++ ) {
		var pane = paneList[i];
    if (pane.nodeType != 1) continue;
		if (pane.offsetHeight > maxHeight) maxHeight = pane.offsetHeight;
		if (pane.offsetWidth  > maxWidth ) maxWidth  = pane.offsetWidth;
    panes[containerId][pane.id] = pane;
    pane.style.display = "none";
	}

    // Dans le cas où on utilise tinyMCE, il faut augmenter la hauteur
    maxHeight += ajHaut;

    paneContainer.style.height = maxHeight + "px";
    paneContainer.style.width  = maxWidth + "px";
    document.getElementById(defaultTabId).onclick();

	//container.paneContainer.style.height = screen.availHeight;
}

    function showPane(paneId, activeTab) {
	// make tab active class
	// hide other panes (siblings)
	// make pane visible

	// work out which container this tab is part of. could also bounce on parent node
	for (var con in panes) {
        activeTab.blur();
    activeTab.className = "tab-active";
    if (panes[con][paneId] != null) { // tab and pane are members of this container
			var pane = document.getElementById(paneId);
    pane.style.display = "block";
    var container = document.getElementById(con);
    var tabs = container.getElementsByTagName("ul")[0];
    var tabList = tabs.getElementsByTagName("a")
    for (var i=0; i<tabList.length; i++ ) {
				var tab = tabList[i];
    if (tab != activeTab) tab.className = "tab-disabled";
			}
    for (var i in panes[con]) {
				var pane = panes[con][i];
    if (pane == undefined) continue;
    if (pane.id == paneId) continue;
    pane.style.display = "none"
			}
		}
	}
    return false;	
}
-->
</script>