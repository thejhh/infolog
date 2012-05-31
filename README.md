infolog
=======

This is a simple event logger designed for [Vectorama's](http://www.vectorama.info) Infodesk.

At the moment this app is only intended for Google Chrome browser and use on any other browser might have some bugs lurking around.

Licence
-------

Main code is under [the MIT license](https://github.com/jheusala/infolog/blob/master/LICENSE.txt).

The project uses and includes some external 3rd party libraries:

* [require.js](http://requirejs.org/) under MIT or new BSD licence
* [jquery](http://jquery.com/) under MIT, BSD or GPL licence
* [bootstrap](http://twitter.github.com/bootstrap/) under the Apache License, Version 2.0
* [showdown.js](https://github.com/coreyti/showdown) under BSD-style licence

TODO
----

* Support for exporting by RSS/Atom feed
* Option to delete newest events
* Format links from messages into HTML links
* When element is foo#bar -- should it convert it as hashtag or not?
* Support for nicks/usernames (maybe by color)
* Reset search form field after use + add better notification when events are filtered by search
* Better search engine
* Syncronize clock to SQL server's time
* Add REMOTE_ADDR into each event

Known bugs
----------

* IE is reported to miss events from client when written random#paskaa
