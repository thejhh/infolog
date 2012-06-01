infolog
=======

A simple event/issue logger for a happening.

The intended use case for the app is to write down history what happens in a happening -- like in a conference or meetup or LAN party.

It's designed originally for [Vectorama 2012](http://www.vectorama.info) infodesk's internal use.

It's open source and you can setup your own secure private server.

See also [live demo at infolog.in](http://infolog.in/). You can use any address *.infolog.in and it's new domain-based stream.

It's implemented for Google Chrome browser, so other browsers might have some bugs lurking around. However I'm happy to fix any problems.

Licence
-------

Main code is under [the MIT license](https://github.com/jheusala/infolog/blob/master/LICENSE.txt).

The project uses and includes some external 3rd party libraries:

* [require.js](http://requirejs.org/) under MIT or new BSD licence
* [jquery](http://jquery.com/) under MIT, BSD or GPL licence
* [bootstrap](http://twitter.github.com/bootstrap/) under the Apache License, Version 2.0
* [showdown.js](https://github.com/coreyti/showdown) under BSD-style licence
* [moment.js](http://momentjs.com/) under MIT license

TODO
----

Priority:

1. Paging support
3. Support for nicks/usernames (maybe by color)
4. Support for adding labels to old events
5. Automatically add labels based on search to new messages?
6. Link for specific message
7. Link for specific search
8. Select box for predefined labels

Wish list:

* Format links from messages into HTML links
* When element is foo#bar -- should it convert it as hashtag or not?
* Better search engine (maybe using [Apache Solr](http://lucene.apache.org/solr/))
* Syncronize clock to SQL server's time
* Setup program
* Options to customize clock and other stuff
* CLI utility to post messages
* Clean up scripts/main.js
* List public streams at http://infolog.in
* UI to "create" new domains
* Remove deleted messages from browsers cache that didn't submit delete request

Done items:

* <del>Support for exporting by RSS/Atom feed</del>
* <del>Reset search form field after use + add better notification when events are filtered by search</del>
* <del>Focus automatically in to the message box</del>
* <del>Add REMOTE_ADDR into each event</del>
* <del>Automatic hostname based "room creation"</del>
* <del>Change title based on domain in use</del>
* <del>Option to delete newest events</del>
** Users can now delete their own messages 5 minutes after submit

Known bugs
----------

* <del>IE had cache problem</del>, fixed.
* <del>Problem with illegal UTF-8 characters</del>, fixed.
