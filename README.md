# Update July 2024
* I've already completed this task in November 2022, it can be found in [master branch](https://github.com/artemk0/artem-kostiuk-test-task) of this repo
* Now I've refactored it a little mostly (except one thing) in terms of adjusting it to work with PHP 8
* I haven't changed any logic of how it works since, in my opinion, it fully follows the requirements, and I'm quite happy with it:)
* The https://lookup.binlist.net/ keeps returning 429 errors even though they claim that rate limit should allow 5 requests within an hour, it blocks me on the second try:(
* So because of the above, I've decided to add a local open source bin numbers DB and use it as a backup, you can see how it is used in [Usage](#usage) section

# Overview
* there have been some problems at the very beginning: the API Endpoint for getting exchange rates `https://api.exchangeratesapi.io/latest` requires an API key and the free one, which could be obtained, is using endpoint `https://api.apilayer.com/exchangerates_data/latest`. Not sure if it was a part of a task, most likely yes;)
* in addition to the above in [Settings.php](https://github.com/artemk0/artem-kostiuk-test-task/blob/master/src/Settings.php) I've included my free API key
* generally I've tried to keep the code free from all the dependencies: used `file_get_contents()` approach instead of cUrl, ~~a minimum PHP version should be 7.4 (haven't tested it though: developed with php8.1)~~, the only composer dependency is `ext-json` and `phpunit` in `require-dev`
* tried to follow the style guide, however, there might be some exceptions only because the document is so big;)
* checkout running it with `input2.txt`: maybe some improvements for displaying error cases
* in total task took me around 6 hours with a break: Friday evening and Saturday morning, including some disturbance watching football in the background;)

# Usage
* Init: `composer install`
* Run: `php src/app.php input.txt` or `php src/app.php input2.txt`
* Add any string except "remote" as second param to use local BIN DB, e.g. `php src/app.php input.txt local`
* Tests: `./vendor/bin/phpunit`

# Hope you'll like it!;)
