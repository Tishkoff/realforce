# REALFORCE Test task

Please note. This task is not flexible enough to cover all requirements and possible usage scenarios. I hope that it is flexible enough to demonstrate my ability to code.

## Deployment instructions
Start by downloading this code.

Project is shipped with a Docker setup that makes it easy to get a containerized development environment up and running. If you do not already have Docker on your computer, it's the right time to install it.

On Mac, only Docker for Mac is supported. Similarly, on Windows, only Docker for Windows is supported. Docker Machine is not supported out of the box.

Open a terminal, and navigate to the directory containing your project skeleton. Run the following command to start all services using Docker Compose:

`$ docker-compose pull # Download the latest versions of the pre-built images`

`$ docker-compose up -d # Running in detached mode`

Run migrations to prepare db wih the following command:
`$ docker-compose exec php bin/console migrate`

You can now load fixtures in the database with the following command:

`$ docker-compose exec php bin/console hautelook:fixtures:load`

It's Ready! Fixtures contains data from task itself.
Open https://localhost in your favorite web browser.

You'll need to add a security exception in your browser to accept the self-signed TLS certificate that has been generated for this container when installing the framework. Repeat this step for all other services available through HTTPS.

Click on the "HTTPS API" button, or go to https://localhost:8443/ or go to generated UI Cliking on the 'HTTPS Admin' or opening https://localhost:444 in your browser.

Tests

In order to execute tests please run following command:
`$ docker-compose exec php vendor/bin/simple-phpunit`    

## Design considerations

Since task is for back-end - main product here is the api. Othere thigns like UIs are just automatically generated to help understanding what is going on.

### Entities:
1. Employees. This entity contains only name and salary as hardcoded properties. Other properties are dynamic.
2. Properties. Each Employee entity can have as many properties as you wish. Properties are just need to be declared before. Property is just a 'name'. All properties type is string. But we cann store other types like integer and boolean as well. Thanks to dynamic nature of PHP.
3. PropertyValues. Here actually are dynamic values stored for employees. 
4. PropertyConditions. Each property can have several conditions attached to it. All is dynamic. Conditions will be appended via logical AND operator.

### Condition structure:
1. Debit or credit - if 0 then debit - if 1 credit
2. flat value or percentage - 0 for flat value and 1 for percentage
3. condition operator - supported operators are ==, !=, >=, <=, >, <.
4. condition value - value that property will be compared with
5. amount - this is the flat value or percentage that will affect salary

### Example 1:
Age condition:
`<debit_credit: 1, flat_percent: 1, condition: '>=', value: '50', amount: 7>`

### Example 2:
Country Tax condition:
`<debit_credit: 0, flat_percentage: 1, condition: '==', value: '1', amount: 20>`
Please note that for existence we are checking with property value equelas '1'

### Application Structure:
1. There is wall CRUD APIs collection for all entities
2. Special API for salary calculation 
`employees\{id}\salary` it returns additional `finalSalary` property with all calculations done.
