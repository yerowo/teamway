# Worker shift projects

Build a REST application from scratch that could serve as a work planning service.

# Business requirements:

- A worker has shifts
- A shift is 8 hours long
- A worker never has two shifts on the same day
- It is a 24 hour timetable 0-8, 8-16, 16-24

# This setup has included:

- PHP
- MySQL
- Nginx
- PHPMyAdmin

# Start set up clone or download from github

https://github.com/yerowo/teamway.git

## To start the services:

```bash
docker-compose up -d
```

note the cmd line should be run on the root directory where the docker-compose.yml file is

## To run composer and install dependency :

- navigate to /public dirctory

```bash
 composer update
```

# API Documentation

https://documenter.getpostman.com/view/3292585/2s93XtzjgY

# Run test

```bash
docker-compose run app vendor/bin/phpunit src/test/WorkerShiftTest.php
```
