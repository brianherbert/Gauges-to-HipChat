Gauges to HipChat
===================
Sends a notification with simple stats to a HipChat room from your [Gauges](https://gaug.es) account.

Requirements & Installation
===========================
This is a PHP application, developed and tested on PHP 5.4.30.

This application uses [Hipchat v2 Api Client](https://github.com/gorkalaucirica/HipchatAPIv2Client), installed via composer.

Open config.php and set your API keys for your Gauges account, along with the notification token for the room you wish to send notifications to in HipChat.

In config.php, you will also need to specify the gauge ID you want to pull statistics from. You can do this by going to the gauge on the website in question, and pulling it out of the URL. (ie: https://secure.gaug.es/dashboard#/gauges/{GAUGE_ID HERE}/overview)

Make sure the "date" file has write permissions.

Running
=======
Simply run index.php from the command line (or the browser, but it's not intended to be used that way).

This script is intended to be run on a cron. It will only send a notification after the end of the day on Gauges so this only needs to be run once per day.