# Website Condition for WordPress

This plugin gathers data that's useful for diagnostic checks. Nothing collected is PII nor does it contain information that would be otherwise considered as highly sensitive.

## Installation

### Using Composer

1. Run `composer require eighteen73/wordpress-condition-report`
2. Activate the plugin in your CMS as normal

### Without Composer

We highly encourage you to install the plugin using Composer where possible. On website's that are not package-managed it can be unzipped into the plugins directory though.

## Behaviour

The plugin requires no configuration. Once activated it sends data to our monitoring server every 3 hours.

If you make a fork of this plugin for your own use, the time interval and destination URL for the data are both defined in `includes/classes/Cron.php`. They were hardcoded because this plugin is really intended for internal use only and we wanted to remove any configuration requirements.

## Included Data

You can see what information the plugin collects by clicking the "Copy data to clipboard" on the Plugins page. This puts the exact JSON that is sent to our server into your clipboard.

Data it includes things like the WordPress version, a list of running plugins and their versions, hosting data (server version, PHP version, etc.), and the Git repo information if applicable

Here is an example of the compiled data:

```json
{
  "cms": {
    "contact": "someone@example.com",
    "name": "wordpress",
    "version": "6.0"
  },
  "theme": {
    "name": "Pulsar",
    "uri": "https://github.com/eighteen73/pulsar",
    "version": "0.1.0"
  },
  "plugins": [
    {
      "active": true,
      "name": "nebula-autoloader",
      "title": "Nebula Autoloader",
      "uri": "https://github.com/eighteen73/nebula/",
      "version": "0.1.0"
    },
    {
      "active": true,
      "name": "wordpress-condition-report",
      "title": "eighteen73 Condition Report",
      "uri": "https://github.com/eighteen73/wordpress-condition-report",
      "version": "1.0.0"
    },
    ...
  ],
  "technical": {
    "git": {
      "last_commit_date": "Sat Jun 11 12:00:51 2022 +0100",
      "origin": "git@github.com:path_to/gitrepo",
      "path": "/local/path/to/gitrepo"
    },
    "os": {
      "architecture": "x86_64",
      "hostname": "example.com",
      "name": "Linux",
      "version": "4.15.0-167-generic"
    },
    "php": {
      "composer-dev": true,
      "interface": "fpm-fcgi",
      "version": "8.0.15"
    },
    "web": {
      "domain": "website.example.com",
      "https": true,
      "ip": "127.0.0.1",
      "path": "/local/path/to/gitrepo/web",
      "protocol": "HTTP/2.0",
      "server": "nginx/1.21.6",
      "url": "https://website.example.com"
    }
  }
}
```
