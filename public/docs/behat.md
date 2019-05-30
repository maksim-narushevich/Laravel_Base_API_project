# BEHAT REST API TESTS
--
*In order to run Behat API tests & setup test environment run following commands*
``(previous environment will be restored automatically after Behat tests passed)``
```
cp behat.dist.local behat.yml
php artisan create:database laravel_api_db_test
vendor/bin/behat --tags @api
```
