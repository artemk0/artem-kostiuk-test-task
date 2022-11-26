# Overview
* there have been some problems at the very beginning: the API Endpoint for getting exchange rates `https://api.exchangeratesapi.io/latest` requires an API key and the free one which could be obtained is using endpoint `https://api.apilayer.com/exchangerates_data/latest`. Not sure if it was a part of a task, most likely yes;)
* in addition to above in [Settings.php](https://link-url-here.org) I've included my free API key
* generally I've tried to keep the code free from all the dependencies: used `file_get_contents()` approach instead of cUrl, minimum PHP version should be 7.4 (haven't tested it though: developed with php8.1), the only composer dependency is `ext-json` and `phpunit` in `require-dev`
* tried to follow the [Paysera PHP style guide](https://github.com/paysera/php-style-guide) however there might be some exceptions only because the document is so big;)
* checkout running it with `input2.txt`: maybe some improvements for displaying error cases
* in total task took me around 6 hours with a break: Friday evening and Saturday morning, including some disturbance watching football in the background;)

# Usage
* Init: `composer install`
* Run: `php src/app.php input.txt` | `php src/app.php input.txt`
* Tests: `./vendor/bin/phpunit`

# Hope you'll like it!;)
