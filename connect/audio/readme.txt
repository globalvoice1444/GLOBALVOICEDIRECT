Please make sure that web callback is configured for your environment. Contact 'support@telinta.com' for details.
Supported browsers are the latest versions of FireFox, Google Chrome, Internet Explorer, Opera.

To configure the tool please open the 'check.php' file using any text editor and set the following variables:

* $host = <mybilling.yourdomain.com or IP address>;
* $login = <your_admin_login>;
* $password = <your_admin_password>;
* $um = <um.yourdomain.com>;
* $limit = <number_of_phone_numbers_to_process_for_csv_file_upload> (default is 100);

Save the file and upload the 'audio_brodcast' folder to your web server.

Open the 'http://<your_domain>/<path_to_the_tool>/audio_broadcast/' link to access the tool web interface.

Fill in the form fields as follows:

* 'Account' : <account_id_used_for_callback>;
* 'Password' : <account_password_used_for_callback>;
* 'IVR number' : <account_id_with_preconfigured_auto-attendant> (contact 'support@telinta.com' for details);
* 'Numbers list' : <list_of_called_numbers>;

Click the 'Connect' button to start audio broadcast.

It is possible to upload a list of called phone numbers via the upload form. 
Simply click on the 'Browse' button and select the appropriate .CSV file.
Once the upload process is completed you will receive the upload status message.
Please note that only numbers from the first column of .CSV file are processed.
