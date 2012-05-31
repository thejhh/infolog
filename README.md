infolog
=======

This is a simple event logger designed for [Vectorama's](http://www.vectorama.info) Infodesk.

At the moment this app is only intended for Google Chrome browser and use on any other browser might have some bugs lurking around.

See also [live demo at infolog.in](http://infolog.in/). You can use any address *.infolog.in and it's new domain-based stream.

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

Priority:

1. Option to delete newest events
2. Support for nicks/usernames (maybe by color)
3. Support for adding labels to old events
4. Automatically add labels based on search to new messages?
5. HTML anchors to label search
6. Select box for predefined labels

Wish list:

* Support for exporting by RSS/Atom feed
* Format links from messages into HTML links
* When element is foo#bar -- should it convert it as hashtag or not?
* Better search engine (maybe using [Apache Solr](http://lucene.apache.org/solr/))
* Syncronize clock to SQL server's time
* Setup program
* Options to customize clock and other stuff
* CLI utility to post messages
* Clean up scripts/main.js
* Change title based on domain in use
* List public streams at http://infolog.in
* UI to "create" new domains

Done items:

* <del>Reset search form field after use + add better notification when events are filtered by search</del>
* <del>Focus automatically in to the message box</del>
* <del>Add REMOTE_ADDR into each event</del>
* <del>Automatic hostname based "room creation"</del>

Known bugs
----------

* <del>IE had cache problem.</del> Fixed now.
