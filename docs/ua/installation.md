# Встановлення

## Через Docker
1. Запустіть Docker-контейнери:
   ```bash
   ./manage-docker.sh start
   ```
2. Запустіть `./install.sh` що створює конфігураційні файли.
3. Відредагуйте наступні файли конфігурацій:
    * `.env`
    * `./config/app.php`
    * `./config/routes.yaml`
    * `./public/.htaccess`
4. За потреби відредагуйте файли `Dockerfile` та `docker-compose.yaml` 
5. Відкрийте проект у браузері за адресою: [http://localhost:8080](http://localhost:8080).
Адреса може відрізнятись в залежності від налаштувань в `.env`.

## Розгортання на LAMP

1. Встановіть composer пакунки:
    ```bash
    composer install
    ```
2. Запустіть `./install.sh` що створює конфігураційні файли.
3. Відредагуйте наступні файли конфігурацій:
    * `.env`
    * `./config/app.php`
    * `./config/routes.yaml`
    * `./public/.htaccess`
4. За потреби відредагуйте файли `Dockerfile` та `docker-compose.yaml` 
5. Відкрийте проект в браузері за посиланням, що визначено налаштуваннями LAMP
