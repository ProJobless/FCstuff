FCstuff
=======

An experimental social-networking website built on CodeIgniter.

Requirements
------------

* PHP 5.3 or newer.
* MySQL 5.5 or newer.

Getting Started
---------------

1. Grab a copy of the FCstuff repository.

    ```
    $ git clone https://github.com/abhijitrucks/fcstuff.git
    ```

2. Enter the `config` directory.

    ```
    $ cd fcstuff/application/config
    ```

3. Make copies of `project.default.php` and `database.default.php`.

    ```
    $ cp project.default.php project.php
    $ cp database.default.php database.php
    ```

4. Configure your settings in `project.php` and `database.php`.

5. Run the database migrations.

    ```
    $ curl http://localhost/fcstuff/migrations
    ```

License
-------

The MIT License (MIT)

Copyright (c) 2013 FCstuff

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
