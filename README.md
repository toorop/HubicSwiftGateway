### WARNING DEPRECIATED 
use https://github.com/oderwat/hubic2swiftgate as alternative


HubicSwiftGateway
=================

HubicSwiftGateway is a PHP app that allows you to create an openStack Swift Gateway to Hubic storage

Warning
-------
This tool is not supported by OVH.

Requirements
---
* web server
* php with Curl extension

How to install ?
---
* Download this script to your web server
* Set document root to www folder
* make cache dir writable by your web server
* and.... that's all !
 
How to use it ?
---
* Get a swift client
* set server : your server
* login : your hubic login
* api key or passwd : your hubic passwd

For example, if this script is hosted at URL https://hubic.toorop.fr (and yes it is) and you want to use swiftCli :
```
swift -A https://hubic.toorop.fr -U YOU_HUBIC_LOGIN -K YOUR_HUBIC_PASSWORD list
default
default_segments
```

If you want to use a GUI client look at [Cyberduck](http://cyberduck.ch/ "GUI Swift client")

Thanks
---
To [Vincent Giersch](https://github.com/gierschv) who has made all the reverse engineering job.

