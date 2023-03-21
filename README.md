# wordle-API

### How to boot up
- Open up docker desktop program
- Open terminal in this folder and use the following commands:
- $ docker-compose up -d --detach --build
- $ docker-compose exec app bash
- $ php artisan migrate
You can now close this terminal and use the API!

### URLs:
- api: http://localhost:8080/api
- phpmyadmin: http://localhost:8081
- mail catcher: http://localhost:8025

### API calls:

For API calls marked as [protected] a token must be passed through an Authorization header as ``Bearer {token}``

- ``/client/new`` - creates a new client account and returns client_key and secret key

- ``/auth/{client_key}/register`` (``username``, ``email`` and ``password`` as form data) - creates a new user
- ``/auth/{client_key}/login`` (``email`` and ``password`` as form data) - logs a user in and returns their access and refresh token

- ``/wordle/{client_key}/newgame`` (optional ``length`` as form data) [protected] - creates a new wordle sessions and returns $length words (default 10) and a session uuid
- ``/wordle/{client_key}/setscore/{session_key}`` (``score`` as form data) [protected] - updates a wordle session's score. a session lasts until a score has been unputted an amount of times equal to it's length, upon which it will close. 
- ``/wordle/{client_key}/currentscore/{session_key}`` [protected] - gets a running session's score
- ``/wordle/{client_key}/topscore`` (optional ``offset`` (0), optional ``limit`` (10) and optional ``date`` as form data) [protected] - returns a list of top scores
