
## Quick Start

-   `composer install `
-   Create database and modify .env with your DB name and Pusher credentials.
   Note that the existing pusher credentials are working
-   `php artisan migrate --seed`
-   `php artisan key:generate`
-   `npm install && npm run dev`
-   `php artisan storage:link`
-   `php artisan serve`

then choose a user from the database and login. 

## Chat System
[docs] https://chatify.munafio.com/

chatting system is build above CHATIFY Package, which provide the following:
- build-in interactive real-time web page for chatting
- Full API for integration with mobile application
- ability to set up custom socket server like pusher

By default, CHATIFY works with pusher as a socket service provider


## Users-RelationShips (Friends/followers)
[docs]  https://github.com/multicaret/laravel-acquaintances

this package can easily manage friends and relationships like being friend with someone,
or following someone or something, also liking add rating system.
Its documentation very rich and helpful, but it came with no interface component at all.
So I hope you use this package to manage users relationship simply.

## Video Chatting

We shall use Agora as Video Chatting provider, the following links shows how is the 
implementation using agora would look like.

- Tutorial 1 (youtube) + github repo

https://github.com/AgoraIO/API-Examples-Web/tree/main/Demo/basicVideoCall
https://www.youtube.com/watch?v=1cYKoSe3MN4

- Tutorial 2 (Blog Post on Dev.to)

https://dev.to/mupati/using-agora-for-your-laravel-video-chat-app-1mo
