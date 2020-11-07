#!/bin/bash
pgrep -f php-dubbo-diaophp-provider | xargs kill -9
echo "1"
sleep 1
php ../diaophp-provider-api/index.php /service/provider/starter