![WIISARI](/docs/readme-images/wiisari-logo.svg?raw=true&sanitize=true)

Wiisari is a web-based timeclock system. Wiisari works best using Chromium-based browser (made and tested on Google Chrome).
The app is currently only in finnish language.

Wiisari is originally based on [PHP Timeclock](http://timeclock.sourceforge.net/) and more specifically [UnitedTechGroup fork](https://github.com/UnitedTechGroup/timeclock) of it.

### Contents
- [About Wiisari](#about)
  - [Features](#features)
  - [User levels](#user-levels)
- [Installation](#installation)
  - [Requirements](#requirements)
- [Usage](#usage)
  - [Configuration](#configuration)
- [Screenshots](#some-screenshots)
- [Includes code from these projects](#includes-code-from-these-projects)

## About
Wiisari is a timeclock system for tracking employee working hours. It is completely web-based so that it can be used on any computer with a modern web-browser. Wiisari originates from PHP Timeclock so wiisari is also made with PHP, but resemblance to the original software is nowdays minimal. For example the whole UI is totally revamped and Wiisari features completely different user system. Also the database schema is not compatible with the original. Wiisari is also made with modern technologies, so newest versions of backend systems are compatible with it. Frontend also takes advantage of modern browser features.

### Features
Some of the key features of Wiisari currently are (administrative features are listed below)
- Every user of the system, be it either supervisor or employee, has their own account
- Every user belongs to one group and every group belongs to one office
- Every user is differentiated with user level that limits functionalities for single user, see [this](#user-levels) section
- The app works on any computer with web-browser (that has a connection to the server)
- Everyone can see their own work hours from the web app (requires logging in)
- Possibility to view work hours as quick-glance graphs or as a full reports with custom timeframe and with a possibilty to download CSV
- Can punch in or out using the web app, so no need to go to a central timeclocking station
- Can attach a message to single punch
- Has a separate mode for central clocking-station for those that don't have a personal internet connected computer
- Clocking station supports barcode-readers

Some of the key administrative features:
- All administrative operations are also handled from the same web-interface
- Can easily create new users, groups and offices from the web-app
- Intuitive UI for filtering, sorting and finding users, groups and offices with many different variables
- Each supervisor supervises a set of groups, so that the supervisor can only access those employees' data
- Can edit a singe user's info and settings
- Can edit a single user's punches
- Can set a limit for starting and ending times for user's workday (useful when you don't want a user to have sliding work hours)
- Can print barcode -cards for either a single user or for multiple users at once

### User levels
Since every user of the system has same type of user account, a user level -system is used to limit users permissions. Wiisari currently has levels from 0 to 3 for representing different types of users. See below for the permissions of each level, note that higher levels inherit the rights of lower levels.

**Level 0**
- Can punch themselves in and out (punch = start or stop timing working hours)
- Can access their own page by logging in
- Can see their own work hour -reports

**Level 1**
- Can see users of given groups and their work hour -reports
- Can print barcode -cards for these users

**Level 2**
- Can create users of level 0
- Can edit level 0 users of given groups, but can't increase their user level (edit = delete user or alter info and parameters)
- Can edit these users' work hours, including adding new punches

**Level 3 (Admin)**
- Access to everything and every user
- You need to use a user with this level to create supervisors (level 1 and 2) and assign them to groups

## Installation
### Requirements
- PHP 7 or newer
- MySQL 5.7 (8.0 not tested)
  - Should also work with MariaDB
  
Installation works as follows:
1. Clone this project and set your server (for example Apache) to point to the cloned directory
2. Create the MySQL database with the given sql-dump in the directory /sql
3. Assign a user to the database with given permissions: `SELECT, INSERT, UPDATE, DELETE`
4. Open the file config.inc.php and set the database -specific environment variables
5. Now you can open the application
6. For logging in, default username and password are both "admin"
7. If the app works, please generate new salt for password-encryption and set it in the config.inc.php (now you also need to generate new password for admin and update it directly into the database)

## Usage
General usage manuals can be found in /docs -directory (currently only in finnish)
### Configuration
Currently configuration is limited to the envirionment variables available in **config.inc.php**.

## Some screenshots
![Clocking station](/docs/readme-images/wiisari-screen1.jpg?raw=true)
![User's own view](/docs/readme-images/wiisari-screen2.jpg?raw=true)
![Login screen](/docs/readme-images/wiisari-screen3.jpg?raw=true)

## Includes code from these projects
PHP Timeclock
https://github.com/UnitedTechGroup/timeclock

Chart.js
https://github.com/chartjs/Chart.js

Tablesorter
https://github.com/mottie/tablesorter

Text Input Effects
https://github.com/codrops/TextInputEffects

PHP Barcode Generator
https://github.com/picqer/php-barcode-generator
