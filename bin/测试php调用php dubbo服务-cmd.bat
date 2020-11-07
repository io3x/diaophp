@echo off
taskkill /f /im php.exe
php ../diaophp-consumer-web/index.php /consumer/cmer/m3 &
php ../diaophp-consumer-web/index.php /consumer/cmer/m4
pause