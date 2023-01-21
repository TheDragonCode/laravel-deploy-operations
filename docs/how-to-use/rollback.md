# Rolling Back Actions

To roll back the latest action operation, you may use the `rollback` command. This command rolls back the last "batch" of actions, which may include multiple action files:

```
php artisan actions:rollback
```

You may roll back a limited number of actions by providing the `step` option to the rollback command. For example, the following command will roll back the last five actions:

```
php artisan actions:rollback --step=5
```

The `actions:reset` command will roll back all of your application's actions:

```
php artisan actions:reset
```

For example:

```bash
php artisan actions:rollback
# action                    batch
# 2022_10_12_021837_some    1
# 2022_10_12_021838_some    2
# 2022_10_12_021839_some    2
# 2022_10_12_021840_some    3  // will be canceled
# 2022_10_12_021841_some    3  // will be canceled

php artisan actions:rollback --step=1
# action                    batch
# 2022_10_12_021837_some    1
# 2022_10_12_021838_some    2
# 2022_10_12_021839_some    2
# 2022_10_12_021840_some    3  // will be canceled
# 2022_10_12_021841_some    3  // will be canceled

php artisan actions:rollback --step=2
# action                    batch
# 2022_10_12_021837_some    1
# 2022_10_12_021838_some    2  // will be canceled
# 2022_10_12_021839_some    2  // will be canceled
# 2022_10_12_021840_some    3  // will be canceled
# 2022_10_12_021841_some    3  // will be canceled
```

## Roll Back & Action Using A Single Command

The `actions:refresh` command will roll back all of your actions and then execute the `actions` command. This command effectively re-creates your entire
database:

```
php artisan actions:refresh
```

You may roll back & re-migrate a limited number of actions by providing the `step` option to the `refresh` command. For example, the following command will roll back &
re-migrate the last five actions:

```
php artisan actions:refresh --step=5
```

## Drop All Actions & Rerun Actions

The `actions:fresh` command will drop all actions records from the actions table and then execute the migrate command:

```
php artisan actions:fresh
```
