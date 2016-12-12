# TWS
Throttling API

To configure project you need to install [docker](https://docs.docker.com/engine/installation) and [docker-compose](https://docs.docker.com/compose/install)
###Project configuration
1. `git clone https://github.com/marikn/tws.git`
2. `cd tws/docker`
3. `docker-compose up -d`
4. `docker-compose exec tws-php-fpm bash`
5. `composer update`

Service will be available on <http://localhost:8080/>

###Examples
<http://localhost:8080/obtain_execution_lock/A>

<http://localhost:8080/release_execution_lock/A>

###Test execution
Be aware you need to perform tests on host machine, not in docker container.
