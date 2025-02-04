# AI Model API

<a href="https://github.com/robuedi/properties-api/actions"><img src="https://github.com/robuedi/properties-api/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a>
<a href="https://github.com/robuedi/properties-api/actions"><img src="https://github.com/robuedi/properties-api/actions/workflows/lint.yml/badge.svg" alt="Lint"></a>


This project is the backend API for interacting locally with a text AI model, in this case ollama

## Setup

0. Install Docker on your system
1. `git clone git@github.com:robuedi/ollama-ai-laravel.git && cd ollama-ai-laravel`
2. `php artisan key:generate && composer install`
2. start the docker containers, first time it will take longer `sail up -d`
3. Prepare the DB with: `sail artisan migrate`

WARNING: the AI model might use at least 12 GB of RAM, so please have more on your system.

## API Endpoints Docs

Your local API Docs URL [here](http://localhost/docs/api#/)
![AI Model API](/readme/docs.png)

## License

Copyright (C) Eduard Cristian Robu - All Rights Reserved
Written by Eduard Cristian Robu <robu.edi.office at gmail.com>, 2024
