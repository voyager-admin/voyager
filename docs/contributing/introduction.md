# Contributing

Thanks for contributing to Voyager!  
Here are some important things to keep in mind:

## Tests

It is important to provide testing for all newly introduced code if possible.

## Coverage

We let [code climate](https://codeclimate.com/github/voyager-admin/voyager) check our test coverage.  

## Developing Javascript and CSS

If you want to change anything related to Javascript and/or CSS, you can make your life easier by using hot module reloading.  
Hot module reload allows you to change code and instantly see the changes you made without the need to reload your browser window or clearing your cache.


To do so, you first have to install all NPM modules by running `npm install`.  
After that run `npm run watch` to provide a dev-server serving the compiled assets.  
This will provide a URL (normally `http://localhost:8080`) which you then can use by calling the command `php artisan voyager:hmr --enable http://localhost:8080` or by going to the `Settings => Admin` and adding the URL to the `Dev-Server URL` setting.  

When you are done, you can simply empty the setting, or call `php artisan voyager:hmr --disable`

## Breaking changes

...