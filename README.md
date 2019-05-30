
- [How to install](https://github.com/Maksim1990/Laravel_Postgres_Product_App_and_REST_API/blob/master/public/docs/installation.md)
- [About REST API](https://github.com/Maksim1990/Laravel_Postgres_Product_App_and_REST_API/blob/master/public/docs/restapi.md)
- [About GUI application interface](https://github.com/Maksim1990/Laravel_Postgres_Product_App_and_REST_API/blob/master/public/docs/gui.md)
- [Requirements that app cover](https://github.com/Maksim1990/Laravel_Postgres_Product_App_and_REST_API/blob/master/public/docs/requirements.md)

**Mail Server**
--
*Mail development server is available on ``8127`` port*
        
    http://127.0.0.1:8127
    
**REST API SWAGGER DOCUMENTATION**
--
*API documentaion is available to see by navigating to the following url*
        
    http://127.0.0.1:8127/api/doc
    
# BEHAT REST API TESTS
--
*In order to run Behat API tests & setup test environment run following commands*
``(previous environment will be restored automatically after Behat tests passed)``
```
cp behat.dist.local behat.yml
php artisan create:database laravel_api_db_test
vendor/bin/behat --tags @api
```
