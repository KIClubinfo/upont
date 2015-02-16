Directives perso :
	fillWindow : Fais s'adapter la hauteur minimale de l'élémént pour que le trio header-luimême-footer remplisse au moins la fenêtre. Si on met l'argument calendrier, on force la hauteur et on ne compte plus le footer.

	modalOpen : Gère les boutons d'ouverture des modaux de bootstrap. Il faut mettre en argument le nom du modal. Pour le modal, il faut mettre dans la page web un tag sous la forme :
		<script type="text/ng-template" id="nomDuModal">
		    'Contenu du modal'
		</script>
	Pour le contenu dynamique, mettre les variables dans la directive variable (ex: <div modal-open="nom" variables"{var1 : 'val1' }"></div>)  Voir aussi la doc de la librairie angular-bootstrap.

	scheduler : gère la librairie dhtmlx scheduler utilisée dans la page calendrier