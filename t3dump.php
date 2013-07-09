<?php 

/**
 * Typo3 DB Dump Script 
 * This Script creates a SQL dump of your mysql Datebase
 * it uses localconf.php to detect database setting and
 * mysqldump to create the dump of the databse.
 * 
 * version: 1.3
 */
 

### Configuration Options
$path_msqldump =''; /* 	this is only needet if mysqldump is not in PATH envirionment variable (c:\xampp\mysql\bin\mysqldump.exe) */
$overwrite_password =''; /* If this is set this password is used instad of the install tool password (md5) */
$path_localconf ='localconf.php'; // include, path for localconf
$controllBytes = 200; // bytes to show from the end of the dump;
$path_dump_store = dirname(__FILE__);

$excludeCacheTables=true; // if setzt the ignore table Pattern is used to exclude chache tables
$ignoreTablePattern = '/^cache_.+/i'; // This is a preg match pattern of tablenames to be ignored

$T3_DUMP_VERSION ='1.3'; // just the version string it shows
### Starting Processing
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>T3 MySQL Dump Script <?php echo $T3_DUMP_VERSION; ?></title>
<style type="text/css">
	.error {color:red;}
	.control-output {
		margin:10px;
		padding:10px;
		background:#eee;
		border:1px solid #999;
	}
</style>
</head>
<body>
<?php 

if(is_readable($path_localconf)){
	require_once($path_localconf);
	$checkpw = $TYPO3_CONF_VARS['BE']['installToolPassword'];
	if($overwrite_password != ''){
		$checkpw=md5($overwrite_password);
	}
	if(!empty($_REQUEST['pwd']) and md5($_REQUEST['pwd']) == $checkpw){ 
		$dumpname = $path_dump_store .'/'. date('Y-m-d-His-').$typo_db;

		// TODO: create table list to dump
		// --tables
		$cacheStructureTables = ''; 
		$dumpTableString = '';
		if($excludeCacheTables){
			$mysqli = new mysqli($typo_db_host,$typo_db_username,$typo_db_password,$typo_db);
			if ($mysqli->connect_errno) {
				printf("Connect failed: %s\n", $mysqli->connect_error);
				exit();
			}
			
			if($result = $mysqli->query('SHOW TABLES')){
				$dumpTableString = '--tables ';
				$cacheTablesString = '--tables '; 
				while ($tableResult = $result->fetch_array()) {
					$tableName = $tableResult[0];
	    			if(!preg_match($ignoreTablePattern, $tableName)){
	    				$dumpTableString .= "$tableName "; 
	    			}else{
	    				$cacheTablesString .= "$tableName ";
	    			}
				}
			}
		}

		$dumpOpt = " -u $typo_db_username -p$typo_db_password -h$typo_db_host -r\"{$dumpname}_db.sql\" $typo_db $dumpTableString";
		$dumpOptCache = " -u $typo_db_username -p$typo_db_password -h$typo_db_host -r\"{$dumpname}_cache.sql\" $typo_db --no-data $cacheTablesString";
		if(file_exists($dumpname.'_db.sql')){
			die("dumpfile already exists: {$dumpname}_db.sql");
		}
		if(empty($path_msqldump)){
			$path_msqldump = 'mysqldump';
		}
		$ausgabe = array();
		$returnVar = 0;
		echo "<h1>Creating dump</h1>"; //<p>using: $path_msqldump $dumpOpt <br/> $dumpname</p>
		$ret = exec($path_msqldump.$dumpOptCache ,$ausgabe, $returnVar );
		$ret = exec($path_msqldump.$dumpOpt ,$ausgabe, $returnVar );
		
		if(file_exists($dumpname.'_db.sql')){
			echo "<p> file <i>{$dumpname}_db.sql</i> created! <br />filesize: " .number_format(filesize($dumpname.'_db.sql'),0,',','.'). ' Bytes</p>';
			echo 'controll output (last lines of dump):<pre class="control-output">';
			$fp = fopen($dumpname.'_db.sql','r');
			fseek($fp,-$controllBytes,SEEK_END);
			$text = fread($fp,$controllBytes);
			fclose($fp);
			echo str_replace("Dump completed","<b>Dump completed</b>",htmlentities($text));
			echo '</pre>';
		}else{
			echo '<p class="error"><strong>ERROR: no Dump File Created!!!</strong></p>';
		}
		
	}else{
		echo <<< ENDFORM
			<form action="" method="post">
				<fieldset>
					<legend>Create Typo3 Database Dump $T3_DUMP_VERSION</legend>
					<p>the install tool password is required to create a dump</p>
					<label>Install tool Password: <input type="password"  value="" name="pwd" /></label><br />
					<!--<label><input type="checkbox" name="include_cached_tables" />Include Cache Tables in dump</label><br />-->
					<input type="submit" name="send" value="Create Dump"/>
				</fieldset>
			</form>
ENDFORM;
	}
}else{
	echo "Localconf.php not Readable / not Found";
}
?>
</body>
</html>