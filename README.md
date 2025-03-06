Steps to run this code:

cp .env.example .env

composer install

get https://apilayer.com/ key for API requests:

BIN_LOOKUP_URL=https://lookup.binlist.net/
BIN_LOOKUP_API_KEY=
EXCHANGE_RATES_URL=https://api.apilayer.com/exchangerates_data/latest
EXCHANGE_RATES_API_KEY=

run: php index.php input.txt

run all tests: ./vendor/bin/phpunit tests/
