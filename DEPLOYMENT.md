# Документация по развёртыванию на VPS (Ubuntu 22.04)

Этот документ описывает процесс развёртывания социальной сети на VPS под управлением Ubuntu 22.04 с использованием стека LAMP (Linux, Apache, MySQL, PHP).

## 1. Системные требования

- **ОС:** Ubuntu 22.04 LTS
- **Веб-сервер:** Apache 2.4+
- **База данных:** MySQL 8.0+
- **PHP:** Версия 8.0 или выше
- **RAM:** Минимум 1 ГБ (рекомендуется 2 ГБ+)
- **Disk:** Минимум 10 ГБ свободного места

## 2. Пошаговая установка

### Шаг 1: Обновление системы и установка пакетов

Подключитесь к вашему VPS по SSH и выполните следующие команды для установки Apache, PHP, MySQL и необходимых расширений:

```bash
sudo apt-get update
sudo apt-get install -y apache2 php php-mysql php-curl php-json php-xml mysql-server git composer unzip
```

Включите модуль `rewrite` для Apache (необходим для работы маршрутизации):

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Шаг 2: Создание базы данных и пользователя MySQL

Войдите в консоль MySQL:

```bash
sudo mysql -u root -p
```
*(Если пароль root для MySQL еще не задан, возможно, потребуется просто `sudo mysql`)*

Выполните SQL-команды для создания базы данных и пользователя (замените `YOUR_PASSWORD` на надежный пароль):

```sql
CREATE DATABASE socialnets;
CREATE USER 'socialnets'@'localhost' IDENTIFIED BY 'YOUR_PASSWORD';
GRANT ALL PRIVILEGES ON socialnets.* TO 'socialnets'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Шаг 3: Развёртывание проекта

Перейдите в директорию веб-сервера и клонируйте репозиторий:

```bash
cd /var/www
# Замените [URL_ВАШЕГО_РЕПОЗИТОРИЯ] на реальный URL
sudo git clone [URL_ВАШЕГО_РЕПОЗИТОРИЯ] socialnets
cd socialnets
```

Установите зависимости PHP через Composer:

```bash
sudo composer install
```

Настройте права доступа. Apache обычно работает от пользователя `www-data`.

```bash
sudo chown -R www-data:www-data /var/www/socialnets
sudo chmod -R 755 /var/www/socialnets
# Если папка storage существует и требует записи:
sudo chmod -R 777 /var/www/socialnets/storage
```

### Шаг 4: Конфигурация приложения

1.  **Настройка базы данных:**
    Откройте файл конфигурации `config/database.php` (или скопируйте пример, если он есть):

    ```bash
    sudo nano config/database.php
    ```

    Убедитесь, что настройки подключения соответствуют созданным ранее:
    ```php
    <?php
    return [
        'host' => 'localhost',
        'dbname' => 'socialnets',
        'user' => 'socialnets',
        'password' => 'YOUR_PASSWORD',
        'charset' => 'utf8mb4',
    ];
    ```

2.  **Инициализация базы данных:**
    Импортируйте структуру базы данных из файла `database/init.sql`:

    ```bash
    mysql -u socialnets -p socialnets < /var/www/socialnets/database/init.sql
    ```

### Шаг 5: Конфигурация Apache

Создайте файл конфигурации виртуального хоста:

```bash
sudo nano /etc/apache2/sites-available/socialnets.conf
```

Вставьте следующее содержимое:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    # Или используйте IP сервера, если домена нет
    # ServerName YOUR_VPS_IP

    ServerAdmin webmaster@localhost
    DocumentRoot /var/www/socialnets/public

    <Directory /var/www/socialnets/public>
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

Включите сайт и перезагрузите Apache:

```bash
sudo a2ensite socialnets
sudo a2dissite 000-default.conf
sudo systemctl restart apache2
```

### Шаг 6: Проверка

1.  Откройте браузер и перейдите по адресу `http://YOUR_VPS_IP` (или по вашему домену).
2.  Должна загрузиться главная страница социальной сети.
3.  Попробуйте зарегистрировать нового пользователя и войти в систему.
4.  Проверьте смену языка и основной функционал.

## 3. Решение типичных ошибок

### Ошибки подключения к БД (Database Connection Error)
- Проверьте файл `config/database.php`: верно ли указаны имя пользователя, пароль и имя базы данных.
- Убедитесь, что сервер MySQL запущен: `sudo systemctl status mysql`.
- Попробуйте подключиться вручную: `mysql -u socialnets -p`.

### Ошибки 404 Not Found (кроме главной страницы)
- Убедитесь, что модуль `rewrite` включен: `sudo a2enmod rewrite`.
- Проверьте, что в конфигурации Apache для директории сайта установлено `AllowOverride All`.
- Проверьте наличие файла `.htaccess` в папке `public` и его содержимое.

### Ошибки прав доступа (Permission Denied)
- Убедитесь, что владелец файлов — `www-data` (или пользователь, от которого работает Apache):
  `sudo chown -R www-data:www-data /var/www/socialnets`
- Проверьте права на запись в папку `storage` (если используется для логов/кэша).

### Белый экран или ошибка 500
- Проверьте логи ошибок Apache: `sudo tail -f /var/log/apache2/error.log`.
- Убедитесь, что установлены все необходимые расширения PHP.
- Проверьте синтаксис PHP файлов.
