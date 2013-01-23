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
* Set werserver document root to www folder
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
swift -A https://hubic.toorop.fr/auth/v1.0 -U YOU_HUBIC_LOGIN -K YOUR_HUBIC_PASSWORD list
default
default_segments
```

If you want to use [Cyberduck](http://cyberduck.ch/ "Swift client") as GUI, just put https://yourServer.tld as server (ie without /auth/v1.0) 

