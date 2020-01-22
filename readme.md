# SNAP CSG e-Workflow Installation Guide
## [v1.1](https://gitlab.praxxys.ph/systems/csg/milestones/6)

**IMPORTANT: Back up before proceeding!**

**Migrate tables and populate new tables**
```bash
php artisan migrate

# Add new roles for Location and Room Permission
php artisan meetingroom:roles
php artisan location:roles

php artisan db:seed --class=LocationsTableSeeder
php artisan db:seed --class=RoomTableSeeder

# Refresh Search Index
php artisan search:refresh
```

**Fixing node sass error**
```bash
npm uninstall node-sass
npm install node-sass --save-dev
npm run prod
```

**Migrate v1.0 Meeting Room Reservation Form Template to v1.1 Meet Room Reservation Model** 
```bash
# Generate an excel file with the meeting room reservation form and answers
# IMPORTANT: Generated excel contain invalid data check and fix invalid date and time formats
# Date Format: YYYY-MM-DD (2018-09-30) 
# Time Format: HH:MM:SS 24H (17:00:00) 
export:mrreservation

# Once excel has been corrected import the generated excel file by declaring the path
import:mrreservation
```

## [v1.0](https://gitlab.praxxys.ph/systems/csg/milestones/5)


## ``Shortcut (Automatically install Let's Encrypt, Nginx & Composer``

- [ ] Install GIT

~~~
sudo apt-get install -y git
~~~

- [ ] Add in the SSH ID of the current user to the praxxys-sysadmin/nginx git project
- [ ] GIT clone the `install` script on the praxxys-sysadmin/nginx project

~~~
git clone ssh://git@gitlab.praxxys.ph:52222/praxxys-sysadmin/nginx.git
~~~

- [ ] Run the `install` script

~~~
. nginx/install
~~~




## ``Install Let's Encrypt``

- [ ] Install Let's Encrypt certbot

~~~
sudo apt-get update
sudo apt-get install -y certbot
~~~




## ``Install Nginx``

- [ ] Install Nginx

~~~
sudo apt-get install -y nginx
# Remove default config
sudo rm /etc/nginx/sites-enabled/default

