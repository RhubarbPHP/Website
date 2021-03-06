#!/bin/bash

if [ -d "/var/www/docs" ]; then
    rm -rf /var/deploying/docs/modules
    mv -f /var/www/docs/modules /var/deploying/docs/
fi

mv /var/www /var/old
mv /var/deploying /var/www
chown -R nginx: /var/www
rm -r /var/old