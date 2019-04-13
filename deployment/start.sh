#!/usr/bin/env bash

build/HelperScripts/createuserfiles

chown -R www:www /www/user_privileges

/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf