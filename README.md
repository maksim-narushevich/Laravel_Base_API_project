
**HOT TO INSTALL APP**
--
     
* *Start app and build required Docker containers:*

        docker-compose up -d
      
* *Install all composer dependencies:*

        docker exec -it laravel_api composer install
        
* *Copy ``.env`` environment config file and set all required settings in it:*

        docker exec -it laravel_api cp .env.dist .env

* *Generate Laravel application key:*

        docker exec -it laravel_api php artisan key:generate
        
* *Run all required migrations:*

        docker exec -it laravel_api php artisan migrate
  
* *Generate the encryption keys needed to generate secure access Passport JWT tokens:*
    
        docker exec -it laravel_api  php artisan passport:install

App is available on ``8187`` port
--
    http://127.0.0.1:8187

**Mail Server**
--
*Mail development server is available on ``8127`` port*
        
    [http://127.0.0.1:8127](http://127.0.0.1:8127)
    
**REST API SWAGGER DOCUMENTATION**
--
*API documentaion is available to see by navigating to following url*
        
    [http://127.0.0.1:8127/api/doc](http://127.0.0.1:8127/api/doc)
    
# BEHAT REST API TESTS
--
*In order to run Behat API tests & setup test environment run following commands*
``(previous environment will be restored automatically after Behat tests passed)``
```
php artisan create:database laravel_api_db_test
vendor/bin/behat --tags @api
```
