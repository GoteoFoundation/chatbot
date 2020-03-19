# Goteo Help Widget

This project allows the inclusion in an HTML page of a standalone help widget that allows visitors to navigate through a flow of questions and answers interactivelly, in a way and with a look and feel that resembles an instant messaging application. Questions flows are categorized in topics that are shown in different HTML pages.

[![Created by Goteo Foundation](resources/assets/backoffice/images/foundation-logo.png)](https://foundation.goteo.org) &nbsp; [![With the support of Platoniq Sistema Cultural](resources/assets/backoffice/images/platoniq-logo.png)](http://platoniq.net)

## Built with

The framework used is [Laravel](https://laravel.com/). Version is 6.15.0 (always updated information about the version is at `vendor/laravel/framework/src/Illuminate/Foundation/Application.php`). Documentation available at [https://laravel.com/docs/6.x](https://laravel.com/docs/6.x).

## Getting Started

These are the instructions for deploying the project.

### Quick start with Docker for Development

#### Initial setup

Skip this section if you've done this part already.
In a new installation run these commands (before `docker-compose up`):

**Note:** 
- some of the step may fail at the first time, just try again.
- You may have to use `sudo` in front of the `docker-compose` command.

```
docker-compose build
docker-compose run --rm composer install
docker-compose run --rm artisan migrate
docker-compose run --rm artisan db:seed
docker-compose run --rm npm install
docker-compose run --rm npm run dev
sudo chmod 777 -R storage/
sudo chmod 777 -R bootstrap/cache/
```

Run `composer install` everytime `composer.lock` changes (on repo updates).

Create the first admin user:

```
docker-compose run artisan register:admin
```

Ready to start the environment 👇

### Starting DEV environment

Just get the docker stack up by executing (use the flag `--build` to force a rebuild):

```
docker-compose up
```

Point your browser to http://localhost:8080

Checkout outgoing emails (maildev smtp catch-all server) at http://localhost:8025

### How to execute common commands in Docker:

**Composer:**

```
docker-compose run --rm composer update
```

**npm:**

```
docker-compose run --rm npm run dev
```

**artisan:**

```
docker-compose run --rm artisan migrate
docker-compose run --rm artisan db:seed
```

### Deployment

Clone or download the project files from the repository into the server. In the webserver configuration, the virtual host must point at the project's directory `/public` as the document root. Make sure that all files and directories have the correct ownership and permissions for the web server.

Once inside the project folder, install the dependencies:

```sh
$ composer install
```

Then copy the `.env.example` file as `.env` and run:

```sh
$ php artisan key:generate
```

This will set the secret key for the project encryption.

#### Configuration

Open the recently created file `.env` and edit it to set all the environment variables.

Project variables:

```
APP_NAME - The name of the app. It is shown in the backoffice.
APP_ENV - The environment of the deployment. 'production', if it is the case.
APP_DEBUG - Debug mode, false if that is the case.
APP_URL - The app URL.
MIN_ANSWERS_PER_QUESTION - Minimum number of answers allowed per question (2 by default).
```

Database variables:

```
DB_CONNECTION - The type of database, usually MySQL.
DB_HOST - The database host.
DB_PORT - The database port.
DB_DATABASE - The database name.
DB_USERNAME - The database user.
DB_PASSWORD - The database password.
```

Email variables (used to send transactional password recovery emails):

```
MAIL_DRIVER - The mail protocol, usually SMTP.
MAIL_HOST - Mail server host.
MAIL_PORT - Mail server port.
MAIL_USERNAME - Mail server username.
MAIL_PASSWORD - Mail server password.
MAIL_FROM_ADDRESS - Sender address.
MAIL_FROM_NAME - Sender name.
```

Now we have to create the database schema:

```sh
$ php artisan migrate
```

This will create all required database tables.

##### Server configuration for production

This application must run in an SSL-enabled server.

For this application to work, CORS must be enabled. This is how CORS can be enabled in two popular web servers:

* [Apache](https://enable-cors.org/server_apache.html)
* [Nginx](https://enable-cors.org/server_nginx.html)

#### Seeding

At this point the database is empty. Let's seed the database with initial content:

```sh
$ php artisan db:seed
```

Now the database has the information for initial languages (Spanish, Catalan and English) and their UI copies, but still no users.

For creating one admin user, there is an Artisan command available:

```sh
$ php artisan register:admin
```

Name, email and password will be asked in order to create the corresponding admin user to access the backoffice. Backoffice is reachable in the root URL of the domain that is pointed by the virtual host.

##### Additional seeding (for testing purposes)

The seeding behavior is different depending on the environment configured in the .env file. If it is set to `production`, only initial languages are created.

But, if run the same command with `APP_ENV` set to any another environment, the database gets seeded with fake automatically generated topics, questions and answers for testing purposes.

##### Database reset and filled (hint)

At any point, the database can be completely fully emptied and reconstructed by executing:

```sh
$ php artisan migrate:fresh --seed
```

**WARNING:** This will wipe all the data in the database.

#### Frontend dependencies and compilation

Both backoffice and public widget client styles and JavaScript codes need to be compiled using NPM.

To install NPM refer to [https://www.npmjs.com/get-npm](https://www.npmjs.com/get-npm).

To install all project dependences execute (from project's root directory):

```sh
$ npm install
```

Then, to compile all styles and code for production, run:

```sh
$ npm run prod
```

## Inclusion of the widget in an HTML page

Once the application is already deployed and all questions and answers of a topic are correctly introduced and translated using the backoffice, the public widget is ready to be includes in any site page. 

It is a JavaScript code that should be placed at the end of the HTML document, right before the closing `</body>` tag.

The first piece of code is intended to load the JavaScript code of the widget. There are two versions of the JS.

* Including jQuery library (if the site where the widget is placed does not load jQuery - jQuery is required by the widget):

```
<script src="http://goteo.local/widget/widget-jquery.js"></script>
```

* Without including jQuery (if the site already loads jQuery, to avoid loading it more than once):

```
<script src="http://goteo.local/widget/widget.js"></script>
```

Right after, an initialization piece of code needs to be placed:

```
<script>
    (window.goteoHelpWidget=window.goteoHelpWidget||{}).load("<domain without the ending slash>", "<language>", <topic>, <load Roboto>);
</script>
```

This is the explanation of each parameter of the `load()` function:

* `<domain without the ending slash>`: The domain were the project is loaded without the ending slash.
* `<language>`: ISO code of the language to display the contents. The language must exist in the backoffice.
* `<topic>`: ID of the topic to display questions and answers about. The ID of an existing topic can be obtained from the backoffice.
* `<load Roboto>`: boolean. When `true` the Google Fonts Roboto font face is loaded. Set to `false` if the Roboto font is already loaded in the page.

Example of the overall widget code without loading jQuery nor the Roboto font:

```
<script src="http://goteo.local/widget/widget.js"></script>
<script>
	(window.goteoHelpWidget=window.goteoHelpWidget||{}).load("https://widget.goteo.org", "es", 1, false);
</script>
```


## License

The code licensed here under the GNU Affero General Public License, version 3 AGPL-3.0. 
