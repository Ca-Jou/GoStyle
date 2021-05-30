# GoStyle - Backend
This repo contains an API written in PHP/Symfony using API Platform. It is meant to be connected with its Frontend part located here:
https://github.com/Johanna1506/GoStyle_Front

## Database setup
### Prod database
1. Create a mysql database named GoStyle
2. Update .env with the proper credentials to access this database
3. Create the proper database schema:
   
   `symfony console doctrine:migrations:migrate`
4. If needed during development, load dummy fixtures into the database:
   
   `symfony console doctrine:fixtures:load --group=app`

### Test database
1. Create a mysql database named GoStyle_test
2. Update .env.test with the proper credentials to access this database
3. Create the proper database schema:
   
   `symfony console --env=test doctrine:migrations:migrate`
4. Load test fixtures into the test database:
   
   `symfony console --env=test doctrine:fixtures:load --group=test`

## Run UnitTests
By default, all tests will run in test environment, thus using the test database.

To run all unit tests:
`php vendor/bin/phpunit`

## Consume API
Once setup, the environment is ready to run! The following routes can be used to consume the API:

| Route                               | Method | Headers                                                               | Content                                                   | Response                                                                                                |
| ----------------------------------- | ------ | --------------------------------------------------------------------- | --------------------------------------------------------- | ------------------------------------------------------------------------------------------------------- |
| `/login`                            | POST   | { "Content-Type": "application/json" }                                | { "username": "foo", "password": "bar" }                  | A JSON object containing the user's API token under the key "apiToken".                                 |
| `/api/coupons/{code}`               | GET    | { "Content-Type": "application/json", "X-AUTH-TOKEN": "foobartoken" } | -                                                         | A JSON object containing all details associated with the requested coupon.                              |
| `/api/users/{apiToken}/get_coupons` | GET    | { "Content-Type": "application/json", "X-AUTH-TOKEN": "foobartoken" } | -                                                         | A JSON object containing the list of coupons scanned by requested user under the key "coupons".         |
| `/api/users/{apiToken}/add_coupon`  | PUT    | { "Content-Type": "application/json", "X-AUTH-TOKEN": "foobartoken" } | { "coupons": [ "/api/coupons/FOO", "/api/coupons/BAR" ] } | A JSON object containing the updated list of coupons scanned by requested user under the key "coupons". |

These routes are documented and can be tested with the API Platform standard on route `/api/docs`.
