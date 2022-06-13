# Website Condition for WordPress

This plugin gathers data that's useful for diagnostic checks. Nothing collected is PII nor does it contain information that would be otherwise considered as highly sensitive.

## Installation

### Using Composer

1. Run `composer require eighteen73/wordpress-condition-report`
2. Activate the plugin in your CMS as normal

Once activated it sends data to our monitoring server every 3 hours.

### Without Composer

We highly encourage you to install the plugin using Composer where possible. On website's that are not package-managed it can be unzipped into the plugins directory though.

## Configuration

Add the following configuration so the plugin knows where to send the data:

```php
define( 'CONDITION_REPORT_URL', 'https://example.com/your/api/endpoint' );
```

You may also set `CONDITION_REPORT_INTERVAL` to change the number of seconds between runs (default is 3 hours) and use the filter `condition_report_headers` to adjust the headers sent to the URL above.

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
