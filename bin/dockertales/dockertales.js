#!/usr/bin/env node
const { exec } = require("child_process");
var shell = require("shelljs");
console.log(`☆*:.｡.o(≧▽≦)o.｡.:*☆ INIT`);
console.log(`- - - - - - - - - - - - - - - `);
console.log(`- Creating network 。.:☆*:･'(*⌒―⌒*)))`);
exec("docker network create zemoga_net", (error, stdout, stderr) => {
    console.log(`- Running PHP Container <(￣︶￣)>`);
    exec("docker-compose -f ./docker/php/docker-compose.yaml up -d --build", (error, stdout, stderr) => {
        if (error) {
            console.log(`error: ${error.message}`);
            return;
        }
        console.log(`- Running Mysql Container (￣ω￣)`);
        exec("docker-compose -f ./docker/mysql/docker-compose.yaml up -d --build", (error, stdout, stderr) => {
            if (error) {
                console.log(`error: ${error.message}`);
                return;
            }
            console.log(`- Running Web Container <(￣︶￣)>`);
            exec("docker-compose -f ./docker/web/docker-compose.yaml up -d --build", (error, stdout, stderr) => {
                if (error) {
                    console.log(`error: ${error.message}`);
                    return;
                }
                console.log(`-- Building composer libs (this take a little while you can take a coffe (o˘◡˘o) ) (－ω－) zzZ`);
                exec("docker-compose -f ./docker/php/docker-compose.yaml run zemoga-php php composer.phar install", (error, stdout, stderr) => {
                    if (error) {
                        console.log(`error: ${error.message}`);
                        return;
                    }
                    console.log(`-- Building Database (￣ω￣)`);
                    exec("docker-compose -f ./docker/php/docker-compose.yaml run zemoga-php php bin/console doctrine:schema:update --force", (error, stdout, stderr) => {
                        if (error) {
                            console.log(`error: ${error.message}`);
                            return;
                        }
                        console.log(`-- Seeding Database <(￣︶￣)>`);
                        exec("docker-compose -f ./docker/php/docker-compose.yaml run zemoga-php php bin/console zem:seed", (error, stdout, stderr) => {
                            if (error) {
                                console.log(`error: ${error.message}`);
                                return;
                            }
                            console.log(`! ┌( ಠ_ಠ)┘ ! Nice work now you can see the project step 1 at this address: http://localhost:5016 ! Enjoy !`);
                            console.log(`! ┌( ಠ_ಠ)┘ ! Nice work now you can see the project step 2 at this address: http://localhost:5016/api ! Enjoy !`);
                        });
                    });
                });

            });
        });
    });
});
