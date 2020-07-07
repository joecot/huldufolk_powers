# Huldufolk Powers Generator

[Live Version](https://thehuldufolk.com/powers)

This is a set of scripts for generating reference pages (and by extension printable PDFs!) of the Huldufolk powers for a character.

After selecting the powers a character has, users are given a list of all their powers and their descriptions, along with a URL to view them. This page is also mobile friendly! This especially useful if you're generating pre-made characters for a game, such as with the [Huldufolk Bulk Sheets](https://github.com/joecot/huldufolk_bulk_sheets) scripts.

## Initital Setup
* Requirements: PHP, MySQL, a Web Server
* Create a mysql database
* copy `config.example.php` to `config.php` and fill it in with your database credentials
* If you're using my current power descriptions:
  * Import `huldufolk.sql` into your database (usually `mysql -uYourUser -p YourDatabase < huldufolk.sql`)
* If you're going to have your own power descriptions:
  * Hey, wait a second, who's writing this game anyway?!
  * Import `huldufolk-structureonly.sql` instead
  * Update `powers.csv` with your power descriptions
  * Run `import.php` to add them to the database

## Web Server configuration
You'll want to configure /p/ and /powers on your site to load powers.php . On nginx this is as simple as:
```
location /p/ {
	rewrite ^/p/(.*)$ /powers/$1 permanent;
}
location /powers/ {
	rewrite ^/(.*)$ /powers.php;
}
location ~ /powers$ {
	rewrite ^/(.*)$ /powers.php;
}
```