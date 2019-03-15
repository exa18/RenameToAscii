<?php
/* ******************

	php scripting by Julian.Cenkier@wp.eu
	
*/
$version = "1.5";
$apk = "Rename to ASCII";
$apkhelp = '
	* renames files and directories to ASCII names
	* it removes all accented chars
	* 
		php script.php -u directory  : verify if exists
		php script.php -u directory -p : change names if exists
		php script.php -v 1 : show summary
		php script.php -h : show help
	*
	* exp. you can run as cron also
	';
$time = microtime(true);
/*
	START
*/
$post = basename(__FILE__);
$diroot = diroot();
$total = array('d'=>0,'f'=>0);
$log = array();

	/*
		IF running HTTP or SSH
	*/
		$sshmode=0;
	if ( !isset($_SERVER[HTTP_USER_AGENT]) ) {
		$sshmode=1;
	}
	/*
		Set
	*/
	$r = getargv();
	$uri = $r[u];
	$perm = $r[p];
	$verify = $r[v];
	$help = $r[h];
	$htmlform = ((isset($_GET['action']) and $_GET['action'] == 'upload')?1:0);
	/*
		Execute
	*/
	if ( ($uri !='/' and !empty($uri)) or $htmlform ) {
		$home = $diroot . $uri . '/';
		if (file_exists($home)) {
			entryClean(dirToArray($home),$home);
			//echo var_dump($log);
			//exit();
			$sum = $total['f']+$total['d'];
			if ($perm) {
				$fi = 'Renamed';
			}else{
				$fi = 'Waiting to rename';
			}
			$done = ($sum?$fi.' '. $sum .' entries ('.$total['f'].' files and '.$total['d'].' dirs)':'Nothing found to change') . ' at ... '.$uri;
		}else{
			$done = 'No such directory exists !';
		}
	}else{
		//$done = "Empty argument!";
		$done='';
	}
	$time = number_format(microtime(true) - $time, 3, '.','');
