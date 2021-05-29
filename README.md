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
