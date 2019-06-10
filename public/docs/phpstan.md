# PHPSTAN CODE ANALYSIS
--

*Before run PHPStan check (or update if necessary) configuration file*
```
./phpstan.neon
```

*In order to run PHPStan inside docker container*
```
 docker exec -it laravel_api ./vendor/bin/phpstan analyse 
```

*In order to run PHPStan separate Docker container*
```
 docker run --rm -v $(pwd):/app phpstan/phpstan analyse
```
