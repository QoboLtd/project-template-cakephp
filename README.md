project-template-cakephp
========================

**This is a non-stable project for CakePHP 3.x at this time. It is currently under development and should be considered experimental.**

This is a template for the new CakePHP project.

Install
-------

When starting a new PHP project, do the following:

```bash
# Initiate the new work space
mkdir new-project
cd new-project
git init
# Kick off the project (this is needed for --squash merge later)
touch README.md
git add README.md
git commit -m "Initial commit"
# Get project-template-cakephp
git remote add template https://github.com/QoboLtd/project-template-cakephp.git
git remote update
# Merge latest tag (or use 'template/master' instead)
git merge --squash $(git tag --sort=v:refname | tail -n 1)
git commit -m "Merged project-template-cakephp ($(git tag --sort=v:refname | tail -n 1))"
# Finalize the setup
composer install
./vendor/bin/phake dotenv:create
```

Usage
-----

Now you can develop your PHP project as per usual, but with the following
advantages:

* Support for [PHP built-in web server](http://php.net/manual/en/features.commandline.webserver.php) (start with ```php -S localhost:8000```)
* Per-environment configuration using ```.env``` file, which is ignored by git
* Powerful build system ([phake-builder](https://github.com/QoboLtd/phake-builder)) integrated
* Composer integrated with ```vendor/``` folder added to ```.gitignore``` .
* PHPUnit integrated with ```tests/``` folder and example unit tests.
* Sensible defaults for best practices - favicon.ico, robots.txt, MySQL dump, Nginx configuration, GPL, etc.

For example, you can easily automate the build process of your application
by modifying the included ```Phakefile```.  Run the following command to examine
available targets:

```
./vendor/bin/phake -T
```

As you can see, there are already some placeholders for your application's build
process.  By default, it is suggested that you have these:

* ```app:install``` - for installation process of your application,
* ```app:update``` - for the update process of the already installed application, and
* ```app:remove``` - for the application removal process and cleanup.

You can, of course, add your own, remove these, or change them any way you want.  Have a look at
[phake-builder](https://github.com/QoboLtd/phake-builder) documentation for more information on how
to use these targets and pass runtime configuration parameters.


Test
----

###Quick

Now that you have the project template installed, check that it works
before you start working on your changes.  Fire up the PHP web server:

```
bin/cake server -H localhost -p 8000
```

In your browser navigate to [http://localhost:8000](http://localhost:8000).
You should see the standard CakePHP home page.  If you do, all parts
are in place.

###PHPUnit

project-template-cakephp brings quite a bit of setup for testing your projects.  The
first part of this setup is [PHPUnit](https://phpunit.de/).  To try it out,
runt the following command (don't worry if it fails, we'll get to it shortly):

```
./vendor/bin/phpunit
```

If it didn't work for you, here are some of the things to try:

* If ```phpunit``` command wasn't found, try ```composer install``` and then run the command again.  Chances are phpunit was removed during the ```app:install```, which runs composer with ```--no-dev``` parameter.
* If ```phpunit``` worked fine, but the tests failed, that's because you probably don't have a web and Selenium server running yet (more on that later).  For now, try the simplified test plan: ```phpunit --exclude-group selenium --exclude-group network```.
* If you had some other issue, please [let us know](https://github.com/QoboLtd/project-template-cakephp/issues/new).

###Selenium

[Selenium](http://www.seleniumhq.org/) is a testing platform that allows one to run tests through a real browser.
Setting this up and getting an example might sound complicated, so project-template-cakephp
to the rescue.  Here is what you need to do:

* Download [Selenium Server Standalone JAR file](http://selenium-release.storage.googleapis.com/2.49/selenium-server-standalone-2.49.0.jar) (check ```.travis.yml``` for newer versions).
* Start the web server (in separate terminal or in background): ```php -S localhost:8000```
* Start the Selenium server (in separate terminal or in background): ```java -jar selenium-server-standalone-2.49.0.jar```

Now you can run the full test suite with:

```
./vendor/bin/phpunit
```

Or just the Selenium tests with:

```
./vendor/bin/phpunit --group selenium
```

###Travis CI

Continious Integration is a tool that helps to run your tests whenever you do any
changes on your code base (commit, merge, etc).  There are many tools that you can
use, but project-template-cakephp provides an example integration with [Travis CI](https://travis-ci.org/).

Have a look at ```.travis.yml``` file, which describes the environment matrix, project installation
steps and ways to run the test suite.  For your real project, based on project-template-cakephp, you'd probably
want to remove the example tests from the file.

###Examples

project-template-cakephp provides a few examples of how to write and organize unit tests.  Have a look
in the ```tests/``` folder.  Now you have **NO EXCUSE** for not testing your applications!



