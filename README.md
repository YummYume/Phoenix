# ![phoenix icon](https://github.com/YummYume/Phoenix/blob/master/public/icons/favicon.ico?raw=true) Phoenix
The Phoenix project. Phoenix helps you manage your projects easily.

### How to install
- Clone the project using `git clone`
- Run `composer install`
- Run `yarn install`
- Run `yarn dev` or `yarn watch`
- Copy the `.env` file and rename it `.env.local` then change any configuration needed (such as the db connection)
- Run `php bin/console d:d:c` to create the database
- Run `php bin/console d:s:u --force` to create the database schema
- Run `php bin/console d:m:m` to load the migrations
- Run `php bin/console d:f:l` to load the fixtures
- Start the server with `symfony server:start`

### Notes
- I am too lazy to create a Makefile 🥱
- I am also too lazy to make a full English translation 🥱🥱
- You can drag the milestones of a project to change their position (using [sortable.js](https://github.com/SortableJS/Sortable))
- The forecast of a project is all done by an AI (using [brain.js](https://github.com/BrainJS/brain.js))
- The default Super Admin account after loading the fixtures is `root@phoenix.com` with `root` as password
