# accountant
Lightweight console based app I wrote to keep track of financial assets I kept buying in different places.

So far is pretty basic, it does just what I need:
* Stores amounts and prices
* Keeps track of average prices
* All in one place, just some keyboard commands away

## Install
Before starting, make sure these are installed in your system.
- php
- php-intl
- php-xml
- composer

Additionally, you'll need to install the driver for the database of your choice.

Clone this repository:
```bash
git clone https://github.com/subiabre/accountant
```

Install composer dependencies:
```bash
composer install
```

Update environment variables:
```bash
cp .env .env.local
```
Then open the new _.env.local_ file and edit the `DATABASE_URL` with the address of your DB. I use a local MYSQL instance but any Doctrine compatible db should do the trick.

And finally create the database:
```bash
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```

Aditionally, and since this application is entirely console-based, on *nix systems you can create a symlink so you can run this app from anywhere:
```bash
sudo ln -s /full/path/to/accountant/bin/console /usr/local/bin/accountant
sudo chmod +x /usr/local/bin/accountant
```
Now you can run this by simply typing `accountant` instead of php bin/console.

## Usage
Get a list of available commands:
```bash
accountant list account
```

Say you share an account in an exchange with your brother and you two buy [dogecoin](dogecoin.com) and don't want to split it 50/50 because one buys more than the other, or one buys in € and the other in $ and the maths are hard. Simply create a new book for each of you:

```bash
accountant new my_doge EUR # specify € for this book
accountant new bros_doge # default currency is $
```

And when someone increases their doges, simply let accountant know what, how much and at what cost:
```bash
accountant add my_doge 300 1
accountant add bros_doge 100 0.9
```
## Roadmap
- [x] Add support for currency notation in cost argument, e.g: `new some_stock 300 1€`
- [x] Allow books to be hidden from the book listing, e.g: `update some_book --hidden=true`
- [x] Add support to change rounding context of books on the fly, e.g: `read some_book --context=4`
- [ ] Add support to export data to JSON/CSV, e.g: `export some_book book.json`
- [ ] Add encryption for database going data
- [ ] Add password option to books, e.g: `update some_book --password=MyPassword`