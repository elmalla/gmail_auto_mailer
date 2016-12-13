@ECHO OFF
c:
call :sub >C:\xampp\htdocs\CI_306_Linked_Em_Production\output.html
exit /b

:sub
cd C:\xampp\htdocs\CI_306_Linked_Em_Production\
php index.php admin_mailer cron_mail 1 mailer 4 gmail bat