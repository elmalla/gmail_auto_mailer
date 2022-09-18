## GMAIL Auto sender   

This project can import company emails from csv files into a mysql DB then send emails using PHPmailer library every set interval. 

I used GMAIL to send my emails to avoid them being marked as a spam and I also limited the number of CC and BCC for the same reason.

I also used the code to send emails from custom domains (ex. linedemails.com).   

The code was used to send my CV & cover letter to over 50,000 company.

Cover letters (.txt) & CV (*.pdf)  of different versions can be placed in \uploads\attachments folder.
 
Call the cron_mail_Prod.bat file from the windows task scheduler and it will start sending emails from Mysql table name : scheduled_emails
 
When we have company details in a csv file then it is saved in the companies table:
![alt text](https://github.com/elmalla/gmail_auto_mailer/blob/main/images/companies.png?raw=true)


While all the emails is saved in the emails_master table:

![alt text](https://github.com/elmalla/search_engine_email_harvester/blob/main/images/emails.png?raw=true)

The project was developed in 2016.

*GMAIL now might have restrictions that prevent such usage. 
