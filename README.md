***
This is case study task for productsup. This application is built on symfony 6 framework.
***
Requirements
--
    - php 8.1 
    - composer

***

Steps to setup
-
1. git clone clone_url
2. docker build . --tag productsup-api
3. docker run -d -p 8000:8000 --name productsup-api
4. visit http://localhost:8000
***

Note:
-
1. This application runs on sqlite. DB type can be changed from .env file configuration.
2. For upload csv format, check var/users.csv file.
3. admin/admin are the default login credentials
