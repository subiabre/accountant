# reck
Command line accounting tool.

1. [About](#about)
2. [Installation](#installation)
    1. [Prerequisites](#prerequisites)
    2. [Installing](#installing)
    3. [Configuring the database](#configuring-the-database)
    4. [Setting up reck globally](#setting-up-reck-globally)
3. [Usage](#usage)
    1. [Basic usage](#basic-usage)
    2. [How to work with reck](#how-to-work-with-reck)
4. [Roadmap](#roadmap)

# About
`reck` is a command line application to record and manage inventory and accountancy of items. It can help you to:
- keep track of buys and sales
- calculate costs, revenue and earnings

# Installation

## Prerequisites
This application requires that your system has:
- [php](http://php.net)
- [composer](http://getcomposer.org)

Additionally, you will have to install the php driver for your database of choice, e.g: `php-mysql` and you may need to install `php-intl` and `php-xml`.

## Installing
Download or clone this repository.
```bash
git clone https://github.com/subiabre/reck
cd reck
```

Install the dependencies.
```bash
composer install
```

### Configuring the database
`reck` works by storing entries in a local database instance. Before starting to use it, you need to set up your database and configure reck to work with it. This can be a tiresome task but this application attempts to make it as easy as possible for you.

After completing the necessary steps to work with your database of choice on your system and installing the php driver for it:

Copy the *.env* file.
```bash
cp .env .env.local
```

And edit your *.env.local* with the address for your database.

Example MySQL:
```.env
DATABASE_URL="mysql://myuser:password@127.0.0.1:5432/database?serverVersion=13&charset=utf8"
```
Example SQLite:
```.env
DATABASE_URL="sqlite:///%kernel.project_dir%/var/app.db"
```

Once `reck` knows where the database is at let it handle all the schema creation.
```bash
composer run-script database-start
```

And you're ready to go and account. However, you may like to be able to access your accouning data from anywhere on the machine:

### Setting up `reck` globally
Aditionally, and since this application is entirely console-based, on *nix systems you can create a symlink so you can run this app from anywhere.
```bash
sudo ln -s /full/path/to/reck/bin/console /usr/local/bin/reck
sudo chmod +x /usr/local/bin/reck
```

# Usage

## Basic usage
Get a list of available commands:
```bash
reck list commands
```

If you need help with a command:
```bash
reck help <command>
```

## How to work with `reck`
Let's start with an example. Say you have a shop where you sell furniture: chairs, tables, etc. To make it easier let's also assume all your items are of the same model.

### Creating books
```bash
reck new chairs
reck new tables
reck new shelfs
```

### Reading books
You just created three books; these are individual record holders for items of the same type, transacted under the same currency and with a common accounting. See them with:
```bash
# See general data for chairs, tables and shelfs
reck books
# See entries data for chairs
reck read chairs
```

### Recording buys and sales
Each time you buy or sell one of the items in your shop tell `reck` by adding a new entry to that book:
```bash
# Bought chairs, 10 units at a total price of 90
reck add buy chairs 10 90
# Sold chairs, 1 unit at a price of 12
reck add sell chairs 1 12
```

Keep in mind that each book represents an homogenous item. For two different models of chairs that you sell at different prices you will need to add entries on their respective, separate, books.

### Using different accounting models

Now let's say you want to know your earnings on chairs by [FIFO](https://www.investopedia.com/terms/f/fifo.asp) but for your tables on [Weighted Average](https://www.investopedia.com/terms/w/weightedaverage.asp). The latter is the default accounting method, so just update your *chairs* book:
```bash
reck update chairs fifo
```

The accounting and the currency can be defined in a book at the moment of creation:
```bash
# New book `desks` will use `fifo` accounting and take `EUR` as currency
reck new desks fifo EUR
```

You can get a list of all available accounting models for your books typing `reck accountings`.

### Deleting a book

You remembered that in fact you have never bought or sold a single shelf in your shop, so delete that book:
```bash
reck drop shelfs
```
This will remove the book and all of the entries that it contained from the database, meaning neither `reck` nor anything else can access that data anymore as it does not exist. This is not reversible but there is one expection:

### Backing up your data

In case you were going to wipe out your entire system `reck` comes with a tool to export and import it's accounting data, at the moment of the export, to an external file for later restorage.
```bash
reck export my_shop_accounting_books.json
reck import my_shop_accounting_books.json
```
This file is not encrypted and anyone with access to this file can access your accounting information as it was when the file was generated. You should keep this file safe and delete it once you've imported it's content.

# Roadmap
- [ ] Add unit tests and code coverage
- [ ] Add LIFO accounting
- [ ] Join books entries: `reck join book1 book2`
- [ ] Set passwords: `reck new fifo EUR P455W0RD`
