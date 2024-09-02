# ToDoAndCo

OpenClassrooms project, a todo list project to optimize and update

[![SymfonyInsight](https://insight.symfony.com/projects/4a5eef08-5438-4b84-b90d-aea8fffe60cb/big.svg)](https://insight.symfony.com/projects/4a5eef08-5438-4b84-b90d-aea8fffe60cb)

## Requirements

| Requirements |
| ------------ |
| PHP          |
| Composer     |
| Symfony cli  |
| MySql        |

## Set Up

To setup the environnement run :

```shell
$ git clone https://github.com/Davidouu/bilemo.git
```

```shell
$ cd bilemo
```

```shell
$ composer install
```

<hr>

### Config .env

In the project folder run this command :

```shell
$ cp .env .env.local
```

And

```shell
$ cp .env .env.test.local
```

Then fill the this variable for the local env and the test env :

```
###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"
###< doctrine/doctrine-bundle ###
```

<hr>

### DB setup

To create the database and all the tables execute this command :

```
$ php bin/console doctrine:database:create
```

```
$ php bin/console doctrine:schema:update --force
```

To load dataFixtures in the database run :

```
$ php bin/console doctrine:fixtures:load
```

And for test :

```
$ php bin/console doctrine:database:create --env=test
```

```
$ php bin/console doctrine:schema:update --env=test --force
```

To load dataFixtures in the database run :

```
$ php bin/console doctrine:fixtures:load --env=test
```
