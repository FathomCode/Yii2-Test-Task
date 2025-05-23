# Yii2-Test-Task

Необходимо сделать страницу с отчётом по продажам, который нужно создать в этом проекте на основе исходников. 

Отчёт должен выводить суммарные продажи в рублях по каждому клиенту и по каждому товару.
При этом должны работать фильтры - по диапазону дат и по районам.

Пример оформления отчёта на втором листе этой таблицы. Естественно, на сайте этот отчёт будет выглядеть немного иначе, мне просто удобнее делать прототипы в таблицах, нежели в графических редакторах.

От вас жду архив с доработанным проектом, в котором будет активная страница с работающим отчётом по этому шаблону.

![alt Пример оформления отчёта](https://github.com/FathomCode/Yii2-Test-Task/blob/main/Пример%20оформления%20отчёта.jpg?raw=true)

####Installation process

# Install composer
```
cd /tmp
curl -sS https://getcomposer.org/installer -o composer-setup.php
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

# Install p-seed
```
cd /home/seed/
mkdir p-seed
chown seed:seed ./p-seed
sudo -u seed git clone git@bitbucket.org:sitd777/p-seed.git ./p-seed
cd ./p-seed
sudo -u seed composer update
```