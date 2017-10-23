![Communibase](https://www.communibase.nl/img/siteLogo.png "Communibase")

# Wordpress Communibase API Support

Adds Communibase API support to Wordpress.

[Communibase](https://www.communibase.nl) is a paid service for community/association/club/society membership administration. See [https://www.communibase.nl](https://www.communibase.nl) for connection details.

This plugin currently adds a setting screen for setting the API-key / host and a `WP_Communibase_Connector` delegate that can be used to access the API.
`WP_Communibase_Connector` delegates to the [Communibase PHP API Connector](https://github.com/kingsquare/communibase-connector-php). The delegate currently only adds a [Transient_API](https://codex.wordpress.org/Transients_API) caching layer and a few convenience methods. It is possible to use the `\Communibase\Connector` directly.

Status: wip, pre-org-internal

## TODO

 - [x] add license
 - [ ] WP Plugin Reqs https://codex.wordpress.org/Writing_a_Plugin
    - [ ] WP Coding Standards https://make.wordpress.org/core/handbook/best-practices/coding-standards/
 - [ ] Wordpress Plugin Registration
    - [ ] http://jeremypry.com/wordpress-plugin-development-with-github/
 - [ ] CI (wercker?)
   - [ ] tests (against top x (5?) wordpress versions)
   - [ ] generate readme.txt from README.md (?)
   - [ ] build & publish dist to [wordpress plugin directory](https://wordpress.org/plugins/developers/add/)
     - [ ] http://jeremypry.com/wordpress-plugin-development-with-github/
 - [ ] move to org
