infolog
=======

This is a simple event logger designed for [Vectorama's](http://www.vectorama.info) Infodesk.

At the moment this app is only intended for Google Chrome browser and use on any other browser might have some bugs lurking around.

See also [live demo](http://dev.jhh.me/infolog/).

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
* <del>Reset search form field after use + add better notification when events are filtered by search</del>
* Better search engine (maybe using [Apache Solr](http://lucene.apache.org/solr/))
* Syncronize clock to SQL server's time
* Add REMOTE_ADDR into each event
* Setup program
* Focus automatically in to the message box
* Options to customize clock and other stuff
* Automatic hostname based "room creation"
* CLI utility to post messages
* Support to add labels to old events
* Automatically add labels based on search to new messages?
* Clean up scripts/main.js

Known bugs
----------

* IE is reported to miss events from client when written random#paskaa
