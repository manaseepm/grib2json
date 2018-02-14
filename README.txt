This script connects to the noaa website to download GRIB2 binary files from given location and converts them to json.

Steps for installation
1. Download the source to directory of your choice

Usage
1. Execute the script as $php grib2jsonmulti.php
The json files will be stored in /json folder

Folder structure
csv - used to store intermediary csv files created by degrib
downloads - used to stored downloaded binary files from noaa website
json - used to store final json output
log - log files
src - source code

config.ini - configuration variables for the script