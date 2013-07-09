Typo3 Dump Script
=================

This script offers an easy way to create an database dump of an Typo3 Installation.
the script uses your install tool password to protects unauthorized persons from creating dumps on your system.


Requirments
-----------

  * PHP 5.0 or Higher 
  * mySQLdump (commandline tool should be normaly installed)
  * PHP needs to be able to call the exec() function.

Installation & Usage
--------------------

  - Copy the script to typo3conf/ directory of your typo3 installation.
  - call the script like http://example.com/typo3conf/t3dump.php
  - enter your install tool password and klick "Create Dump"
  - your database dump should be created.

Configuration (Optional)
------------------------

if you experience any problems with the "installation & Usage section" it might be you need to configure some 
details open the t3dump script. there should be a section labled "### Configuration Options" where you 
could set some details like the path to your mysqldump script. or another path for storing the sql dumps.

$path_msqldump 
	Allows you to set an path for the mysqldump Executable if you mysqldump is not in the PATH

$overwrite_password
	set an Alternate Password if you don't wan't to use the installtool password

$path_localconf
	the Path to the Local conf php if you dont install t3dump into typo3conf/ you need to fix this path

$controllBytes
	How Many Bytes to show at the end of an Dump to see if everything went fine.

$path_dump_store
	where to store the dumps

$excludeCacheTables
	should cache table data be dumped too?

$ignoreTablePattern 
	regularPatter to Identifiy cache Tables
