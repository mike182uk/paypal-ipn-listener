#Contributing

Contributions are **welcome** and will be fully **credited**.

Contributions can be made via a Pull Request on [Github](https://github.com/mike182uk/paypal-ipn-listener).

##Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer) is included as a dev dependency. Make sure you run `bin/php-cs-fixer fix` before committing your code.

- **Add specs where appropriate** - [PHPSpec](http://www.phpspec.net/en/latest/)

- **Add examples for new features** - [Behat](http://docs.behat.org/en/v3.0/)

- **Document any change in behavior** - Make sure the README and any other relevant documentation are kept up-to-date.

- **Create topic branches** - i.e `feature/some-awesome-feature`.

- **One pull request per feature**

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.


##Running Specs

```bash
bin/phpspec run
```

##Running Examples

To successfully run the examples you need to make sure the mock server is running. The mock server requires node to be installed and the `MOCK_SERVER_PORT` environment variable to be set:

```bash
export MOCK_SERVER_PORT=3000
cd features/bootstrap/server
npm install
node server.js
```

Once the server is running you should be able to run Behat from the project directory:

```bash
bin/behat
```

