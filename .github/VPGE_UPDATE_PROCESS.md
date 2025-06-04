# VPGE Drupal Site Update Process

This document serves as a guide to updating VPGE site, blending practical steps with higher-level technical considerations. It aims to support SWS developers in managing upgrades across local, staging, and production environments while maintaining stability, test coverage, and developer clarity.
## Purpose

This document is a comprehensive guide for updating Drupal sites maintained by **VPGE**. It synthesizes hands-on update steps with technical and architectural recommendations to ensure each site upgrade—from local development through deployment—is stable, testable, and maintainable.

---

## 1. Install the Site Locally

### Steps

- Clone the site repository.
- Run `composer install`.
- Import the database (from Acquia or backup).
- Run database and config updates:
```
drush updb -y
drush cim -y
```

## 2. Assess and Pull Downstream Changes

> **VPGE** uses a downstream profile like stanford_profile, you’ll want to sync updates:

1. Remove the current profile directory: `rm -rf docroot/profiles/custom/vpge_profile`
2. Clone and pull updates from the canonical profile: `git pull https://github.com/SU-SWS/stanford_profile.git 11.x -X ours --no-edit`
3. Run `drush cim -y` to integrate any config changes from the updated profile. For **VPGE**, please make sure you have any config changes are duplicated and/or applied to the /config/split directory. .

## 3. Update stanford_profile and dependencies

In the project root composer.json, update the reference to stanford_profile to use the appropriate dev branch or 11.x-dev.
1. Run `composer update -w` or `composer update su-sws/stanford_profile -W` to only update the profile.
2. Add the dev version of the VPGE profile to the composer json in project route.
2. Commit and push changes. Create a PR from this branch.
3. Recommendation: Standardize branch naming conventions (e.g., update/drupal-11) and PR titles ([Update] Drupal 11 + Profile Changes) to improve clarity across teams.

## 4. Audit VPGE Modules and Custom Code Updates Use Upgrade Status
1. If not installed: `composer require drupal/upgrade_status --dev`
2. Visit `/admin/reports/upgrade-status` to assess module readiness, deprecated code, and unsupported projects.
3. Follow the steps to update custom VPGE code.
4. Check for any outdated dependencies:  `composer outdated'
5. Review patch failures; use Composer’s output to remove fixed patches or address changes upstream.
6. Use PHP Codesniffer to review custom theme, modules and PHP:

   phpcs --standard=Drupal,DrupalPractice path/to/custom/modules

   Or rely on Upgrade Status module’s deprecated code analysis.

   Stack-level tip: As part of long-term sustainability, encourage teams to phase out deprecated contrib modules and rely on upstream-supported alternatives.
7. If any downstream updates necessitate updates to the theme, update npm. Review to see node --version needs updating.

## 5. Push code to initiate a code review.
1. Commit your changes to the VPGE profile, including:
   - Composer updates
   - Config exports (drush cex -y)
   - Test-related YAML updates
   Push to GitHub and open a PR for review, if not already done.


### Ian's suggested method for upstream updates:
#### `vpge_profile`
1. Clone the profile to a convenient location: `git clone git@github.com:SU-SWS/vpge_profile.git ~/code/vpge_profile && cd ~/code/vpge_profile`
2. Determine the newest published tag on `stanford_profile`.  At the time of this writing, that's `11.7.2`, but it will be different when you are doing this.
3. Create a new branch: `git checkout -b release_xxx`
4. Pull the upstream updates: ` git config pull.rebase false && git pull https://github.com/SU-SWS/stanford_profile.git tags/11.7.2 -X ours --no-edit --no-commit`.  Remember to replace the tag with the latest published tag on stanford_profile. The `no-commit` option will give you the chance to go back and review the changes (and revert anything if necessary).
5. Open your code editor (assuming you have a way of looking at git diffs within it). `code .`
6. Go through the updates which have been pulled in.  Make sure they are appropriate.  Double check namespace changes, changes to github workflows, and any custom code to make sure there are no unexpected changes.  Pay particular attention to ensure that any changes made to the `config_split` configs are correct.
7. Update the `vpge_profile.info.yml` to reflect the tagged version of the `stanford_profile` you merged with.  Update the `CHANGELOG` appropriately as well.
8. Add your changes, commit, and push.  Make a new PR for the release branch.
9. Ensure tests pass.  If they do not, investigate and correct.

#### `ace-vpgegryphon`
1.  Create a release branch.
2.  Update the `composer.json` file to reflect the dev branch of the VPGE profile, and the tagged version of the `stanford_profile` that you pulled in upstream changes.  Important: Use the tagged version of `stanford_profile`  Using the `11.x` branch may pull in unreleased work which will complicate your process.
3.  `composer update`
4.  `drush @default.local deploy && drush @diversityworks.local deploy`
  - you may run into quirks which prevent this from working the first time.  Troubleshoot as necessary and correct any problems preventing the update.
  - I like to go ahead and commit the results to your new branch at this point.
5. So now you have the DB updates done, and the configs imported.  You're going to want to export the config to get the stuff in the config/sync directory.
  - `drush @default.local cex && drush @diversityworks.local cex`
6. Now you will likely have more changes to your config directory.
  - go through and check to make sure nothing harmful has occurred.
  - Now is a good time to check the site on your localdev.  Make sure:
    - cron runs correctly
    - check the logs for errors, fix anything relevant.
    - `/professional-development/search-grad-grow` <- this is a custom form on VPGE.  make sure it still works.
7. Commit your changes and push them up.  See if the tests pass. Spoiler alert: they probably won't.
  - starting with the lowest level tests (usually phpunit), examine the run logs on github.
  - check the github action to make sure you're using the best version of `stanford-caravan` and anything else which might have changed.  If there's a new version of Drupal, or an OS update, or a PHP update, you may want to revert to the previous version of the github workflow to get it to run correctly.
  - Check the EventSubscriber, and make sure it matches the one in `stanford_profile`.  Be cautions to maintain the `vpge_profile` namespace.  Also check the vpge_profile.services.yml file to make sure you're injecting the right parameters.
8. Once tests pass, you have a release candidate. In Acquia, copy the production databases and files to DEV.  There is an `acquia-pipelines.yml` file in the root of the repo which should automatically build an artifact on Acquia.  Deploy it to the DEV server and make sure the updates apply cleanly.  If they do not, investigate and correct.
9. At this point, you should have a release-ready artifact which passes all tests.  Merge the profile PR, tag it, and create a release.  Update your root `composer.json` to reference the tagged version of the profile.
10. `composer update` to update the lock file, let it pass the tests, tag the release, create the release notes, and let it build the tagged artifact on Acquia.  Deploy at your convenience.

2. Use Acquia’s Dev/Stage for integration testing:
  - Sync latest DB to dev environment
  - Run visual regression tools like Backstop.js
  - Ensure all tests pass before merging.

Possible technical recommendation: Integrate Nightwatch tests or Cypress for critical path UX flows (e.g., login, form submission) and ensure they run pre-deploy.
