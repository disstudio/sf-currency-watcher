# sf-currency-watcher
Symfony console command that pulls currency exchange rates from different providers and watches if it changes above given threshold.

## Installation

Pull repository in local folder

Use **docker-compose** to build and run container

```
docker-compose up
```

Create file `.env.local` with your local parameters. For example:

```
NOTIFICATION_EMAIL=disstudio1990@gmail.com

MAILER_DSN=native://default
MAILER_FROM=mail@example.com
```

More detailed info can be found at `.env` file.

## Usage

From outside the container:

`docker-compose exec sfcw_php php bin/console app:currency-rate:watch USD`

That command can be used, for example, in cron.

## Testing

Run into container shell

`php bin/phpunit`
