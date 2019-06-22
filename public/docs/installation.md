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

* *Change permission for 'storage' folder:*
    
        docker exec -it laravel_api  chmod +x ./services/docker/set_storage_read_write_permissions.sh
        docker exec -it laravel_api  ./services/docker/set_storage_read_write_permissions.sh

App is available on ``8187`` port
--
    http://127.0.0.1:8187
    
## In order to populate Database with fake data just run following command:
```
docker exec -it laravel_api  php artisan db:seed
```
