@ECHO ON
echo [%date% – %time%] Log start > %temp%\cron_backup.log
c:

cd C:\xampp\htdocs\CI_306_Linked_Em\
mysqldump ci_linked_e_m -uroot -padmin > C:\xampp\htdocs\CI_306_Linked_Em\backup\db\ci_linked_e_m.sql
REM C:\xampp\mysql\bin\mysql.exe -uroot -padmin -s -N -e "SHOW DATABASES" | for /F "usebackq" %%D in (`findstr /V "information_schema performance_schema"`)do mysqldump %%D -uroot -padmin > C:\xampp\htdocs\CI_306_Linked_Em\backup\db\%%D.sql
ECHO Batch file return code: %ERRORLEVEL% >> %temp%\cron_backup.log