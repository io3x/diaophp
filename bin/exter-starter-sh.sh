#!/bin/bash
ps -ef | grep service/starter/init | grep -v grep | kill -9 `awk '{print $2}'`
echo "1"
sleep 1
php ../diaophp-framework-core/index.php /service/starter/init