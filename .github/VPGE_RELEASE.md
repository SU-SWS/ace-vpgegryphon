## VPGE Release Process – Tag, Deploy, Release

1. Preparation: Checkout & Compare

   Checkout the latest development branch:

git checkout dev && git fetch && git pull

Compare dev to main to see what changes are included:

    git log ^main dev

    Use this log to draft changelog/release notes.

2. Determine Version Number

   Check CHANGELOG.md or previous release tags to determine the next version.

   Examples:

        1.0.0

        1.1.0-beta1

3. Create Release Branch

   Start from dev:

git checkout dev

Create a release branch:

    git checkout -b release-<VERSION>
    # e.g. git checkout -b release-1.0.0

4. Update Version References (if needed)

   Update any hardcoded version strings (module info.yml files, README.md, etc.).

   Confirm with past PRs if/what to update.

5. Update CHANGELOG

   Add a new section with:

        Version number

        Release date

        Bullet points of changes

        Security or dependency notes, if applicable

6. Commit the Release Changes

   Make one single commit for the release prep:

   git commit -am "1.0.0"

7. Compare Composer Lock Files

   Install composer-lock-diff globally if not already:

composer global require io-digital/composer-lock-diff

Compare:

`git checkout <previous-release-tag>`
`git checkout release-<VERSION> -- composer.lock`
`composer-lock-diff --md`

Save the markdown output for your PR and GitHub release notes.

Clean up your local repo afterward:

    git reset --hard
    git clean -fd

8. Push and Open a Pull Request

   Push your release branch:

   git push origin release-<VERSION>

   Open a PR:

        Title: # <VERSION>

        Base: main

        Summary: Include changelog notes and composer diff

9. Merge Release PR

   Temporarily enable merge commits in GitHub settings.

   Merge the release PR into main using a merge commit.

   Disable merge commits again afterward.

10. Back-merge to dev

    Merge the changes from main back into dev:

`git checkout main && git pull`
`git checkout dev && git pull`
`git merge main`

Reset composer to dev mode (if applicable):

    composer require su-sws/vpge_profile:dev -W
    composer update -W
    git commit -am "Back to dev"
    git push

11. Create a GitHub Release

    Go to Releases > "Draft new release"

    Tag: 1.0.0 (new tag)

    Target: main

    Title: Version number

    Description: Paste changelog and composer-lock-diff summary

    Mark as pre-release if necessary

    Click Publish release

12. Deploy Artifact to Acquia

    Checkout the release tag:

git fetch --all
git checkout <tag>

Run deployment:

    blt deploy

    When prompted:

        Create a tag? Yes

        Commit msg: leave blank

        Tag name: <date>_<version>
        E.g., 2025-04-10_1.0.0

13. Deploy in Acquia Cloud UI

    Open the Acquia Cloud Dashboard for VPGE.

    Click the "Code" icon next to each environment.

    Choose the tag you just created and click Deploy.

    Acquia will handle DB updates/config imports automatically.
