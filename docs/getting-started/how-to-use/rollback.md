# Rolling Back Actions

To roll back the latest action operation, you may use the `rollback` command. This command rolls back the last "batch" of actions, which may include multiple action files:

```
php artisan migrate:actions:rollback
```

You may roll back a limited number of actions by providing the `step` option to the rollback command. For example, the following command will roll back the last five actions:

```
php artisan migrate:actions:rollback --step=5
```

The `migrate:actions:reset` command will roll back all of your application's migrations:

```
php artisan migrate:actions:reset
```

## Roll Back & Action Using A Single Command

The `migrate:actions:refresh` command will roll back all of your migrations and then execute the `migrate:actions` command. This command effectively re-creates your entire
database:

```
php artisan migrate:actions:refresh
```

You may roll back & re-migrate a limited number of migrations by providing the `step` option to the `refresh` command. For example, the following command will roll back &
re-migrate the last five migrations:

```
php artisan migrate:actions:refresh --step=5
```

## Drop All Actions & Rerun Actions

The `migrate:actions:fresh` command will drop all actions records from the actions table and then execute the migrate command:

```
php artisan migrate:actions:fresh
```
