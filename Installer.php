<?php

namespace Floxim\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

class Installer extends LibraryInstaller
{
   
    protected $types = array(
        'floxim-module' => 'module/{$vendor}/{$name}/',
        'floxim-theme' => 'theme/{$vendor}/{$name}/',
        'floxim-themes' => 'theme/{$name}/'
    );
    
  /**
   * {@inheritDoc}
   */
  public function getInstallPath(PackageInterface $package)
  {
      $type = $package->getType();
      $pattern = $this->types[$type];
      
      $vars = array(
        'type' => $type,
      );
      $prettyName = $package->getPrettyName();
      if (strpos($prettyName, '/') !== FALSE) {
        $pieces = explode('/', $prettyName);;
        $vars['vendor'] = $pieces[0];
        $vars['name'] = $pieces[1];
      } else {
        $vars['vendor'] = '';
        $vars['name'] = $prettyName;
      }
      return $this->templatePath($pattern, $vars);
  }
  /**
   * {@inheritDoc}
   */
  public function supports($packageType)
  {
      return array_key_exists($packageType, $this->types);
      /**
      if ($this->composer->getPackage()) {
        $extra = $this->composer->getPackage()->getExtra();
        if (!empty($extra['custom-installer'])) {
          if (!empty($extra['custom-installer'][$packageType])) {
            return true;
          }
        }
      }
      return false;
      */
  }
  /**
   * Replace vars in a path
   *
   * @see Composer\Installers\BaseInstaller::templatePath()
   *
   * @param  string $path
   * @param  array  $vars
   * @return string
   */
  protected function templatePath($path, array $vars = array())
  {
      if (strpos($path, '{') !== false) {
          extract($vars);
          preg_match_all('@\{\$([A-Za-z0-9_]*)\}@i', $path, $matches);
          if (!empty($matches[1])) {
              foreach ($matches[1] as $var) {
                  $path = str_replace('{$' . $var . '}', $$var, $path);
              }
          }
      }
      return $path;
  }
}