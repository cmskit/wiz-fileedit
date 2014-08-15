<?php
/*
 * File-Editor based on ace
 * depends on Syntax-Wizard
 * */
require dirname(dirname(__DIR__)) . '/inc/php/session.php';

if(!file_exists('../markup/index.php')) exit('Markup-Wizard is missing');

require '../../inc/php/header.php';

// test for root
if(!isset($_SESSION[$_GET['project']]['root'])) exit('you must be logged in as Root!');

// tell PHP the File-Path (if exists) or tell user to place a path
if(!$_GET['path']){
exit('
<html><head><script>
	var p=parent.$(\'#\'+parent.targetFieldId).val();
	if(p.length>5){window.location.search="project='.$_GET['project'].'&path="+p}
	else{parent.message("please enter a valid File-Path!", true, 5000)}
</script></head></html>');
}

// test if its the objects/ folder
$pathArray = explode(DIRECTORY_SEPARATOR, urldecode($_GET['path']));
if($pathArray[count($pathArray)-2]=='objects') exit('you are not allowed to access Files within "objects"');


// create absolute path
$path = '../../../projects/'.strtolower(htmlentities($_GET['project'])).'/'.urldecode($_GET['path']);

$mime = strtolower(array_pop(explode('.', $path)));

// allowed modes + translation mime => ace-mode (see: markup/src-min/mode-xxx.js)
$mode = array(
	'js' => 'javascript',
	'php' => 'php',
	'md' => 'markdown',
	'css' => 'css',
	'html' => 'html',
	'htm' => 'html',
	'sql' => 'sql',
	'json' => 'json',
	'xml' => 'xml',
	'txt' => 'text'
);

// only allow defined Files
if(!$mode[$mime]) exit(strtoupper($mime).' is not allowed/supported!');

if(!is_file($path) && !$_GET['create'])  exit('file "'.$_GET['path'].'" not found. '.((realpath(dirname($path))) ? '<a href="?project='.$_GET['project'].'&path='.$_GET['path'].'&create=1">create?</a>':'(cannot create either because Folder-Names are wrong!)'));

if(!is_writable(dirname($path))) exit('Directory is not writable!');

$canSave = is_writable($path);

$saved = false;
if($_POST['content']) {
	file_put_contents($path, $_POST['content']);
	$saved = 'File saved!';
}

$content = (is_file($path) ? file_get_contents($path) : '');

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title>Editor</title>
	
	<link href="../markup/inc/styles.css" rel="stylesheet" />
	<script src="../markup/inc/scripts.js" type="text/javascript" charset="utf-8"></script>
	
	<script src="../markup/src-min/ace.js" type="text/javascript" charset="utf-8"></script>
	<script>
		document.writeln('<script src="../markup/themes/theme-'+theme[0]+'.js" type="text/javascript" charset="utf-8"><\/script><style>body, #helpdesk{font-size:'+fontSize+'px;background-color:'+theme[1]+';color:'+theme[2]+';border:1px solid '+theme[2]+';}<\/style>');
	</script>
</head>
<body>

<form method="post" id="form" enctype="multipart/form-data" action="index.php?project=<?php echo strtolower($_GET['project']).'&path='.$_GET['path'].'&create='.$_GET['create'];?>">
<input type="hidden" id="content" name="content" value="" />
</form>

<img src="../markup/inc/img/help.png" id="bhelp" onclick="helpToggle()" title="help" />

<?php
// draw save-button if file is writable or to create one
if($canSave || $_GET['create']) {
	echo '<img src="../markup/inc/img/save.png" id="bsave" onclick="save()" title="save" />';
}else {
	echo '<img src="../markup/inc/img/nosave.png" id="bsave" title="File is not writable" />';
}
// save-feedback
if($saved){ echo '<script>alert("'.$saved.'")</script>'; }

?>

<pre id="editor"><?php echo htmlentities($content);?></pre>
<div id="helpdesk" style="display:none">
	<div>
		<img style="float:right" src="../markup/inc/close.png" onclick="helpToggle()" />
		<span style="font-size:10px" id="stats"></span>
	</div>
	<?php echo file_get_contents('../markup/inc/help.html');?>
</div>

<script>

var editor;
var mode = "<?php echo $mode[$mime];?>";
window.onload = function() {
	editor = ace.edit('editor');
	editor.setTheme("ace/theme/"+theme[0]);
	
	var mode = require("ace/mode/<?php echo $mode[$mime];?>").Mode;
	editor.getSession().setMode(new mode());
	
	// Font size
	document.getElementById('editor').style.fontSize = fontSize+'px';
	// Tab size:
	editor.getSession().setTabSize(4);

	// Use soft tabs:
	editor.getSession().setUseSoftTabs(true);
	
};

function save(){
	//alert(editor.getSession().getValue());
	
	document.getElementById('content').value = editor.getSession().getValue();
	document.getElementById('form').submit();
}

</script>

</body>
</html>
