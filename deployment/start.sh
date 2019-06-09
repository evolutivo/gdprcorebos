#!/usr/bin/env bash

build/HelperScripts/createuserfiles

chown -R www:www /www/user_privileges

php modules/cbupdater/loadapplychanges.php 
php modules/cbupdater/loadapplychanges.php

cp -r /tmp_storage/. /www/storage
chown -R www:www /www/storage

/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf