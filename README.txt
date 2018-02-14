*
* GRIB2 to JSON File Conversion Program 
* Author: Manasee Mahajan
* Date: 11th Feb 2018
*

** Overview **
This script connects to the noaa website to download GRIB2 binary files from given location and converts them to json.

** Steps for installation ** 
1. Download the source to directory of your choice
2. Ensure that the following folder structure exists - You will have to create empty folders marked below with a (N) next to their name

root/
	- /csv (N) #used to store intermediary csv files created by degrib
	- /downloads (N) #used to stored downloaded binary files from noaa website
	- /json (N) #used to store final json output
	- /log (N) #log files
	- /src #source code
	- .gitignore #ignore config for git repo
	- config.ini #configuration variables for the script
	- README.txt #this file.
	
** Steps to run the program **
1. Open Command Prompt
2. Navigate to the root folder above ($> cd path\to\root\folder)
3. Change directory to src ($> cd src)
4. Execute script using $> php grib2jsonmulti.php #note that it is assumed that php executable is in the PATH. If not, provide the full path to the php.exe file 
