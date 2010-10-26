<?php

	/**
	 * @todo Implement spl_autoload
	 * @todo Fix CmdLineParser so it integrates the functionality given in the function mergeConfig located in launcher.php
	 * @todo Analyze script performance with xdebug
	 * 
	 */

	define ("__CLASSPATH","class");

	error_reporting(E_ALL);

	function checkPHPVersion(){

		$version		= substr(PHP_VERSION,0,strpos(PHP_VERSION,"."));
		$subversion	= substr(PHP_VERSION,strpos(PHP_VERSION,".")+1);
		$subversion	= substr($subversion,0,strpos($subversion,"."));

		if($version != 5 || $subversion < 3){
			return FALSE;
			
		}

		return TRUE;

	}

	if(!checkPHPVersion()){

		echo "Sorry but you need at least version 5.3.0 in order to run aidSQL :(\n";
		exit(1);

	}

	//Interfaces
	require_once "interface/HttpAdapter.interface.php";
	require_once "interface/InjectionPlugin.interface.php";
	require_once "interface/Parser.interface.php";
	require_once "interface/Log.interface.php";

	//Classes
	require_once "class/aidsql/Crawler.class.php";
	require_once "class/aidsql/Runner.class.php";
	require_once "class/log/Logger.class.php";
	require_once "class/core/CmdLine.class.php";
	require_once "class/core/String.class.php";
	require_once "class/core/File.class.php";
	require_once "class/http/eCurl.class.php";
	require_once "class/google/GoogleSearch.class.php";
	require_once "config/config.php";
	
	//Parsers
	require_once "class/parser/Generic.parser.php";
	require_once "class/parser/TagMatcher.parser.php";
	require_once "class/parser/Dummy.parser.php";
	require_once "class/parser/MySQLError.parser.php";

	//Functions
	require_once "functions/launcher.functions.php";

	checkPHPVersion();

	$logger	=	new Logger();
	$logger->setEcho(TRUE);

	banner($logger);

	try {

		unset($_SERVER["argv"][0]);

		$sites			=	array();
		$links			=	array();

		$parameters		=	mergeConfig($_SERVER["argv"],"config/config.ini");
		$cmdParser		=	new CmdLineParser($config,$parameters);
		$parsedOptions	=	$cmdParser->getParsedOptions();

		if(isset($parsedOptions["log-save"])){
			$logger->setFilename($parsedOptions["log-save"]);
		}

		$logger->setColors($parsedOptions["colors"]);

		if(!empty($parsedOptions["url"])){
			$sites[0]	=	$parsedOptions["url"]; 
		}

		//Instance of the http adapter, shared by aggregation through all classes

		$httpAdapter	= 	new $parsedOptions["http-adapter"]();

		if(isset($parsedOptions["connect-timeout"])){

			$httpAdapter->setConnectTimeout($parsedOptions["connect-timeout"]);

		}

		if(isset($parsedOptions["request-interval"])&&$parsedOptions["request-interval"]>0){
			$httpAdapter->setRequestInterval($parsedOptions["request-interval"]);
		}

		if(isset($parsedOptions["log-prepend-date"])){
			$logger->useLogDate($parsedOptions["log-prepend-date"]);
		}

		//Check if youre bored and you just want to rule the world (?)
		/////////////////////////////////////////////////////////////////

		if(in_array("google",array_keys($parsedOptions))){

			$logger->setPrepend("[G00Gl3]");
			$logger->log("Googling ...",0,"light_green");

			sleep(2);			

			$google	=	new GoogleSearch($httpAdapter);

			$google->setQuery($parsedOptions["google"]);

			(isset($parsedOptions["google-language"])) ? $google->setLanguage($parsedOptions["google-language"]) : NULL;

			$start = (isset($parsedOptions["google-offset"])) ? $parsedOptions["google-offset"] : 0;

			$google->setStart($start);

			$sites = googleSearch($google);

			if($parsedOptions["google-shuffle-sites"]){
				shuffle($sites);
			}

			$logger->setPrepend("");

		}

		if(isset($parsedOptions["omit-sites"])){

			filterSites($sites,$logger,$parsedOptions["omit-sites"]);

		}

		//Check if url vars where passed,if not, we crawl the url
		/////////////////////////////////////////////////////////////////

		if(!in_array("urlvars",array_keys($parsedOptions))){

			$logger->setPrepend("[Crawler]");

			$httpAdapter->setMethod($parsedOptions["http-method"]);

			if(!sizeof($sites)){
				$logger->log("No sites :(!",1,"red");
				exit(1);
			}

			foreach($sites as $site){

				$httpAdapter->setUrl($site);

				$crawler			=	new aidsql\Crawler($httpAdapter,$logger);

				if(isset($parsedOptions["lpp"])){

					$crawler->setLinksPerPage($parsedOptions["lpp"]);

				}

				if(isset($parsedOptions["max-links"])){

					$crawler->setMaxLinks($parsedOptions["max-links"]);

				}

				if(isset($parsedOptions["page-types"])){
					$crawler->addPageTypes(explode(",",$parsedOptions["page-types"]));
				}

				if(isset($parsedOptions["omit-paths"])){

					$omitPaths = explode(",",$parsedOptions["omit-paths"]);
					$crawler->addOmitPaths($omitPaths);

				}

				if(isset($parsedOptions["omit-pages"])){

					$omitPages = explode(",",$parsedOptions["omit-pages"]);
					$crawler->addOmitPages($omitPages);

				}

				$crawler->crawl();

				$links			= $crawler->getLinks(TRUE);

				//Takes away all crawled links without any parameters (useless to us ... to this date)
				filterLinksWithoutParameters($links);

				$logger->setPrepend("[aidSQL]");

				//Test crawled links
				testLinks($links,$httpAdapter,$cmdParser,$logger);

				$logger->setPrepend("");

			}


		}else{

			//If urlvars was specified we will do whatever the user tells us to do

			$links = array($parsedOptions["url"]=>$parsedOptions["urlvars"]);

		}

	}catch(Exception $e){

		$logger->log($e->getMessage(),1,"light_red");

	}

	if(!sizeof($links)){

		$logger->log("Not enough links / No valid links (i.e no parameters) to perform injection :(");
		exit(1);

	}
	
	testLinks($links,$httpAdapter,$cmdParser,$logger);

?>
