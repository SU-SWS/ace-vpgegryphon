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
4.

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

## 5. Push code to intiate a code review.
Push, Review, and Test in CI/Remote
Steps

Commit your changes, including:

        Composer updates

        Config exports (drush cex -y)
        Test-related YAML updates
Push to GitHub and open a PR for review.

Use Acquia’s Dev/Stage for integration testing:

        Sync latest DB to dev environment

        Run visual regression tools like Backstop.js
Ensure all tests pass before merging.

Technical recommendation: Integrate Nightwatch tests or Cypress for critical path UX flows (e.g., login, form submission) and ensure they run pre-deploy.
