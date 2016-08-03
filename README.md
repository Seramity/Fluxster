# Fluxster

Fluxster was a small social networking website where people can join, share, and follow other people. (Similar to Twitter)
People or businesses were able to use Fluxster to share content, follow users, and have their friends/customers keep up to date.

Anyone may clone this repo and test Fluxster out on their own.

## Installation
In order to test run Fluxster on your own, I recommend downloading a desktop web environment.
For Windows I recommend something like [WAMP](http://www.wampserver.com/en/)
For Mac: [MAMP](https://www.mamp.info/en/)
For all Windows, Mac and Linux: [XAMPP](https://www.apachefriends.org/index.html)

1. Install a desktop web environment that suits your needs. (If you are unsure how to do this there are many videos that clearly explain this process)
2. Make a copy of Fluxster into your "www" folder
3. Create a new database and import the "fluxster.sql" file
4. Open /includes/config.php and all the $conf variables to your mySQL fields
5. Open Fluxster in your browser (e.g. http://localhost/fluxster)
6. Enjoy!

### Troubleshoot
If you have any problems with creating an account, posting, or changing user settings do the following:

1. Open up phpMyAdmin (usually at http://localhost/phpmyadmin)
2. Open the "Variables" table
3. Find "sql mode" and click "Edit"
4. Remove "STRICT_ALL_TABLES"
5. Save

## Disclaimer
DO NOT run Fluxster in production!
Fluxster contains many security flaws and bad practices that can put user's data that is stored at risk.

For example: The login process is storing a user's username and MD5 hashed PASSWORD into cookies (Yikes!). Please mind this. This is an old project and was a huge learning experience for me.

Of course you can run Fluxster on your own desktop environment, But I highly advise to not run it on a live server.
