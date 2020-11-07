@echo off
taskkill /f /im php.exe
php ../diaophp-consumer-web/index.php /consumer/cmer/m7
pause