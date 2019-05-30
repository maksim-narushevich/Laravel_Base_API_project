# Laravel_Postgres_Product_App_and_REST_API
Laravel PostgresSQL Product Applications with REST API integration


- [How to install](https://github.com/Maksim1990/Laravel_Base_API_project/blob/feature/Update_Readme_docs/public/docs/installation.md)
- [How to run BEHAT REST API tests](https://github.com/Maksim1990/Laravel_Postgres_Product_App_and_REST_API/blob/master/public/docs/restapi.md)
- [About Mail server](https://github.com/Maksim1990/Laravel_Postgres_Product_App_and_REST_API/blob/master/public/docs/gui.md)
- [About Swagger docementation](https://github.com/Maksim1990/Laravel_Postgres_Product_App_and_REST_API/blob/master/public/docs/requirements.md)

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