/*
	Print summary in SSH or browser as html
*/
if ($sshmode){
	if ($verify or $help){
		echo "$apk $version : Executed in $time seconds." . PHP_EOL;
		if ($help){
			echo $apkhelp . PHP_EOL;
		}
		echo $done  . PHP_EOL;
	}
}else{
/* ******************

	HTML
*/
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title><?=$apk?> <?=$version?></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
	<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
	<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

	<style type="text/css">
	body{width: 100%;height: 100%}
	.row{display: flex;justify-content: center; align-items: center;height:100%}
	.row>div{text-align:center}
	h3, .color{color:#3cd}
	span.label{line-height:2;position: relative;top: -.2em}
	.inputfile{width:.1px;height:.1px;opacity:0;overflow:hidden;position:absolute;z-index:-1}
	.inputfile-1 + label{color:#fff;background-color:#3cd}
	.inputfile + label{max-width:80%;font-size:1.25rem;font-weight:700;text-overflow:ellipsis;white-space:nowrap;cursor:pointer;display:inline-block;overflow:hidden;padding:.625rem 1.25rem;margin-bottom:1.5rem}
	.inputfile + label svg{width:1em;height:1em;vertical-align:middle;fill:currentColor;margin-top:-.25em;margin-right:.25em}
	.txt-normal{font-weight:normal}
	.btn-default.btn-on.active, .btn-default.btn-off.active{background-color: #3cd;color: white}	
	.btn-switch .btn-default.btn-off.active{background-color: #777}
	#footer{margin:auto}
	.txt-normal{font-weight:normal}
	.btn-default.btn-on.active, .btn-default.btn-off.active{background-color: #3cd;color: white}	
	.btn-switch .btn-default.btn-off.active{background-color: #777}
	.btn-switch-grp .btn-switch{margin-bottom:10px}
	.btn-switch-grp{margin:20px 0 15px 0}
	input[type="text"]{width:60%}
	label{display:block;}
	@media (max-width: 27em){
	input[type="text"]{width:100%}
	}
	</style>
	<script type="text/javascript">
	var ff = {
		labelval : 'Choose a file...',
		input:"file-1",
		labelchange : function(label){
			$('label[for="'+ff.input+'"] span').html(label);
		}
	};
	
		$(function(){
		ff.labelchange(ff.labelval);
			$('#'+ff.input).on('change', function(){
				var file = document.forms['form'][ff.input].files[0];
				//file.name == "photo.png"
				//file.type == "image/png"
				//file.size == 300821
				ff.labelchange(file.name);
				$('input[type="submit"]').removeClass('hidden');
				$('input[type="reset"]').removeClass('hidden');
			});
			$('input[type="reset"]').on('click', function(){
				$('input[type="submit"]').addClass('hidden');
				$('input[type="reset"]').addClass('hidden');
				ff.labelchange(ff.labelval);
				window.location = window.location.href;
			});
		});
	</script>
</head>
<body>
	<div class="container">
	<div class="row">
	<div class="col-xs-12">
	<h3><?=$apk?> <span class="badge"><?=$version?></span></h3>
	<?php
	if ($done) {
		echo '<hr />' . $done;
	}else{
		echo '<hr />' . nl2br($apkhelp);
	}
	
	?>
	<hr />
		<form id="form" method="get" action="<?=$post?>" enctype="multipart/form-data">
			<input type="hidden" name="action" value="upload" />
			<?php if (empty($done)) { ?>
			<div class="form-group">
				<label for="uri">Enter relative path</label>
				<input id="uri" class="inputuri" type="text" name="u" />
            </div>
            <div class="form-group btn-switch">
			<div class="btn-group" data-toggle="buttons">
				<label class="btn btn-default btn-off btn-xs active">
				<input type="radio" name="p" value="0" checked="checked"/>TEST</label>
				<label class="btn btn-default btn-on btn-xs">
				<input type="radio" name="p" value="1" />CHANGE</label>
            </div>
            </div>
			<?php } ?>
					<br />
			<?php if (empty($done)) { ?>
			<input class="btn btn-success " type="submit" value="Execute" />
			<?php }else{ ?>
			<a href="/<?=$post?>" class="btn btn-default " type="reset" value="Clear" >Clear</a>
			<?php } ?>
		</form>
		<hr />
		<div id="footer" class="small">powered by PHP5&nbsp;&nbsp;|&nbsp;&nbsp;coded by Julian Cenkier</div>
	</div>
	</div>
	</div>
</body>
</html>	
	
<?php	
}
exit();

/* ******************

	FUNCTIONS
*/
	function getext($ext) {
		return strtolower(substr($ext,-3));
	}
	function getfn($fname) {
		return substr($fname,0,-4);
	}
	function escaping($f){
		return htmlspecialchars( $f );
	}
	function makeDir($dirsub,$diroot=__DIR__) {
		if (!file_exists($diroot."/".$dirsub."/")) {
		    @mkdir($diroot."/".$dirsub, 0755);
		}
	}
	function diroot(){
		if(defined(__DIR__)){
			$diroot= __DIR__;
	    }else{
			$diroot= dirname(__FILE__);
	    }
	    return $diroot. '/';
	}
	function getargv(){
		$result=array();
		$argv = $_SERVER[argv];
		if ( isset($_SERVER[argc]) and $_SERVER[argc]>1 and strpos($_SERVER[PHP_SELF],$argv[0])!==FALSE ) {
		/*
			SSH execute
		*/
			$arg = getopt("u:p:v:h", array("","","","help") );
			if (count($arg)>0) {
				$result[u] = ($arg[u]?escapingUri($arg[u]):0);
				$result[p] = (isset($arg[p])?1:0);
				$result[v] = (isset($arg[v])?1:0);
				$result[h] = (isset($arg[h])?1:0);
			}
		}else{
		/*
			HTTP run
		*/
			$argv = $_SERVER[QUERY_STRING];
			$arg = explode("&",$argv);
			foreach ( $arg as $k=>$v) {
				$a = explode("=",$v);
				$result[$a[0]] = $a[1];
			}
			$result[u] = ($result[u]?escapingUri($result[u]):0);
			$result[p] = (isset($result[p])?1:0);
			$result[v] = (isset($result[v])?1:0);
			$result[h] = (isset($result[h])?1:0);
		}
		return $result;
	}
	function dirToArray($dir) {
		$result = array();
		$cdir = scandir($dir);
		foreach ($cdir as $key => $value) {
			if (!in_array($value,array(".",".."))) {
				if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
					$result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
				}else{
					$result[] = $value;
				}
			}
		}
		return $result;
	}
	function entryClean($f,$t=''){
		global $total,$perm,$log;
		foreach ($f as $k=>$v) {
			if (is_array($v)) {
				$fix = cleanFilename($k);
				if ($k != $fix) {
					//$fix = URLify::filter ( $fix,200,"", true );
					$fix = URLify::downcode ( $fix );
					//$log[$t.$fix.'/'] = $t.$k.'/';
					if ($perm) {
						if (@rename($t.$k.'/',$t.$fix.'/')){
							$total['d']+=1;
						}
					}else{
						$total['d']+=1;
					}
				}else{
					$fix = $k;
				}
				//echo $t.$k.'/ => '.$t. $fix .'/<br>';
				entryClean($v, $t . $fix . '/');
			}else{
				$fix = cleanFilename($v);
				//$fix = URLify::filter ( $fix,200,"", true );
				$fix = URLify::downcode ( $fix );
				if ($v != $fix) {
					//$log[$t][$fix] = $v;
					if ($perm) {
						if (@rename($t.$v,$t.$fix)){
							$total['f']+=1;
						}
					}else{
						$total['f']+=1;
					}
				}
				//echo $v.' => '.$t.$fix.'<br>';
			}
		}
	}
	function escapingUri($uri){
		$uri = escaping($uri);
		$uri = preg_replace("/\/{2,}/", "/", $uri);
		$uri = preg_replace("/(^\/)/", "", $uri);
		$uri = preg_replace("/(\/|\/\s*)$/", "", $uri);
		return $uri;
	}
	/*
		http://php.net/manual/en/function.mb-convert-encoding.php#112547
	*/
	function cleanFilename($text) {
	    // map based on:
	    // http://konfiguracja.c0.pl/iso02vscp1250en.html
	    // http://konfiguracja.c0.pl/webpl/index_en.html#examp
	    // http://www.htmlentities.com/html/entities/
	    $map = array(
	        chr(0x8A) => chr(0xA9),
	        chr(0x8C) => chr(0xA6),
	        chr(0x8D) => chr(0xAB),
	        chr(0x8E) => chr(0xAE),
	        chr(0x8F) => chr(0xAC),
	        chr(0x9C) => chr(0xB6),
	        chr(0x9D) => chr(0xBB),
	        chr(0xA1) => chr(0xB7),
	        chr(0xA5) => chr(0xA1),
	        chr(0xBC) => chr(0xA5),
	        chr(0x9F) => chr(0xBC),
	        chr(0xB9) => chr(0xB1),
	        chr(0x9A) => chr(0xB9),
	        chr(0xBE) => chr(0xB5),
	        chr(0x9E) => chr(0xBE),
	        chr(0x80) => '&euro;',
	        chr(0x82) => '&sbquo;',
	        chr(0x84) => '&bdquo;',
	        chr(0x85) => '&hellip;',
	        chr(0x86) => '&dagger;',
	        chr(0x87) => '&Dagger;',
	        chr(0x89) => '&permil;',
	        chr(0x8B) => '&lsaquo;',
	        chr(0x91) => '&lsquo;',
	        chr(0x92) => '&rsquo;',
	        chr(0x93) => '&ldquo;',
	        chr(0x94) => '&rdquo;',
	        chr(0x95) => '&bull;',
	        chr(0x96) => '&ndash;',
	        chr(0x97) => '&mdash;',
	        chr(0x99) => '&trade;',
	        chr(0x9B) => '&rsquo;',
	        chr(0xA6) => '&brvbar;',
	        chr(0xA9) => '&copy;',
	        chr(0xAB) => '&laquo;',
	        chr(0xAE) => '&reg;',
	        chr(0xB1) => '&plusmn;',
	        chr(0xB5) => '&micro;',
	        chr(0xB6) => '&para;',
	        chr(0xB7) => '&middot;',
	        chr(0xBB) => '&raquo;',
	    );
	    return html_entity_decode(mb_convert_encoding(strtr($text, $map), 'UTF-8', 'ISO-8859-2'), ENT_QUOTES, 'UTF-8');
    }
/*
	function cleanFilename($string, $is_filename = TRUE) {
		$string = mb_convert_encoding($string,'ASCII','UTF-8');
		return preg_replace('/[^a-zA-Z0-9\-\._\s]/','-', $string);
		//$string = preg_replace('/[^\w\-'. ($is_filename ? '~_\.' : ''). ']+/u', '-', $string);
		//return preg_replace('/--+/u', '-', $string);
	}
*/
	/**
	 * https://github.com/jbroadway/urlify
	 *
	 * A PHP port of URLify.js from the Django project
	 * (https://github.com/django/django/blob/master/django/contrib/admin/static/admin/js/urlify.js).
	 * Handles symbols from Latin languages, Greek, Turkish, Bulgarian, Russian,
	 * Ukrainian, Czech, Polish, Romanian, Latvian, Lithuanian, Vietnamese, Arabic,
	 * Serbian, Azerbaijani and Kazakh. Symbols it cannot transliterate
	 * it will simply omit.
	 *
	 * Usage:
	 *
	 *     echo URLify::filter (' J\'étudie le français ');
	 *     // "jetudie-le-francais"
	 *
	 *     echo URLify::filter ('Lo siento, no hablo español.');
	 *     // "lo-siento-no-hablo-espanol"
	 */
	class URLify
	{
		public static $maps = array (
			'de' => array ( /* German */
				'Ä' => 'Ae', 'Ö' => 'Oe', 'Ü' => 'Ue', 'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss',
				'ẞ' => 'SS'
			),
			'latin' => array (
				'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A','Ă' => 'A', 'Æ' => 'AE', 'Ç' =>
				'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
				'Ï' => 'I', 'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' =>
				'O', 'Ő' => 'O', 'Ø' => 'O', 'Œ' => 'OE' ,'Ș' => 'S','Ț' => 'T', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U',
				'Ý' => 'Y', 'Þ' => 'TH', 'ß' => 'ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' =>
				'a', 'å' => 'a', 'ă' => 'a', 'æ' => 'ae', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
				'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' =>
				'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o', 'ø' => 'o', 'œ' => 'oe', 'ș' => 's', 'ț' => 't', 'ù' => 'u', 'ú' => 'u',
				'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th', 'ÿ' => 'y'
			),
			'latin_symbols' => array (
				'©' => '(c)'
			),
			'el' => array ( /* Greek */
				'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
				'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
				'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
				'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
				'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
				'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
				'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
				'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
				'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
				'Ϋ' => 'Y'
			),
			'tr' => array ( /* Turkish */
				'ş' => 's', 'Ş' => 'S', 'ı' => 'i', 'İ' => 'I', 'ç' => 'c', 'Ç' => 'C', 'ü' => 'u', 'Ü' => 'U',
				'ö' => 'o', 'Ö' => 'O', 'ğ' => 'g', 'Ğ' => 'G'
			),
			'bg' => array( /* Bulgarian */
				'Щ' => 'Sht', 'Ш' => 'Sh', 'Ч' => 'Ch', 'Ц' => 'C', 'Ю' => 'Yu', 'Я' => 'Ya',
				'Ж' => 'J',   'А' => 'A',  'Б' => 'B',  'В' => 'V', 'Г' => 'G',  'Д' => 'D',
				'Е' => 'E',   'З' => 'Z',  'И' => 'I',  'Й' => 'Y', 'К' => 'K',  'Л' => 'L',
				'М' => 'M',   'Н' => 'N',  'О' => 'O',  'П' => 'P', 'Р' => 'R',  'С' => 'S',
				'Т' => 'T',   'У' => 'U',  'Ф' => 'F',  'Х' => 'H', 'Ь' => '',   'Ъ' => 'A',
				'щ' => 'sht', 'ш' => 'sh', 'ч' => 'ch', 'ц' => 'c', 'ю' => 'yu', 'я' => 'ya',
				'ж' => 'j',   'а' => 'a',  'б' => 'b',  'в' => 'v', 'г' => 'g',  'д' => 'd',
				'е' => 'e',   'з' => 'z',  'и' => 'i',  'й' => 'y', 'к' => 'k',  'л' => 'l',
				'м' => 'm',   'н' => 'n',  'о' => 'o',  'п' => 'p', 'р' => 'r',  'с' => 's',
				'т' => 't',   'у' => 'u',  'ф' => 'f',  'х' => 'h', 'ь' => '',   'ъ' => 'a'
			),
			'ru' => array ( /* Russian */
				'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
				'з' => 'z', 'и' => 'i', 'й' => 'i', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
				'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
				'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
				'я' => 'ya',
				'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
				'З' => 'Z', 'И' => 'I', 'Й' => 'I', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
				'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
				'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
				'Я' => 'Ya',
				'№' => ''
			),
			'uk' => array ( /* Ukrainian */
				'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G', 'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g'
			),
	        'kk' => array ( /* Kazakh */
	            'Ә' => 'A', 'Ғ' => 'G', 'Қ' => 'Q', 'Ң' => 'N', 'Ө' => 'O', 'Ұ' => 'U', 'Ү' => 'U', 'Һ' => 'H',
	            'ә' => 'a', 'ғ' => 'g', 'қ' => 'q', 'ң' => 'n', 'ө' => 'o', 'ұ' => 'u', 'ү' => 'u', 'һ' => 'h',
	        ),
			'cs' => array ( /* Czech */
				'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
				'ž' => 'z', 'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T',
				'Ů' => 'U', 'Ž' => 'Z'
			),
			'pl' => array ( /* Polish */
				'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
				'ż' => 'z', 'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'O', 'Ś' => 'S',
				'Ź' => 'Z', 'Ż' => 'Z'
			),
			'ro' => array ( /* Romanian */
				'ă' => 'a', 'â' => 'a', 'î' => 'i', 'ș' => 's', 'ț' => 't', 'Ţ' => 'T', 'ţ' => 't'
			),
			'lv' => array ( /* Latvian */
				'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
				'š' => 's', 'ū' => 'u', 'ž' => 'z', 'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i',
				'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N', 'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z'
			),
			'lt' => array ( /* Lithuanian */
				'ą' => 'a', 'č' => 'c', 'ę' => 'e', 'ė' => 'e', 'į' => 'i', 'š' => 's', 'ų' => 'u', 'ū' => 'u', 'ž' => 'z',
				'Ą' => 'A', 'Č' => 'C', 'Ę' => 'E', 'Ė' => 'E', 'Į' => 'I', 'Š' => 'S', 'Ų' => 'U', 'Ū' => 'U', 'Ž' => 'Z'
			),
			'vn' => array ( /* Vietnamese */
				'Á' => 'A', 'À' => 'A', 'Ả' => 'A', 'Ã' => 'A', 'Ạ' => 'A', 'Ă' => 'A', 'Ắ' => 'A', 'Ằ' => 'A', 'Ẳ' => 'A', 'Ẵ' => 'A', 'Ặ' => 'A', 'Â' => 'A', 'Ấ' => 'A', 'Ầ' => 'A', 'Ẩ' => 'A', 'Ẫ' => 'A', 'Ậ' => 'A',
				'á' => 'a', 'à' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a', 'ă' => 'a', 'ắ' => 'a', 'ằ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a', 'â' => 'a', 'ấ' => 'a', 'ầ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
				'É' => 'E', 'È' => 'E', 'Ẻ' => 'E', 'Ẽ' => 'E', 'Ẹ' => 'E', 'Ê' => 'E', 'Ế' => 'E', 'Ề' => 'E', 'Ể' => 'E', 'Ễ' => 'E', 'Ệ' => 'E',
				'é' => 'e', 'è' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e', 'ê' => 'e', 'ế' => 'e', 'ề' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
				'Í' => 'I', 'Ì' => 'I', 'Ỉ' => 'I', 'Ĩ' => 'I', 'Ị' => 'I', 'í' => 'i', 'ì' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
				'Ó' => 'O', 'Ò' => 'O', 'Ỏ' => 'O', 'Õ' => 'O', 'Ọ' => 'O', 'Ô' => 'O', 'Ố' => 'O', 'Ồ' => 'O', 'Ổ' => 'O', 'Ỗ' => 'O', 'Ộ' => 'O', 'Ơ' => 'O', 'Ớ' => 'O', 'Ờ' => 'O', 'Ở' => 'O', 'Ỡ' => 'O', 'Ợ' => 'O',
				'ó' => 'o', 'ò' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o', 'ô' => 'o', 'ố' => 'o', 'ồ' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o', 'ơ' => 'o', 'ớ' => 'o', 'ờ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
				'Ú' => 'U', 'Ù' => 'U', 'Ủ' => 'U', 'Ũ' => 'U', 'Ụ' => 'U', 'Ư' => 'U', 'Ứ' => 'U', 'Ừ' => 'U', 'Ử' => 'U', 'Ữ' => 'U', 'Ự' => 'U',
				'ú' => 'u', 'ù' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u', 'ư' => 'u', 'ứ' => 'u', 'ừ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
				'Ý' => 'Y', 'Ỳ' => 'Y', 'Ỷ' => 'Y', 'Ỹ' => 'Y', 'Ỵ' => 'Y', 'ý' => 'y', 'ỳ' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
				'Đ' => 'D', 'đ' => 'd'
			),
			'ar' => array ( /* Arabic */
				'أ' => 'a', 'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'g', 'ح' => 'h', 'خ' => 'kh', 'د' => 'd',
				'ذ' => 'th', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh', 'ص' => 's', 'ض' => 'd', 'ط' => 't',
				'ظ' => 'th', 'ع' => 'aa', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'k', 'ك' => 'k', 'ل' => 'l', 'م' => 'm',
				'ن' => 'n', 'ه' => 'h', 'و' => 'o', 'ي' => 'y',
				'ا' => 'a', 'إ' => 'a', 'آ' => 'a', 'ؤ' => 'o', 'ئ' => 'y', 'ء' => 'aa',
				'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
			),
			'fa' => array ( /* Persian */
				'گ' => 'g', 'ژ' => 'j', 'پ' => 'p', 'چ' => 'ch', 'ی' => 'y', 'ک' => 'k',
				'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
			),
			'sr' => array ( /* Serbian */
				'ђ' => 'dj', 'ј' => 'j', 'љ' => 'lj', 'њ' => 'nj', 'ћ' => 'c', 'џ' => 'dz', 'đ' => 'dj',
				'Ђ' => 'Dj', 'Ј' => 'j', 'Љ' => 'Lj', 'Њ' => 'Nj', 'Ћ' => 'C', 'Џ' => 'Dz', 'Đ' => 'Dj'
			),
			'az' => array ( /* Azerbaijani */
				'ç' => 'c', 'ə' => 'e', 'ğ' => 'g', 'ı' => 'i', 'ö' => 'o', 'ş' => 's', 'ü' => 'u',
				'Ç' => 'C', 'Ə' => 'E', 'Ğ' => 'G', 'İ' => 'I', 'Ö' => 'O', 'Ş' => 'S', 'Ü' => 'U'
			)
		);
		/**
		 * List of words to remove from URLs.
		 */
		public static $remove_list = array (
			'a', 'an', 'as', 'at', 'before', 'but', 'by', 'for', 'from',
			'is', 'in', 'into', 'like', 'of', 'off', 'on', 'onto', 'per',
			'since', 'than', 'the', 'this', 'that', 'to', 'up', 'via',
			'with'
		);
		/**
		 * The character map.
		 */
		private static $map = array ();
		/**
		 * The character list as a string.
		 */
		private static $chars = '';
		/**
		 * The character list as a regular expression.
		 */
		private static $regex = '';
		/**
		 * The current language
		 */
		private static $language = '';
		/**
		 * Initializes the character map.
	     * @param string $language
		 */
		private static function init ($language = "")
	    {
			if (count (self::$map) > 0 && (($language == "") || ($language == self::$language))) {
				return;
			}
			/* Is a specific map associated with $language ? */
			if (isset(self::$maps[$language]) && is_array(self::$maps[$language])) {
				/* Move this map to end. This means it will have priority over others */
				$m = self::$maps[$language];
				unset(self::$maps[$language]);
				self::$maps[$language] = $m;
			}
			/* Reset static vars */
			self::$language = $language;
			self::$map = array();
			self::$chars = '';
			foreach (self::$maps as $map) {
				foreach ($map as $orig => $conv) {
					self::$map[$orig] = $conv;
					self::$chars .= $orig;
				}
			}
			self::$regex = '/[' . self::$chars . ']/u';
		}
		/**
		 * Add new characters to the list. `$map` should be a hash.
	     * @param array $map
		 */
		public static function add_chars ($map)
	    {
			if (! is_array ($map)) {
				throw new LogicException ('$map must be an associative array.');
			}
			self::$maps[] = $map;
			self::$map = array ();
			self::$chars = '';
		}
		/**
		 * Append words to the remove list. Accepts either single words
		 * or an array of words.
	     * @param mixed $words
		 */
		public static function remove_words ($words)
	    {
			$words = is_array ($words) ? $words : array ($words);
			self::$remove_list = array_merge (self::$remove_list, $words);
		}
		/**
		 * Transliterates characters to their ASCII equivalents.
	     * $language specifies a priority for a specific language.
	     * The latter is useful if languages have different rules for the same character.
	     * @param string $text
	     * @param string $language
	     * @return string
		 */
		public static function downcode ($text, $language = "")
	    {
			self::init ($language);
			if (preg_match_all (self::$regex, $text, $matches)) {
				for ($i = 0; $i < count ($matches[0]); $i++) {
					$char = $matches[0][$i];
					if (isset (self::$map[$char])) {
						$text = str_replace ($char, self::$map[$char], $text);
					}
				}
			}
			return $text;
		}
		/**
		 * Filters a string, e.g., "Petty theft" to "petty-theft"
		 * @param string $text The text to return filtered
		 * @param int $length The length (after filtering) of the string to be returned
		 * @param string $language The transliteration language, passed down to downcode()
		 * @param bool $file_name Whether there should be and additional filter considering this is a filename
		 * @param bool $use_remove_list Whether you want to remove specific elements previously set in self::$remove_list
		 * @param bool $lower_case Whether you want the filter to maintain casing or lowercase everything (default)
		 * @param bool $treat_underscore_as_space Treat underscore as space, so it will replaced with "-"
	     * @return string
		 */
		public static function filter ($text, $length = 60, $language = "", $file_name = false, $use_remove_list = true, $lower_case = true, $treat_underscore_as_space = true)
	    {
			$text = self::downcode ($text,$language);
			if ($use_remove_list) {
				// remove all these words from the string before urlifying
				$text = preg_replace ('/\b(' . join ('|', self::$remove_list) . ')\b/i', '', $text);
			}
			// if downcode doesn't hit, the char will be stripped here
			$remove_pattern = ($file_name) ? '/[^_\-.\-a-zA-Z0-9\s]/u' : '/[^\s_\-a-zA-Z0-9]/u';
			$text = preg_replace ($remove_pattern, '', $text); // remove unneeded chars
			if ($treat_underscore_as_space) {
			    	$text = str_replace ('_', ' ', $text);             // treat underscores as spaces
			}
			$text = preg_replace ('/^\s+|\s+$/u', '', $text);  // trim leading/trailing spaces
			$text = preg_replace ('/[-\s]+/u', '-', $text);    // convert spaces to hyphens
			if ($lower_case) {
				$text = strtolower ($text);                        // convert to lowercase
			}
			return trim (substr ($text, 0, $length), '-');     // trim to first $length chars
		}
		/**
		 * Alias of `URLify::downcode()`.
		 */
		public static function transliterate ($text)
	    {
			return self::downcode ($text);
		}
	}