FCstuff
=======

An experimental social-networking website built on CodeIgniter.

Requirements
------------

* PHP version 5.1.6 or newer.
* A mysql, mysqli, postgre, odbc, mssql, sqlite or oci8 database.

Configurations
--------------

###Database connections

* Go to ./application/config

* Make a copy of database.default.php as database.php

* Change the following settings -

    + The hostname of your database server. Example:

        ```php
        $db['default']['hostname'] = 'localhost';
        ```

    + The username used to connect to the database. Example:

        ````php
        $db['default']['username'] = 'root';
        ````

    + The password used to connect to the database. Example:

        ```php
        $db['default']['password'] = 'root';
        ```

    + The name of the database you want to connect to. Example:

        ```php
        $db['default']['database'] = 'fcstuff';
        ```

    + The database type. Example:

        ```php
        $db['default']['dbdriver'] = 'mysql';
        ```

###General settings

* Make a copy of project.default.php as project.php

* Change the following settings -

    + The website url. Example:

        ```php
        $config['base_url'] = 'http://fcstuff.com/';
        ```

    + The threshhold for logging error messages. Example:

        ```php
        $config['log_threshold'] = 0;
        ```

###Database Migrations

* Run the migration script by visiting http://base_url/migrations/

License
-------

The MIT License (MIT)

Copyright (c) 2013 fcstuff

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to
use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
the Software, and to permit persons to whom the Software is furnished to do so,
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
