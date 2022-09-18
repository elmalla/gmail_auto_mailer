@ECHO ON
echo [%date% – %time%] Log start > %temp%\cron_backup.log
c:

cd C:\xampp\htdocs\CI_306_Linked_Em_Production\
mysqldump ci_linked_e_m -uroot -padmin > C:\xampp\htdocs\CI_306_Linked_Em_Production\backup\db\ci_linked_e_m.sql
REM C:\xampp\mysql\bin\mysql.exe -uroot -padmin -s -N -e "SHOW DATABASES" | for /F "usebackq" %%D in (`findstr /V "information_schema performance_schema"`)do mysqldump %%D -uroot -padmin > C:\xampp\htdocs\CI_306_Linked_Em\backup\db\%%D.sql
for %%F in (C:\xampp\htdocs\CI_306_Linked_Em_Production\backup\db\ci_linked_e_m.sql) do set file=%%~fF
for /f "tokens=2 delims==" %%I in ('wmic datafile where name^="%file:\=\\%" get lastmodified /format:list') do set datetime=%%I
echo %datetime%
ECHO Batch file return code: %ERRORLEVEL% >> %temp%\cron_backup.log