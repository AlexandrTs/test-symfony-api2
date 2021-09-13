## Symfony API

### Installation & setup

#### Clone repository
```
git clone ... symfony-api
```
#### Move to repository folder
```
cd symfony-api
```
#### Install composer dependencies
```
make composer COMMAND="install"
```
#### Configure database connection 

You need to fill correct dsn in env file: .env

```
DATABASE_URL=...
```

#### Create database schema
```
make php COMMAND="./bin/console doctrine:schema:create"
```
### Environments

I use only `dev` environment to simplify check this task.
In real life we need to add test env too.

### API 

#### Run import customers command
```
make php COMMAND="./bin/console app:import-customers"
```

100 customers will be imported from RandomUser API.
You can run this command as many times as you need. 
Sometimes API return duplicates.

#### In another terminal run php web server
```
make server
```
Web server will start at localhost:8000
And you can check API in your browser: http://localhost:8000/api

### Tests

#### Start servers

For testing purposes we need one more web server.

Phiremock web server (to simulate RandomUser API)
```
make server-phiremock
```

Will start at localhost:8002

#### Run tests

```
make php COMMAND="./vendor/bin/codecept run --steps"
```

Run only unit tests

```
make php COMMAND="./vendor/bin/codecept run unit"
```





