Hipstergram
===========

JSON API and example frontend for fetching/displaying Instagram images posted to Twitter. Uses Twitter's search API, no authentication required.

Demo: http://hipstergram.lennux.nu

Installation
------------
Stick all the files somewhere under your webserver's document root. Enter the hashtags you want to search for in settings.json.

URLS:
+ / - The frontend
+ /recent.json - JSON feed of the last 20 images saved to the database.
+ /all.json - JSON feed of every image in the database
+ /tweet.json?tweet=<id> - The full tweet for image <id>

FILES:
+ index.php - Router
+ settings.json - Hashtag/tweet count settings
+ database.php - DB class
+ twitterquery.php - Twitter API class
+ instagram.php - Instagram API class
+ tweet.php - Tweet model class
+ pics.html - Frontend example application
+ hipstergram.js - Example javascript
+ db/hipstergram.db.dist - Database template (copied to db/hipstergram.db on first request)

Requirements
------------
+ PHP >= 5.3
+ PDO
+ sqlite3
