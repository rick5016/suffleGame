<html>

<head>
	<meta charset="utf-8">
	<title>Jeux mélangé</title>
	<link rel="stylesheet" href="bootstrap/dist/css/bootstrap.css">
	<style>
		#content { width:780px;margin-left: auto;margin-right:auto; }
		.list { margin:5px;float:left; }
		ul { list-style-type: none; margin: 0; padding: 0;}
		li { border: solid 1px black;height:50px; width:250px;padding:5px;margin:5px 0px;}
	</style>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script>
		function shuffle(a) {
			var j, x, i;
			for (i = a.length; i; i--) {
				j = Math.floor(Math.random() * i);
				x = a[i - 1];
				a[i - 1] = a[j];
				a[j] = x;
			}
		}
	</script>
</head>
<body>
<div id="content">
	<div id="dragItems"></div>
	<div style="clear: both;"></div>
	<button type="button" id="reset" class="btn btn-default">Mélanger</button>
	<button type="button" id="valider" class="btn btn-default">Valider</button>
	<div id="resultat" style="float:right;"></div>
</div>
	<?php
	$file       = fopen("liste.txt", 'r');
	$tabReponse = array();
	$tab        = array();
	while (!feof($file))
	{
		$line 		  = fgetcsv($file, 0, ';');
		$tab[] 		  = array($line[0], $line[1]);
	}
	?>
  <script>
	$( function()
	{
		var tab = <?php echo json_encode($tab); ?>;
		makeGame(tab);
		
		// Bouton Valider
		$("#valider").click(function()
		{
			var total = 0;
			var bonnes_reponses = 0;
			$('#emplacements').children('li').each(function( index ) {
				total++;
				if ($( this ).text() != tab[index][1]) {
					$( this ).attr('class', 'dragdiv emplacement ui-draggable ui-draggable-handle ui-droppable bg-danger');
				} else {
					bonnes_reponses++;
					$( this ).attr('class', 'dragdiv emplacement ui-draggable ui-draggable-handle ui-droppable bg-success');
				}
			});
			$("#resultat").html(bonnes_reponses + '/' + total);
		});
		
		// Bouton Mélanger
		$("#reset").click(function()
		{
			$("#dragItems").html('');
			$("#resultat").html('');
			makeGame(tab);
		});
		
		function makeGame(tab)
		{
			var reponses = [];
			
			$("#dragItems").append($('<ul id="questions" class="list"></ul>'));
			for(var i= 0; i < tab.length; i++)
			{
				reponses[i] = tab[i][1]; // réponse
				var $newDiv = $('<li class="dragdiv question">' + tab[i][0] + '</li>'); // question
				$("#questions").append($newDiv);
			}
			shuffle(reponses);
			
			$("#dragItems").append($('<ul id="emplacements" class="list"></ul>'));
			for(var i= 0; i < tab.length; i++)
			{
				var $newDiv = $('<li class="dragdiv emplacement"></li>');
				makeElementAsDragAndDrop($newDiv, 'drop');
				$("#emplacements").append($newDiv);
			}
			
			$("#dragItems").append($('<ul id="reponses" class="list"></ul>'));
			for(var i= 0; i < tab.length; i++)
			{
				var $newDiv = $('<li class="dragdiv reponse">' + reponses[i] + '</li>');
				makeElementAsDragAndDrop($newDiv, 'drag');
				$("#reponses").append($newDiv);
			}
		}
		
		function makeElementAsDragAndDrop(elem, type)
		{
			if (type == 'drag')
			{
				$(elem).draggable({
					classes: {
						"ui-draggable": "bg-info"
					},
					revert: "invalid",
					cursor: "move",
					helper: "clone"
				});
			}
			
			$(elem).droppable({
				classes: {
					"ui-droppable-hover": "bg-warning"
				  },
				drop: function(event, ui) {
					// draggable : $(ui.draggable) : reponse     : elemdrag
					// droppable : $(this)         : emplacement : elemdrop
					var test_draggable = $(this).hasClass( "ui-draggable" );
					
					var dragElem = $(ui.draggable).clone().replaceAll(this); // clone de elemdrag et remplace elemdrop
					$(this).replaceAll(ui.draggable); // elemdrop remplace elemdrag
					
					// Si elemdrop est draggable il le reste
					if (test_draggable) {
						makeElementAsDragAndDrop(this, 'drag');
					} else {
						makeElementAsDragAndDrop(this, 'drop');
					}
					makeElementAsDragAndDrop(dragElem, 'drag');
				}
			});
		}
	  
	});
  </script>

</body>
</html>

