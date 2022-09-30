# ECF-Back
<p>This project aim is to simulate an ecommerce website using Symfony.</p><br>

# Installation
<p><strong>Run the following code:</strong></p>
<p>&nbsp;&nbsp;<code>composer install</code></p>

***

<p><strong>Create a .env.local file at your root project and configure your DATABASE_URL path by replacing the DATABASE_NAME and DATABASE_PASSWORD:</strong></p>
<p>&nbsp;&nbsp;DATABASE_URL="mysql://DATABASE_NAME:DATABASE_PASSWORD@127.0.0.1:3306/ecf-back"</p>

***

<p><strong>Create the database and seed it:</strong></p>

- <code>php bin/console doctrine:database:create</code>
- <code>php bin/console doctrine:migrations:migrate</code>
- <code>php bin/console doctrine:fixtures:load</code>

***

<p><strong>Run the symfony server using Wamp or an equivalent:</strong></p>
<p>&nbsp;&nbsp;<code>symfony serve</code></p>

***

<p>Enjoy!</p>
