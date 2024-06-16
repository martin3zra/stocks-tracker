## Stock Tracker

REST Api application that a user can use to track the value of stocks in stock market. This project is based on [Slim Framework v4](https://www.slimframework.com/docs/v4/).

## Features

Authentication/Authorizarion process that allow the users to register an account and later use that account to interact with the REST Api application by issue a token using the user credentials.

Once logged in the user can perform a request to the `/api/stock?q={stock_code}` to get the current value of the company/market value that was provided to the endpoint. The response that would be delivered to the user will contain all the information of the stock such as `name`, `symbol`, `open`, `high`, `low`, `close` and `date`. Also a email with the same iunformation would be send to the user email account.

The user can see the history of all the searches hi has performed in the past by hitting the `/api/history` endpoint.

## Tech

Stock Tracker uses a number of open source projects to work properly:

- [Slim Framework](https://www.slimframework.com/docs/v4/) - Slim is a PHP micro framework that helps you quickly write simple yet powerful web applications and APIs.
- [Docker](https://www.docker.com/) - Docker helps developers bring their ideas to life by conquering the complexity of app development.
- [Composer](https://getcomposer.org) - A Dependency Manager for PHP.
- [Laravel Database](https://github.com/illuminate/database) - The Illuminate Database package.
- [JSON Schema validator](https://github.com/jsonrainbow/json-schema) - A library to validate a json schema.
- [Firebase JWT](https://github.com/firebase/php-jwt) - A simple library to encode and decode JSON Web Tokens (JWT) in PHP. Should conform to the current spec.
- [Guzzle](https://github.com/guzzle/guzzle) - Guzzle is a PHP HTTP client library.
- [Symfony DotEnv](https://github.com/symfony/dotenv) -Registers environment variables from a .env file.
- [Symfony Mailer](https://github.com/symfony/mailer) - Helps sending emails
- [Symfony Mime](https://github.com/symfony/mime) - Allows manipulating MIME messages
- [RabbitMQ](https://www.rabbitmq.com) - RabbitMQ is a reliable and mature messaging and streaming broker, which is easy to deploy on cloud environments, on-premises, and on your local machine. It is currently used by millions worldwide.

And of course Stock Tracker itself is open source with a [martin3zra/stocks-tracker](https://github.com/martin3zra/stocks-tracker) on GitHub.

## Requirements:
In order to use and test this REST Api you'll need to have previously set up in your machine a [Docker](https://www.docker.com/) installation.

## Installation

Clone the repository or download the [zip file](https://github.com/martin3zra/stocks-tracker/archive/refs/heads/main.zip).
```sh
git clone https://github.com/martin3zra/stocks-tracker
```

CD into the project directory
```sh
cd stocks-tracker
```

Build docker container by using the `docker-compose.yml` file.
```sh
docker compose build && docker compose up -d
```

Install the dependencies and devDependencies and start the server.

```sh
docker exec -it slim composer install
```

Let's create the environment file
```sh
cp .env.example .env
```
 
Now make sure to add the `APP_KEY`, this is a string value use for the JWT Token signature, you can use any random string here.
```sh
APP_KEY=********* # Any random string here.
```

Next, change the credentials for mailtrap `MAILER_DSN` once the .env is created.
```sh
MAILER_DSN=smtp://******....***:******....***@sandbox.smtp.mailtrap.io:2525?encryption=ssl&auth_mode=login
```


If you go to your broswer and type [localhost:8890](http://localhost:8890) you must see a welcome message. and if you go to postman and hit [localhost:8890/api](http://localhost:8890/api) you should see the same message in `json` format.

Now let's create our database tables by running the migration command.
```sh
docker exec -it slim php bin/console.php db:migrate
```

And finally to be to listen/consume the messages broadcastest by RabbyMQ we need to start another console command by running:
```sh
docker exec -it slim php bin/listener.php
```
now you should see a Started to listen output in your console. And with that you're all set to request stock quotes.

To help you out, we've provided a [Postman collection](https://github.com/martin3zra/stocks-tracker/blob/main/Stock%20Tracker.postman_collection.json) to test the app that contains the endpoins and payloads you need to test all our features.

## License

MIT

**Free Software, Hell Yeah!**
