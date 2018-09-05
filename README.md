## DaDaDev Fax

Simple Fax application running on PHP7.2 and using Twilio as SDK. 

### Simple Usage

1. Just create yourself a twilio.com account and buy a number which is capable of receiving fax.

2. Configure your .env using the format in .env.dist. The important fields are:
```
APP_ENV=prod
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name

###> App Settings ###
# currently only 'de' and 'en' are supported
APP_LANGUAGE=en

###> Twilio Account ###
TWILIO_ACCOUNT_SID=YourAccountSID
TWILIO_ACCOUNT_AUTH_TOKEN=YourAuthToken
TWILIO_ACCOUNT_SENDING_NO=Your Registered Phone Number in E.164 Format
###< Twilio Account ###

###> Fax Document Saving ###
# Relative from project directory
FAX_DOCUMENT_SAVE_PATH=../secure/documents/
###< Fax Document Saving ###
###> App Settings ###
```

3. Setup the application on your webserver and init your database using commandline:
bin/console doctrine:migrations:migrate

4. Generate a password using: bin/console security:encode-password

5. On database level add your user using following statement:
```
INSERT INTO users (email, password, forename, surname, created)
VALUES ('Your Email Address',
        'Password Generated Previously',
        'Firstname',
        'Lastname',
        NOW());
```

6. Now after initializing everything there are 2 cronjobs that you would need or want to be implemented:
```
# These should run every minute:
* * * * * bin/console app:fax:retrieve-files
* * * * * bin/console app:fax:update-status
```

7. And last but not least you have to setup your twilio phone number to receive fax and call a webhook in the following path:
```
http(s)://yourDomain/api/twilio/fax-receive
```