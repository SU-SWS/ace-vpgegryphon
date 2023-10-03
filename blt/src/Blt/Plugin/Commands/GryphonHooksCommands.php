<?php

namespace Gryphon\Blt\Plugin\Commands;

use Acquia\Blt\Robo\BltTasks;
use Acquia\Blt\Robo\Common\EnvironmentDetector;
use Consolidation\AnnotatedCommand\CommandData;
use Drupal\Core\Serialization\Yaml;
use Robo\Contract\VerbosityThresholdInterface;

/**
 * Class GryphonHooksCommands for any pre or post command hooks.
 */
class GryphonHooksCommands extends BltTasks {

  /**
   * After a multisite is created, modify the drush alias with default values.
   *
   * @hook post-command recipes:multisite:init
   */
  public function postMultiSiteInit() {
    $root = $this->getConfigValue('repo.root');
    $multisites = [];

    $default_alias = Yaml::decode(file_get_contents("$root/drush/sites/default.site.yml"));
    $sites = glob("$root/drush/sites/*.site.yml");
    foreach ($sites as $site_file) {
      $alias = Yaml::decode(file_get_contents($site_file));
      preg_match('/sites\/(.*)\.site\.yml/', $site_file, $matches);
      $site_name = $matches[1];

      $multisites[] = $site_name;
      if (count($alias) != count($default_alias)) {
        foreach ($default_alias as $environment => $env_alias) {
          $env_alias['uri'] = "$site_name.sites-pro.stanford.edu";
          $alias[$environment] = $env_alias;
        }
      }

      file_put_contents($site_file, Yaml::encode($alias));
    }

    // Add the site to the multisites in BLT's configuration.
    $root = $this->getConfigValue('repo.root');
    $blt_config = Yaml::decode(file_get_contents("$root/blt/blt.yml"));
    asort($multisites);
    $blt_config['multisites'] = array_unique($multisites);
    file_put_contents("$root/blt/blt.yml", Yaml::encode($blt_config));

    $this->say(sprintf('Remember to create the cron task. Run <info>gryphon:create-cron</info> to create the new cron job.'));
    $create_db = $this->ask('Would you like to create the database on Acquia now? (y/n)');
    if (substr(strtolower($create_db), 0, 1) == 'y') {
      $this->invokeCommand('gryphon:create-database');
    }
  }

}
