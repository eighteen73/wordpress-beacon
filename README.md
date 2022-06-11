# Website Monitor for WordPress

Gathers data that's useful for diagnostic checks. Nothing collected is PII nor does it contain information that would be otherwise considered as highly sensitive.

## Installation

Assuming you are using a Composer-based WordPress project:

1. Run `composer install website-monitor`
2. Activate the plugin in your CMS

## Behaviour

The plugin requires no configuraiton. Once activated it sends data to our monitoring server every 3 hours. 

## Included Data

You can see exactly what it collects by clicking the "Copy data to clipboard" on the Plugins page. This put the exact JSON tha is sent to our server into your clipboard.

As an indication of the type of data, it includes:

* WordPress version
* Installed plugins and versions
* Hosting data (server version, PHP version, etc.)
* Git repo information if applicable
