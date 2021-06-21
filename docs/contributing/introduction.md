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
Now you can enable using this server by calling the command `php artisan voyager:dev --enable` or by going to the `Settings => Admin` and changing the `Dev-Server` setting to `Yes`.  

When you are done, you can simply change the setting to `No`, call `php artisan voyager:dev --disable` or click `Disable` on the notification that appears when your dev-server becomes unavailable.

## Breaking changes

...