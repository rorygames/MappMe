MappMe
======

Mapp (Mapping) Me (Myself)
Open source personal mapping software using Google Maps.

Information
======
MappMe is designed and created to be personal mapping software that runs on your own server. It only ever communicates with your specified server and the data never leaves it, you have full control of everything. Data is sent from your mobile device to the server, leaveraging the GPS location information from it.
You can customise and edit everything about it from the access code length to the settings that users can change.

Mobile Application - MappMe Transmitter
======
The mobile application is availabe from the Google Play store for Android devices.
https://play.google.com/store/apps/details?id=com.rorywebdev.MappMe
It requires Android 1.6+ and uses PhoneGap. The app itself has only been tested on devices running Android 4.0 and higher so there may be issues with lower devices.

Requirements
======
There are a few requirements for MappMe to work correctly on your server, they are however relatively commonly found on most newer server installations.
- PHP 5.1+ (requires the PDO plugin and OOP class structure)
- MySQL 5+
- Internet connected (required for sending mapping data from the mobile client)

You will also need a Google Developers API key for the usage of the map on the server.
They can be easily obtained by following this tutorial by Google:
https://developers.google.com/maps/documentation/javascript/tutorial#api_key

Features
======
- Fully guided installation process, install it to any directory and MappMe will generate and create the rest of the information.
- Tiered user system, administrator and standard user accounts that offer different options.
- Customisable Google Maps interface, add polylines, custom markers and user selected limits.
- Extensive user feature list, turn items on or off easily and regenerate items such as passwords and access codes
- Secure data authentication, access code based data sending prevents the need for usernames and passwords on mobile devices.
- Reverse geodecoding options, see the (almost!) exact address of each marker you place.

Features Coming Soon
======
- Forgotten password system, access code based to ensure the correct user.
- Reverse geodecoding options, change the level of accuracy of your markers (street address > town > country etc.)
- HTTPS options, globally enable or disable HTTPS for the entire system.

More Information?
======
If you're looking for more information feel free to message me on GitHub.
