# ACE VPGEGryphon

This a version of the SWS ace-gryphon application with customizations for VPGE.  It's directly tied to the installation profile located at `https://github.com/SU-SWS/vpge_profile`.  Most development work should be done in that profile, where there are directories for custom and contrib modules, as well as a profile helper module and a custom subtheme where modifications can be made to override the base theme, `stanford_basic`.

## Pulling upstream changes

Simply run: `git pull https://github.com/SU-SWS/ace-gryphon.git 2.x -X ours --no-edit`

## Deploying an artifact to Acquia

Simply run: `blt artifact:deploy --commit-msg "Your Commit Message" --tag "2.0.0"`

Make sure to include a good commit message, and make sure that the tag you use is valid and not currently used.  We encourage you to use [Semantic Versioning](https://semver.org/).

That command will build an artifact in the `/deploy` directory and upload it to the Acquia build repo with the chosen tag.  You can then deploy the artifact to the various environments in the application [here](https://cloud.acquia.com/a/applications/eed9a501-bc72-4e69-8d48-82e211f15f5a).

----
# Setup Local Environment - Native LAMP Stack

(See below for Lando config)

BLT provides an automation layer for testing, building, and launching Drupal 8 applications. For ease when updating codebase it is recommended to use  Drupal VM. If you prefer, you can use another tool such as Docker, [DDEV](https://docs.acquia.com/blt/install/alt-env/ddev/), [Docksal](https://docs.acquia.com/blt/install/alt-env/docksal/), [Lando](https://docs.acquia.com/blt/install/alt-env/lando/), (other) Vagrant, or your own custom LAMP stack, however support is very limited for these solutions.
1. Install Composer dependencies.
After you have forked, cloned the project and setup your blt.yml file install Composer Dependencies. (Warning: this can take some time based on internet speeds.)
    ```
    $ composer install
    ```
2. Setup a local blt alias.
If the blt alias is not available use this command outside and inside vagrant (one time only).
    ```
    $ composer run-script blt-alias
    ```
3. Set up local BLT
Copy the file `blt/example.local.blt.yml` and name it `local.blt.yml`. Populate all available information with your local configuration values.

4. Setup Local settings
After you have the `local.blt.yml` file configured, set up the settings.php for you setup.
    ```
    $ blt blt:init:settings
    ```
5. Get secret keys and settings
SAML and other certificate files will be download for local use.
     ```
    $ blt sws:keys
    ```

Optional:
If you wish to not provide statistics and user information back to Acquia run
     ```
    $ blt blt:telemetry:disable --no-interaction
    ```
# Setup Local Environment - Lando

1. Clone this repository somewhere on your local system.
2. Change directory into the location where you cloned the repository.
3. Run `cp lando/example.env .env; cp lando/example.lando.yml .lando.yml; cp lando/example.php.ini lando/php.ini; cp lando/example.local.sites.php docroot/sites/local.sites.php;`
4. Run `lando start`.  This will perform all the basic installation steps.
5. Run `lando composer sync-vpge`.  This will sync the VPGE database and files to your local system.  The sync comes from the Stage server.
6. Run `lando composer sync-diversityworks`.  This will synce the DiversityWorks database and file to your local system.  The sync comes from the Stage server.

If using lando, prefix any `blt` commands with `lando`, e.g., `lando blt drupal:install`.  The same applies to composer commands.

### Lando local drush aliases

The VPGE site is `@default` -- e.g, `@default.local, @default.dev, @default.test`
The Diversityworks sit is `@diversityworks` -- e.g., `@diversityworks.local, @diversityworks.dev, @diversityworks.test`

So, for instance, to clear the caches on your local copy of VPGE:
```
lando drush -y @default.local cr

```

---
## Other Local Setup Steps

1. Set up frontend build and theme.
By default BLT sets up a site with the lightning profile and a cog base theme. You can choose your own profile before setup in the blt.yml file. If you do choose to use cog, see [Cog's documentation](https://github.com/acquia-pso/cog/blob/8.x-1.x/STARTERKIT/README.md#create-cog-sub-theme) for installation.
See [BLT's Frontend docs](https://docs.acquia.com/blt/developer/frontend/) to see how to automate the theme requirements and frontend tests.
After the initial theme setup you can configure `blt/blt.yml` to install and configure your frontend dependencies with `blt setup`.

2. Pull Files locally.
Use BLT to pull all files down from your Cloud environment.

   ```
   $ blt drupal:sync:files
   ```

3. Sync the Cloud Database.
If you have an existing database you can use BLT to pull down the database from your Cloud environment.
   ```
   $ blt sync
   ```


---

# Resources

Additional [BLT documentation](https://docs.acquia.com/blt/) may be useful. You may also access a list of BLT commands by running this:
```
$ blt
```

Note the following properties of this project:
* Primary development branch: 1.x
* Local environment: @default.local
* Local site URL: http://local.example.loc/

## Working With a BLT Project

BLT projects are designed to instill software development best practices (including git workflows).

Our BLT Developer documentation includes an [example workflow](https://docs.acquia.com/blt/developer/dev-workflow/).

### Important Configuration Files

BLT uses a number of configuration (`.yml` or `.json`) files to define and customize behaviors. Some examples of these are:

* `blt/blt.yml` (formerly blt/project.yml prior to BLT 9.x)
* `blt/local.blt.yml` (local only specific blt configuration)
* `box/config.yml` (if using Drupal VM)
* `drush/sites` (contains Drush aliases for this project)
* `composer.json` (includes required components, including Drupal Modules, for this project)

## Resources

* GitHub - https://github.com/SU-SWS/ace-gryphon
* Acquia Cloud subscription - https://cloud.acquia.com/app/develop/applications/8449683b-500e-4728-b70a-5f69d9e8a61a
