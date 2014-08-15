<?php
$config = <<<EOD
{
	"info":  {
		"name": "file editor",
		"description": {
			"en": "edit (writable) Files located in your Project-Folder",
			"de": "Dateien bearbeiten"
		},
		"io":  [
			"path-string",
			"nothing"
		],
		"authors":  ["Christoph Taubmann"],
		"homepage": "http://cms-kit.org",
		"mail": "info@cms-kit.org",
		"copyright": "GPL",
		"credits":  [
			"see markup-wizard"
		]
	},
	"system":  {
		"version": 0.8,
		"inputs":  [
			"VARCHAR"
		],
		"include":  ["wizard:fileedit\\nexternal:true"],
		"requirements":  {
			"wizards":  {
				"syntax": 0
			}
		},
		"translations":  [
			"en",
			"de"
		]
	}
}
EOD;
?>
