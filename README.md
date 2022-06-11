# Website Monitor for WordPress

Gathers data that's useful for diagnostic checks. Nothing collected is PII nor does it contain information that would be otherwise considered as highly sensitive.

## Installation

### Using Composer

This plugin is hosted in our private repo so you will need to ensure that is in your `composer.json` file.

```json
"repositories": [
  {
    "type": "composer",
    "url": "https://code.orphans.co.uk/pkg/wordpress"
  },
  ...
],
```

Then:

1. Run `composer install website-monitor`
2. Activate the plugin in your CMS as normal

### Without Composer

We hihgly encourage the plugin to be installed using Composer where possible.


## Behaviour

The plugin requires no configuraiton. Once activated it sends data to our monitoring server every 3 hours.

## Included Data

You can see exactly what it collects by clicking the "Copy data to clipboard" on the Plugins page. This put the exact JSON tha is sent to our server into your clipboard.

As an indication of the type of data, it includes:

* WordPress version
* Installed plugins and versions
* Hosting data (server version, PHP version, etc.)
* Git repo information if applicable
