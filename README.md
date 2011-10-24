#Totsy.com

* [Totsy.com](http://www.totsy.com/)
* [Jira & feature requests](https://totsy1.jira.com/login.jsp)
* [Webmail](https://webmail.totsy.com) - company email
* [Lithium](http://lithify.me/) 
* [RockMongo - Development DB Manager](http://rockmongo.totsy.com/) 
* [NGINX](http://www.nginx.org) 
* [MONGODB](http://www.mongodb.org) 
* V3

## Description

###Where the savvy mom shops
Totsy offers moms on-the-go and moms-to-be access to brand-specific sales, up to 90% off retail, just for them and the kids, ages 0-8. 

##Development Team

* coming soon!

##Cloning Lithium (our PHP framework) and the Totsy Platform:

    git clone git@github.com:Totsy/Totsy-Platform.git local.totsy.com

Here is the basic usage: (NOTE: this isn't implemented yet! -eric)

    git clone --recursive git@github.com:Totsy/Totsy-Platform.git

The above command is the same as running: (NOTE: this isn't implemented yet! -eric)

    git clone git@github.com:Totsy/Totsy-Platform.git local.totsy.com
    cd local.totsy.com/
    git submodule init
    git submodule update

You can also shorten the last two commands to one: (NOTE: this isn't implemented yet! -eric)

    git submodule update --init

##Dev Environments

* NOTE: We use remote development environments i.e.: [eric.totsy.com](http://eric.totsy.com/) that are pre-enabled with pre-configured NGINX, MONGODB, LITHIUM, and Totsy Repos already setup. If you're new contact Pierre Davidoff pdavidoff@totsy.com for instructions on how to get started. You'll most likely need SSH Keys to begin coding, so follow this example on generating them: [SSH keys](http://help.github.com/mac-set-up-git/) 