#MyDiary

####Description
MyDiary is a simple symfony MVC application who allows to add a diary entry once a day.
Project is covered with PHPUnit tests.

####Requirements
* php 7.2+
* composer

Getting Started

1. Pull project from github repository:
 git clone https://github.com/Syemon/MyDiary.git
2. Go to the project root:
 cd MyDiary
3. Download and install packages: composer install
4. Create database: php bin/console doctrine:database:create
5. Update database schema: php bin/console doctrine:schema:update --force
6. Load fixtures: php bin/console doctrine:fixtures:load
7. Run an application: php bin/console server:run

####Running the tests

To run test type:  ./vendor/bin/phpunit tests/AppBundle/Entity/DiaryTest
