# Commands

Here are some of the commands that you'll need:
 - install dependencies: `docker run -it --rm -v $PWD:/app -w /app chialab/php-dev:8.2 composer install`
 - run tests with phpunit: `docker run -it --rm -v $PWD:/app -w /app chialab/php-dev:8.2 composer test`
 - reformat using php-cs-fixer: `docker run -it --rm -v $PWD:/app -w /app chialab/php-dev:8.2 composer cs-fix`
 - analyse with phpstan: `docker run -it --rm -v $PWD:/app -w /app chialab/php-dev:8.2 composer phpstan`
