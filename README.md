#Totsy.com

Where the savvy mom shops - Totsy offers moms on-the-go and moms-to-be access to brand-specific sales, up to 90% off retail, just for them and the kids, ages 0-8. 

##Development Resources

* [Totsy.com](http://www.totsy.com/)
* [Jira](https://totsy1.jira.com/login.jsp)
* [Gmail](https://webmail.totsy.com) 
* [Lithium](http://lithify.me/) 
* [RockMongo](http://rockmongo.totsy.com/) - dev access
* [NGINX](http://www.nginx.org) 
* [MONGODB](http://www.mongodb.org)

##Development Team

coming soon!

##Cloning Lithium / Totsy Platform:

    git clone git@github.com:Totsy/Totsy-Platform.git local.totsy.com

Here is the basic usage: (NOTE: this isn't implemented yet! -eric)

    git clone --recursive git@github.com:Totsy/Totsy-Platform.git

The above command is the same as running: (NOTE: this isn't implemented yet! -eric)

    git clone git@github.com:Totsy/Totsy-Platform.git local.totsy.com
    cd local.totsy.com/
    git submodule init
    git submodule update

Symbolic link the Libraries li3_ (name of library)

    ln -s /usr/local/sailthru-php5-client/ /var/www/<username>.totsy.com/libraries/sailthru


You can also shorten the last two commands to one: (NOTE: this isn't implemented yet! -eric)

    git submodule update --init

##Dev Environments

NOTE: We use remote development environments (example [eric.totsy.com](http://eric.totsy.com/)) that are pre-enabled with pre-configured NGINX, MONGODB, LITHIUM, and Totsy Repos already setup. If you're new contact Pierre Davidoff pdavidoff@totsy.com for instructions on how to get started. You'll most likely need SSH Keys to begin coding, so follow this example on generating them: [SSH keys](http://help.github.com/mac-set-up-git/) 