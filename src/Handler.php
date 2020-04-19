<?php

namespace Metadrop;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\CommandEvent;

/**
 * Core class of the plugin.
 *
 * Contains the primary logic which determines the files to be fetched and
 * processed.
 *
 * @internal
 */
class Handler {

  const DIR = './scripts';

  const TARGET_DIR = '../vendor/metadrop/scripthor/bin/';

  const SIMLINK_FILES = [
    'frontend-build.sh',
    'copy-content-config-entity-to-module.sh',
    'reload-local.sh',
  ];

  /**
   * The Composer service.
   *
   * @var \Composer\Composer
   */
  protected $composer;

  /**
   * Composer's I/O service.
   *
   * @var \Composer\IO\IOInterface
   */
  protected $io;

  /**
   * Handler constructor.
   *
   * @param \Composer\Composer $composer
   *   The Composer service.
   * @param \Composer\IO\IOInterface $io
   *   The Composer I/O service.
   */
  public function __construct(Composer $composer, IOInterface $io) {
    $this->composer = $composer;
    $this->io = $io;
  }

  /**
   * Create simlinks.
   *
   * @throws \Exception
   *   Error when not created
   */
  public function createSymlinks() {
    $this->io->write('Scripthor start.');

    if ($this->createScriptDir()) {
      $this->createScriptLink();
    }
    else {
      $this->io->writeError('./scripts directory not created.');
      throw new \Exception('./scripts directory not created.');
    }
    $this->io->write('Scripthor finished.');
  }

  /**
   * Remove simlinks.
   */
  public function removeSymlinks() {
    $this->io->write('Scripthor remove start.');
    $this->io->write('Scripthor remove finished.');
  }

  /**
   * Create script directory.
   *
   * @return bool
   *   Exist or not directory
   */
  protected function createScriptDir() {
    if (!is_dir(self::DIR)) {
      $this->io->write('./scripts directory created with 755 permissions.');
      mkdir(self::DIR, 0755);
    }

    if (is_dir(self::DIR)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Create script symbolic links.
   *
   * @return bool
   *   Exist or not directory
   */
  protected function createScriptLink() {
    $link = self::DIR . '/';

    foreach (self::SIMLINK_FILES as $file) {
      $script = self::DIR . '/' . $file;
      if (!file_exists($script)) {
        symlink(self::TARGET_DIR . $file, $script);
        $this->io->write('Script created: ' . $file);
      }
      else {
        $this->io->write('Script exists: ' . $file);
      }
    }
  }
}
