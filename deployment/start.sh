#!/usr/bin/env bash

build/HelperScripts/createuserfiles

chown -R www:www /www/user_privileges

php modules/cbupdater/loadapplychanges.php 
php modules/cbupdater/loadapplychanges.php 

/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf