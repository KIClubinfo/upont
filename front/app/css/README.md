Pour ajouter des modules bootstrap (à éviter mais bon...), il faut décommenter le nom du module dans le fichier bootstrap.less

Conventions css :

.up-ticket :
	élément avec une margin, un padding donnés, et dont le background, la border et la box-shadow sont définies. Il s'agit du conteneur de texte de base du site.

.up-zone-picker :
	Menu secondaire qui apparaît en dessous du header. Les liens apparaissent jolis, il faut gérer le lien en surbrillance avec angular-ui-router. (avec ui-sref-active)