# Create strong 2048-bit Diffie Helman group
sudo openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048
sudo cp <PASSWORD>/conf/* /etc/nginx/snippets/
~~~




## ``Install PHP extensions``

- [ ] Install needed php extensions

~~~
sudo apt-get install -y php7.0-fpm php7.0-mysql php7.0-curl php7.0-gd php7.0-dom php7.0-mbstring unzip xz-utils
~~~




## ``Install Composer``

- [ ] Install composer

~~~
sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
sudo php -r "if (hash_file('SHA384', 'composer-setup.php') === '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php composer-setup.php
sudo php -r "unlink('composer-setup.php');"

sudo mv composer.phar /usr/bin/composer
~~~




## ``???``

- [ ] ???

~~~
echo 'PATH="$HOME/nginx:$PATH"' | sudo tee -a /root/.profile 
~~~




## ``Install MariaDB``

- [ ] Generate database defaults-file

~~~
# Generate password for mariadb
openssl rand -base64 48
# Create defaults file
touch ~/.mysqlpword
chmod 600 ~/.mysqlpword
~~~
~~~
[client]
user=root
password=<PASSWORD>
~~~

- [ ] Install MariaDB Server

~~~
sudo apt-get install -y mariadb-server
~~~

- [ ] Run `mysql_secure_installation` and set the password




## ``Create project database and users``

- [ ] Generate database user password

~~~
# Important! Remember this for later
openssl rand -base64 48
~~~

- [ ] Create the project database & user and then set its privileges

~~~mysql
CREATE DATABASE <PROJECT-NAME>_db;
CREATE USER '<PROJECT-NAME>'@'localhost' IDENTIFIED BY '<PASSWORD>';
GRANT SELECT,INSERT,UPDATE,DELETE ON <PROJECT-NAME>_db.* TO '<PROJECT-NAME>'@'localhost';
~~~

- [ ] Generate database master user password

~~~
# Important! Remember this for later
openssl rand -base64 48
~~~

- [ ] Create master user and then set its privileges

~~~mysql
CREATE USER 'dbmaster'@'localhost' IDENTIFIED BY '<PASSWORD>';
GRANT CREATE USER,ALTER,INDEX,CREATE,DROP,GRANT OPTION,RELOAD,SELECT,INSERT,UPDATE,DELETE,LOCK TABLES ON *.* TO 'dbmaster'@'localhost' IDENTIFIED BY '<PASSWORD>';
~~~

- [ ] Save the privilege changes

~~~mysql
FLUSH PRIVILEGES;
~~~




## ``GIT clone the project using the make-server script``

- [ ] Run the make-server script inside the praxxys-sysadmin/nginx project folder

~~~
make-server

# Don't include the database settings (-d)
 ____   ____      _    __   __ __   ____   __ ____
|  _ \ |  _ \    / \   \ \ / / \ \ / /\ \ / // ___|
| |_) || |_) |  / _ \   \ / /   \ \ /  \ V / \___ \
|  __/ |  _ <  / ___ \  / \ \   / / \   | |   ___) |
|_|    |_| \_\/_/   \_\/_/ \_\ /_/ \_\  |_|  |____/
            NGINX Setup Utility v1.0

You must pass a domain, user, and type.
Usage: nginx/make-server -d domain.praxxys.ph -t {static|silverstripe|laravel} -u username [-w add www subdomain] [-s add-ssl] [-g ssh://address to pull from gitlab] [-h mysql hostname] [-f /path/to/mysql/sql/file] [-a xz-assets/storage]
~~~

- [ ] Set the .env variable

~~~
DB_CONNECTION=mysql
DB_DATABASE=<DATABASE>
DB_USERNAME=<DATABASE-USER>
DB_PASSWORD=<DATABASE-USER-PASS>

#DB_USERNAME=<DATABASE-MASTERUSER>
#DB_PASSWORD=<DATABASE-MASTERUSER-PASS>

SCOUT_QUEUE=true
SCOUT_DRIVER=tntsearch

QUEUE_DRIVER=database

TNTSEARCH_FUZZINESS=auto

GOOGLE_CLIENT_ID=579331943292-mqo8vi3inoo9eoim4cadj2quaoq7e9nn.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=gntEbCP_-uGJMkK3nsYTDDE-
GOOGLE_CALLBACK_URL=http://csg.snaboitiz.com/auth/google/callback
~~~

- [ ] Refresh environment settings

~~~
php artisan config:clear
~~~




## ``Perform database migration and seeders``

- [ ] Generate key

~~~
php artisan key:generate
~~~

- [ ] Perform migration and seeding

~~~
php artisan migrate:refresh --seed
~~~

- [ ] Import manually the employee data using the batch uploader
- [ ] Seed the default form template and settings

~~~
php artisan db:seed --class="FormsSeeder"
php artisan db:seed --class="SettingsSeeder"

# Optional: If you want to create the UAT users
php aritsan db:seed --class="UATUsersSeeder"
~~~




## ``Set-up Supervisor``

- [ ] Install supervisor

~~~
sudo apt-get install supervisor
~~~

- [ ] Create the configuration file for CSG

~~~
vim /etc/supervisor/conf.d/csg-worker.conf

# csg-worker.conf
[program:csg-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/csg.snaboitiz.com/artisan queue:listen --sleep=3 --tries=3
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/www/csg.snaboitiz.com/storage/logs/queue-worker.log

[supervisord]
nodaemon=false

[inet_http_server]
port=127.0.0.1:9001
~~~

- [ ] Start the supervisor

~~~
supervisorctl reread
supervisorctl update
supervisorctl start csg-worker:*
~~~


## ``Post-Launch Steps``

- [ ] Set bi-daily database backup mechanism keep for 14 instances
- [ ] Set daily backup of storage folder keep for 7 instances
- [ ] Setup monitoring of:
	- [ ] RAM
	- [ ] Disk Space
	- [ ] CPU Usage
	- [ ] Services Monitor
		- [ ] Web Service
		- [ ] Email Service
		- [ ] Push Service