WP-CLI `wp scaffold theme-tests`
=============================

[![Build Status](https://travis-ci.org/miya0001/wp-cli-scaffold-theme-tests.svg?branch=master)](https://travis-ci.org/miya0001/wp-cli-scaffold-theme-tests)

Generate files needed for running tests for a WordPress theme.

* PHP_CodeSniffer
	* WordPress coding standards
* phpmd

## Getting Started

Clone this project.

```
$ git clone git@github.com:miya0001/wp-cli-scaffold-theme-tests.git
```

Add following lines into your `~/.wp-cli/config.yml`.

```
require:
  - /path/to/scaffold-theme-tests/command.php
```

Then run `wp help scaffold theme-tests`.

## How it works

* Run command `wp scaffold theme-tests <theme-slug>`, it creates some files in the theme direcotry.
* Signup or register theme github repository to the Travis CI.
* Run `git push`.

Then follwoing commands will be executed on the Travis CI automatically.

* Install composer
* Setup WordPress coding standards
* Run CodeSniffer and PHPMD.
