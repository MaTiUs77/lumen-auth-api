#!/bin/bash
docker exec -it siep-auth-php chmod 777 ./storage -R
docker exec -it siep-auth-php composer install

