### Motivation

Here I am usging [this skeleton](https://github.com/paysera/skeleton-commission-task/archive/master.zip) as a bootstrap.

### Requirements

```
"php": ">=7.1"

```
### Installation

Use defeault .env or create a new one from .env.example . For new creation you must check value for EXCHANGE_RATES_API_URL(https://developers.paysera.com/tasks/api/currency-exchange-rates). In defeault .env file, I have used "|-|" once, as a url seperator to hide scensative key word from url. But onec in url. If you don't need to hide any word on url, then ignore it.

Now install composer dependencies using the below command.

```
composer install

```

A sample CSV file as named input.csv is included in the project root as narreted.

You are done.

### Run script

Run the below command.

```
php script.php app:fees input.csv

```

The output fees value depends on the currency rate, which is gathered from the provided EXCHANGE_RATES_API_URL(https://developers.paysera.com/tasks/api/currency-exchange-rates) url.


Also you can change the value for RULE_DEPOSIT_FEE, RULE_WITHDRAW_BUSINESS_FEE, RULE_WITHDRAW_PRIVATE_FEE. Here I am using the 
### Run tests

```
composer run test

```