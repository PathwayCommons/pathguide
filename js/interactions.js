var vis;
var _mouseOverNode;

function changeNetwork() {
	var fileName = $("#NetworksSelect").val();
	
	var descriptions = {
		AllPathwaysDB: "This network shows the links among many databases in Pathguide.",
		ExchangeFormats: "A network of the exchange format languages used by pathway and interaction databases.",
		InteractionDB: "A network displaying only interaction databases from Pathguide.",
		MetaminingDB: "This network shows the connections among databases that consolidate data from several sources.",
		PathwaysDB: "A network of databases with pathway information and the data sources they use.",
		PredictiveInteractionDB: "A network displaying only databases of predicted interactions from Pathguide.",
		UnifyingEffortsDB: "A network of databases with the mandate to unify biological data."
	}
	
	$("#NetworkDescription").text(descriptions[fileName]);
	drawInteractionsNetwork(fileName);
}

function drawInteractionsNetwork(fileName) {
	$("#NetworkVisMenu").html("&nbsp;");
    $("#SelectedNetRes").html("");
	
	var visOpt = { swfPath: "flash/CytoscapeWeb", flashInstallerPath: "flash/playerProductInstall" };
	vis = new org.cytoscapeweb.Visualization("NetworkVis", visOpt);
	
	vis.ready(function() {
        var menu = '<a href="#" onClick="resetNetVisLayout();return false;">Reset Layout</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="#" id="NetworkVisPanZoomToggle" onClick="toggleNetVisPanZoomControl();return false;">Show Pan-Zoom Control</a>';
    	$("#NetworkVisMenu").html(menu);
    	
    	// Add right-click context menu item: 
    	vis.addContextMenuItem("Go to the webpage...", "nodes", function(evt) {
			var dt = evt.target.data;
			var url = dt.URL;
			if (url == null) { url = $("#ResourceRow_"+dt.PGID+" .ListLink").attr("href"); }
			window.open(url);	
		})
    	.addContextMenuItem("About Cytoscape Web...", function(evt) {
    		window.open("http://cytoscapeweb.cytoscape.org/");	
    	});
    })
	.addListener("select", "nodes", function(evt) {
    	// Show selected resources
    	var nodesArray = evt.target;
    	$.each(nodesArray, function(i, node) {
            var dt = node.data;
            var id = dt.PGID;
			var notPathguide = false;
			var row = $("[id=ResourceRow_"+id+"]");
			if (row.length === 0) {
				notPathguide = true;
				row = $("#ResourceRow_TEMPLATE");
				id = dt.canonicalName.toLowerCase();
			}
			if ($("[id=SelectedResourceRow_"+id+"]").length === 0) {
            	// The resource is not displayed in the table...
				row = row.clone().attr("id", "SelectedResourceRow_"+id);
				if (notPathguide) {
					if (dt.URL != null) {
						// Set shortName and URL:
						row.find("td:first").html('<a href="'+dt.URL+'" class="ListLink" target="_blank">'+dt.canonicalName+'</a>');
					} else {
						// Set only the shortName:
						row.find("td:first").html(dt.canonicalName);
					}
				}
				$("#SelectedNetRes").append(row);
			}
    	});
        sortResources();
        setAlternateRowStyle();
    })
    .addListener("deselect", "nodes", function(evt) {
    	// Hide deselected resources
    	var nodesArray = evt.target;
    	$.each(nodesArray, function(i, node) {
            var dt = node.data;
            var row = $("#SelectedResourceRow_"+dt.PGID);
    		if (row.length !== 0) {
                row.remove();
    		} else {
                $("[id=SelectedResourceRow_"+dt.canonicalName.toLowerCase()+"]").remove();
            }
    	});
        setAlternateRowStyle();
    })
    .addListener("mouseover", "nodes", function(evt) {
    	_mouseOverNode = evt.target;
	   	highlighFirstNeighbors(evt.target);
	})
	.addListener("mouseout", "nodes", function(evt) {
		_mouseOverNode = null;
		clearFirstNeighborsHighligh();
	})
	.addListener("dblclick", "nodes", function(evt) {
	   	selectFirstNeighbors(evt.target);
	});

	var drawOpt = {
			panZoomControlVisible: false,
			edgesMerged: false,
			nodeLabelsVisible: true,
			nodeTooltipsEnabled: false,
			edgeTooltipsEnabled: false,
			visualStyle: {
				nodes: {
		            borderWidth: 0,
					label: { passthroughMapper: { attrName: "canonicalName" } },
					labelHorizontalAnchor: "center",
					labelVerticalAnchor: "middle",
					labelFontWeight: "bold",
					labelGlowOpacity: 0.4,
					labelGlowColor: "#ffffff",
					selectionBorderWidth: 2,
					selectionBorderColor: "#000000",
					selectionGlowColor: "#ffff00"
				},
                edges: {
					opacity: { defaultValue: 1,
                               discreteMapper: { attrName: "interaction",
                                                 entries: [ { attrValue: "crossreferences",  value: 0.8 },
                                                            { attrValue: "maps", value: 0.8 } ]
                                                } },
                    selectionGlowOpacity: 0
				}
			}
	};
	
	$.get("networks/"+fileName+".xgmml", function(network) {
		if (typeof network !== "string") {
            if (window.ActiveXObject) {
            	network = network.xml;
            } else {
            	network = (new XMLSerializer()).serializeToString(network);
            }
        }
		drawOpt.network = network;
		vis.draw(drawOpt);
	});
}

