
Install the project
----------------------------------
You have to install docker and Symfony Server previously

Install symfony server documentation

```console  
https://symfony.com/doc/current/setup/symfony_server.html
```

Install docker documentation

```console  
https://docs.docker.com/get-docker/
```

Run the project
----------------------------------

Start symfony server
```console 
$ symfony server:start
```

Start mysql docker
```console 
$ docker-compose up -d
```

Run the project first time
---------------------------------- 

``` 
$ php bin/console sulu:build dev
```