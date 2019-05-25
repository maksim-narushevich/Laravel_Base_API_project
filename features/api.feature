Feature: REST API Tests
  Test Laravel API
  As a user
  I want to have a test API scenario

  @api_purge
  @api
  Scenario: Prepare initial DB condition for running API test scenarios
    Given setup environment from ".env.behat" file with "test" env
    Given create test database "laravel_api_db_test" if not exist
    Given purge DB
    Given run database migrations
    Given generate secure access Passport JWT tokens

  @api_register
  @api
  Scenario: Register new User
    Given the "Content-Type" request header is "application/json"
    Given the request body is:
    """
    {
      "email": "test@gmail.com",
      "password": "testqwerty9100Y",
      "name": "test",
      "c_password": "testqwerty9100Y"
    }
    """
    When I request "/api/v1/register" using HTTP POST
    Then the response code is 200


  @api_login
  @api
  Scenario: Login as specific user,getting JWT token and go to 'Users' page
    Given the "Content-Type" request header is "application/json"
    Given the request body is:
    """
    {
        "email": "test@gmail.com",
        "password": "testqwerty9100Y"
    }
    """
    When I request "/api/v1/login" using HTTP POST
    Then the response code is 200
    Then save response token
    Then the response body matches:
    """
    /token/
    """
    # Scenario to fetch list of users
    Given the "Content-Type" request header is "application/json"
    Then set authorization token
    When I request "/api/v1/auth-user" using HTTP GET
    Then the response code is 200
    Then the response body matches:
    """
    /success/
    """

  @api_delete
  @api
  Scenario: Delete specific user,getting JWT token and go to 'Users' page
    Given the "Content-Type" request header is "application/json"
    Given the request body is:
    """
    {
        "email": "test@gmail.com",
        "password": "testqwerty9100Y"
    }
    """
    When I request "/api/v1/login" using HTTP POST
    Then the response code is 200
    Then save response token
    Then the response body matches:
    """
    /token/
    """
    # Scenario to delete user
    Given the "Content-Type" request header is "application/json"
    Then set authorization token
    When I delete user with email "test@gmail.com" with request to "/api/v1/auth-user/delete" using HTTP DELETE
    Then the response code is 200

  @api_restore
  @api
  Scenario: Restore application environment
    Given setup environment from ".env.dist" file with "restore_behat" env
    Given delete temporary ".env.temp" file if exist