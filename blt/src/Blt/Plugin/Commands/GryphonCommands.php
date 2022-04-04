<?php

namespace Gryphon\Blt\Plugin\Commands;

use Acquia\Blt\Robo\BltTasks;

/**
 * Class GryphonCommands.
 */
class GryphonCommands extends BltTasks {

  /**
   * Enable a list of modules for all sites on a given environment.
   *
   * @param string $environment
   *   Environment name like `dev`, `test`, or `ode123`.
   * @param string $modules
   *   Comma separated list of modules to enable.
   *
   * @example blt gryphon:enable-modules dev views_ui,field
   *
   * @command gryphon:enable-modules
   * @aliases grem
   *
   */
  public function enableModules($environment, $modules) {
    $commands = $this->collectionBuilder();
    foreach ($this->getConfigValue('multisites') as $site) {
      $commands->addTask($this->taskDrush()
        ->alias("$site.$environment")
        ->drush('en ' . $modules));
    }

    $commands->run();
  }

  /**
   * Uses BLT to build an artifact from the current D8 directory
   *
   * @command     devops:build-artifact
   * @description Uses BLT to build an artifact from the current D8 directory
   */
  public function buildArtifact() {
      $tag_date = date('Y-m-d');
      $tag = $this->ask('What tag would you like to use for this artifact?');
      $commit_message = $this->ask('What should we put in the commit message?');
      $this->taskExec('vendor/bin/blt artifact:deploy --commit-msg "' . $commit_message . '" --tag "' . $tag . '"')->run();
  }

  /**
   * Uses Git to pull upstream updates from the ace-gryphon repo.
   * git pull https://github.com/SU-SWS/ace-gryphon.git 2.x -X ours --no-edit
   *
   * @command devops:pull-upstream
   * @description Uses Git to pull upstream updates from the ace-gryphon repo.
   */
  public function pullUpstream() {
    $this->taskGitStack()
      ->stopOnFail()
      ->pull('https://github.com/SU-SWS/ace-gryphon.git', '2.x')
      ->rawArg('-X ours --no-edit')
      ->run();
  }

}