function resetNetVisLayout() {
	if (vis) {
		vis.layout("Preset");
	}
}

function toggleNetVisPanZoomControl() {
	if (vis) {
		var visible = !vis.panZoomControlVisible();
		vis.panZoomControlVisible(visible);
		$("#NetworkVisPanZoomToggle").html(visible ? "Hide Pan-Zoom Control" : "Show Pan-Zoom Control");
	}
}

function highlighFirstNeighbors(target) {
	setTimeout(function() {
		if (_mouseOverNode != null && _mouseOverNode.data.id === target.data.id) {
			var fn = vis.firstNeighbors([target]);
			var bypass = { nodes: {}, edges: {} };
		
			var allNodes = vis.nodes();
			$.each(allNodes, function(i, n) {
				bypass.nodes[n.data.id] = { opacity: 0.2 };
			});
			var neighbors = fn.neighbors;
			neighbors = neighbors.concat(fn.rootNodes);
			$.each(neighbors, function(i, n) {
				bypass.nodes[n.data.id] = { opacity: 1 };
			});
		
			var allEdges = vis.edges();
			$.each(allEdges, function(i, e) {
				bypass.edges[e.data.id] = { opacity: 0.1 };
			});
			var edges = fn.edges;
			$.each(edges, function(i, e) {
				bypass.edges[e.data.id] = { opacity: 1 };
			});
		
			vis.visualStyleBypass(bypass);
		}
	}, 400);
}

function clearFirstNeighborsHighligh() {
	setTimeout(function() {
		if (_mouseOverNode == null) {
			vis.visualStyleBypass({});
		}
	}, 400);
}

function selectFirstNeighbors(node) {
	var fn = vis.firstNeighbors([node]);
	var nodes = fn.neighbors.concat(fn.rootNodes);
	vis.deselect();
	vis.select(nodes);
}

function setAlternateRowStyle() {
    $("#SelectedNetRes > tbody > tr").each(function(i) {
        if (i%2 === 0) {
            $(this).removeClass("Even");
            $(this).addClass("Odd");
        } else {
            $(this).removeClass("Odd");
            $(this).addClass("Even");
        }
    });
}

function sortResources() {
    var trList = $("#SelectedNetRes > tbody > tr");
    $("#SelectedNetRes").html("");
    
    var array = [];
    $.each(trList, function(i) {
        array.push($(this))
    });

    var sorted = array.sort(function(tr1, tr2) {
        var txt1 = tr1.find(".ListLink").text();
        if (txt1 == "") {
        	txt1 = tr1.find("td:first").html();
        }
        var txt2 = tr2.find(".ListLink").text();
        if (txt2 == "") {
        	txt2 = tr2.find("td:first").html();
        }
        txt1 = txt1.toLowerCase();
        txt2 = txt2.toLowerCase();
        
        if (txt1 > txt2) { return 1; }
        else if (txt1 == txt2) { return 0; }
        else { return -1; }
    });

    $.each(sorted, function(i, row) {
        $("#SelectedNetRes").append(row);
    });
}
