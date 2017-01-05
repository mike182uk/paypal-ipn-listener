# Contributing

Contributions are **welcome!**

Contributions can be made via a Pull Request on [GitHub](https://github.com/mike182uk/paypal-ipn-listener).

## Reporting an Issue

Please report issues via the issue tracker on [GitHub](https://github.com/mike182uk/paypal-ipn-listener). For security-related issues, please email the maintainer directly.

## Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - [PHP CS Fixer](https://github.com/FriendsOfPHP/PHP-CS-Fixer). Make sure you run `composer run-script fix` before committing your code.

- **Add specs where appropriate** - [PHPSpec](http://www.phpspec.net/en/latest/)

- **Add examples for new features** - [Behat](http://docs.behat.org/en/v3.0/)

- **Document any change in behaviour** - Make sure the README and any other relevant documentation are kept up-to-date.

- **Create topic branches** - i.e `feature/some-awesome-feature`.

- **One pull request per feature**

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.

## Running the specs

```bash
composer run-script specs
```

## Running the examples

To run the examples you need to make sure the mock server is running. The mock server requires the `MOCK_SERVER_PORT` environment variable to be set:

```bash
export MOCK_SERVER_PORT=3000
```

Once the `MOCK_SERVER_PORT` environment variable is set, start the mock server:

```bash
composer run-script mock-server
```

You should now be able to run the examples:

```bash
composer run-script examples
```

If you started the mock server in a different terminal window / tab to where you are running the examples, you will need to set the `MOCK_SERVER_PORT` environment variable again.
