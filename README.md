# Stock Tracker

## Run database migration
```shell
docker exec -it slim php /stocks/bin/console.php db:migrate
```

## Run listener for RabbitMQ messages
```shell
docker exec -it slim php /stocks/bin/listener.php
```
