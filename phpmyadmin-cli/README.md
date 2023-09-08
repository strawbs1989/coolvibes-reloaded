phpMyAdmin command-line interface
=================================

phpmyadmin-cli - bringing phpMyAdmin to the command-line.

MySQL databases are often managed using the popular administration tool 
[phpMyAdmin](http://www.phpmyadmin.net/). Unfortunately, automation and 
scripting of the commands available in the web-based interface of phpMyAdmin
can be cumbersome.

phpmyadmin-cli provides a command-line interface for phpMyAdmin, providing easy 
command-line access to your MySQL database. Commands given on stdin are handed 
to phpMyAdmin to be executed on the MySQL database. The results of these 
commands are then printed to stdout. An interactive shell is also available. 

phpmyadmin-cli is written in [Python](http://www.python.org/).


Requirements
------------

* Python 2.6+
* [Requests](http://www.python-requests.org/)
* [PTable](https://github.com/kxxoling/PTable)
* [phpMyAdmin](http://www.phpmyadmin.net/) 3.x or 4.x


Installation
------------

On most UNIX-like systems, you can install phpmyadmin-cli by running one of the 
following install commands as root or by using sudo.

``` sh
git clone git://github.com/fdev/phpmyadmin-cli.git
cd phpmyadmin-cli
python setup.py install
```

*or*

``` sh
pip install git+http://github.com/fdev/phpmyadmin-cli
```


Usage
-----

```
Usage: phpmyadmin-cli [OPTIONS] database
  -e, --execute=name  Execute command and quit.
  -E, --export=table  Export specified tables, can be used multiple times.
  -A, --export-all    Export all tables.
  -h, --help          Display this help and exit.
  -l, --location=url  Location of phpMyAdmin (http://localhost/phpmyadmin/).
  -p                  Prompt for password to use.
  --password=name     Password to use.
  -s, --ssl-ignore    Ignore bad SSL certificates.
  -t, --timeout=n     Http request timeout in seconds.
  -u, --user=name     User for login if not current user.
  -V, --version       Output version information and exit.
```


Examples
--------

**Import a sql file**
```
$ phpmyadmin-cli testdatabase < database.sql
```

**Export an entire database**

```
$ phpmyadmin-cli --export-all testdatabase > database.sql
```

**Export specific tables**

```
$ phpmyadmin-cli --export article testdatabase > articles.sql
```

**Execute a single query**
```
$ phpmyadmin-cli -e 'SELECT di FROM article' testdatabase
ERROR #1054 - Unknown column 'di' in 'field list'
$ phpmyadmin-cli -e 'SELECT id FROM article' testdatabase
"id"
"1"
"2"
"3"
"4"
"5"
```

**Using the interactive shell**
```
$ phpmyadmin-cli testdatabase
Welcome to the phpMyAdmin command-line interface.

Copyright (c) 2014, fdev.nl. All rights reserved.

This application is not affiliated with or endorsed
by the phpMyAdmin Project or its trademark owners.

phpmyadmin> SHOW TABLES;
+------------------------+
| Tables_in_testdatabase |
+------------------------+
| article                |
| comment                |
| user                   |
| access_log             |
+------------------------+
Query OK, 4 rows (0.05 sec)

phpmyadmin> DESCRIBE article;
+------------------+--------------+------+-----+---------+----------------+
| Field            | Type         | Null | Key | Default | Extra          |
+------------------+--------------+------+-----+---------+----------------+
| id               | int(11)      | NO   | PRI | NULL    | auto_increment |
| title            | varchar(255) | NO   |     | NULL    |                |
| content          | text         | NO   |     | NULL    |                |
| created          | datetime     | NO   | MUL | NULL    |                |
| created_by       | int(11)      | NO   | MUL | 0       |                |
+------------------+--------------+------+-----+---------+----------------+
Query OK, 5 rows (0.06 sec)

phpmyadmin> SELECT id, title FROM article ORDER BY title;
+----+----------------+
| id | title          |
+----+----------------+
| 5  | Fifth article  |
| 1  | First article  |
| 4  | Fourth article |
| 2  | Second article |
| 3  | Third article  |
+----+----------------+
Query OK, 5 rows (0.03 sec)
```


Known limitations
-----------------

* No easy way to switch between different databases.
* Will probably break when using a non-default phpMyAdmin theme.
* When using stdin there will be no output (you *can* use `--execute`).


Compatibility
-------------
While phpmyadmin-cli should work with phpMyAdmin 3.x and 4.x, it has only been 
tested with on 3.3.10deb1, 3.4.4, 3.5.8.1deb1 and 4.1.11.


Frequently Asked Questions
--------------------------
**Q:** Does phpmyadmin-cli work over https?

**A:** Yes it does. You can ignore bad certificates using the 
`--ssl-ignore` option.


**Q:** Was this an April Fools joke?

**A:** Yes, you should obviously use the mysql command when available. 
However, the application does fully work as described and, unfortunately, actual 
use cases for this application do exist.


*This application is not affiliated with or endorsed by the phpMyAdmin Project 
or its trademark owners.*

