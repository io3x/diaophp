@echo off
php ../diaophp-framework-core/index.php /service/event_demo/event &
php ../diaophp-framework-core/index.php /service/event_demo/event_back &
php ../diaophp-framework-core/index.php /service/event_demo/bulk_event_back
pause