php artisan operations:rollback
# operation                 batch
# 2022_10_12_021837_some    1
# 2022_10_12_021838_some    2
# 2022_10_12_021839_some    2
# 2022_10_12_021840_some    3  // will be canceled
# 2022_10_12_021841_some    3  // will be canceled

php artisan operations:rollback --step=1
# operation                 batch
# 2022_10_12_021837_some    1
# 2022_10_12_021838_some    2
# 2022_10_12_021839_some    2
# 2022_10_12_021840_some    3  // will be canceled
# 2022_10_12_021841_some    3  // will be canceled

php artisan operations:rollback --step=2
# operation                 batch
# 2022_10_12_021837_some    1
# 2022_10_12_021838_some    2  // will be canceled
# 2022_10_12_021839_some    2  // will be canceled
# 2022_10_12_021840_some    3  // will be canceled
# 2022_10_12_021841_some    3  // will be canceled
