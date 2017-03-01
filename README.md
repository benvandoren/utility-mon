# UtilityMon

Essentially this parses utility information and saves it to a database.

rtlamr reads from rtl_tcp and creates a stream of any utility meter information it finds. I then parse it to determine current watts being used and save the current usage along with the actual meter reading so that stats can be calculated. The python read the stream and save the data, the php files read it from the database.

There is a forced granularity of 5 minutes between readings per customer to minimize extreme readings, if there hasn't been a measurable change continue to wait.

The php code reads it from the database and outputs in a helpful manner. Allowing you to monitor yourself and neighbors usage.

## Pre Requisites:
1. Linux required, but I've had success passing the usb device to a VirtualBox VM.

1. Install rtl-sdr package, blacklist the necessary drivers, add udev rules so you can access the sdr as a non privileged user (otherwise you have to run as root). This is described for both CentOS and Arch (in minimal detail) at my wiki: https://wiki.bvd.io/wiki/RTL_SDR

1. Install the LAMP stack, that's also on the wiki, but there are honestly better guides tailored to whatever OS you have.

## Getting Started:
1. Download / extract the files. How you organize your web server is up to you. If you aren't sure, the easiest thing to do is simply place the folder in your web directory, likely /var/www/html/\<place utility-mon here\>.

1. Using MySQL create the DB "UtilityMon" and user "utility_mon", and grant the privilege to read and write to the db.

1. Attempt to access the web page at http://\<hostname or ip address\>/utility-mon/index.php. It should be a mostly blank page since no data has been collected. If you structure the web directory different you may need to modify the url to style.css in header.php and script.js in meter.php.

1. Rename dbConnect.php.sample and dbConnect.py.sample without ".sample" and fill in password and hostname information.

1. Run the file CreateTable.py, this will create the table schema to be used. *Note that the program must be started from within the container directory (eg. with ./CreateTable.py) because it sources dbConnect.py

1. Start utility-mon.py, after the initial startup dialog there should be no output until at least 5 minutes has passed.

1. Possible Errors: You might see "Error: Received a corrupted packet from stream" this means the variables could not be extracted (using regex) from the line (usually because it's blank or giberish indicating a problem with the underlying stream). If you see it on occasion it's fine, if it's nonstop errors then restart the program. Any output like "ll+, now 496" is from rtl_tcp and will also warrant restarting the program. With the new changes it appears to start consistently. *Note that the program must be started from within the container directory (eg. with ./utility-mon.py) because it sources dbConnect.py

1. After 5 minutes you should see console output like "Customer 423207035 Using 1076.411960 watts. 90 Wh / 301 s". This means it's working and data is being written to the database. Again access the main page and you should see meter information that has been collected, follow the user id url for detailed information.

## Significant Changes:

- Simply start utility-mon.py and the underlying rtl_tcp and rtlamr will start on their own. I solved the problem that was preventing me from doing this originally by sleeping 15 seconds after rtl_tcp and 5 seconds after rtlamr. I also use subprocess instead of os to make the system calls. All in all it actually starts consistently now and with only a single command.

## Other:

At 43k datapoints my database is 2.5 MB


## Possible Improvements:

- Start without issues / recover from crashes and stream errors.
- Create a table for each unique customer instead of one giant table
- Consolidate data on a monthly basis or so, since individual data points are less important with time
- Track by all time, year, last n months, month, week, day, etc.
- Graphs and more interactivity described above

## Issues:

- mysql in python was a pita for some reason, the library I use works so I aint changing it.
Should start rtlamr ~15 seconds after rtl_tcp
- Handle crashes better, it runs pretty stable. Only crashed because of 2 id's of different meter types, I now append the type to the id. Ideally should restart without any user intervention.
- Dumping everything into one big table, I worry it will get too big and bulky.
- A few oddball readings, mostly because of not enough data. Also clearly meter type 4 is not the same kind of electric meter as all the other ones. Gas and Water I just guessed at the units - cubic feet / sec
