=== Communibase API Support ===
Tags: communibase
Requires PHP: 5.5
Requires at least: 4.6
Tested up to: 4.8
Stable tag: 4.6
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Author: aubz
Contributors: aubz

Communibase API Support

== Description ==

[Communibase](https://www.communibase.nl) is a paid service for community/association/club/society membership administration. See [https://www.communibase.nl](https://www.communibase.nl) for connection details.

This plugin currently adds a setting screen for setting the API-key / host and a `WP_Communibase_Connector` delegate that can be used to access the API.
`WP_Communibase_Connector` delegates to the [Communibase PHP API Connector](https://github.com/kingsquare/communibase-connector-php). The delegate currently only adds a [Transient_API](https://codex.wordpress.org/Transients_API) caching layer and a few convenience methods. It is possible to use the `\Communibase\Connector` directly.

== Screenshots ==
TODO

== Frequently Asked Questions ==
TODO on dist build add from latest wiki

== Upgrade Notice ==
TODO on dist build add from latest release

== Changelog ==
TODO on dist build add from latest release/commits
