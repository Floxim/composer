<?php

namespace Floxim\Composer;

use Composer\Installer\LibraryInstaller;
use Composer\Package\PackageInterface;

class Installer extends LibraryInstaller
{
   
    protected $types = array(
        'floxim-module',
        'floxim-modules',
        'floxim-theme',
        'floxim-themes'
    );
    
    public function nameToPath($type, $name)
    {
        $short_type = preg_replace("~^floxim-~", '', $type);
        if (strpos($name, "/") === false) {
            $name = "/".$name;
        }
        
        list($vendor, $name) = explode("/", $name);
        $vendor = self::camelize($vendor);
        $name = self::camelize($name);
        $name = preg_replace("~^Floxim~", '', $name);
        $name = preg_replace("~^Modules?|Themes?~", '', $name);
        switch ($short_type) {
            case 'themes':
                $res = 'theme/'.$vendor.$name;
                break;
            case 'modules':
                $res = 'module/'.$vendor.$name;
                break;
            default:
                $res = $short_type.'/'.$vendor.'/'.$name;
                break;
        }
        return $res;
    }
        
    public function getInstallPath(PackageInterface $package) 
    {
        $type = $package->getType();
        $name = $package->getPrettyName();
        return $this->nameToPath($type, $name);   
    }
  
    protected static function camelize($string, $first_upper = true)
    {
        $t_string = trim($string, '_');
        $parts = preg_split('~[_-]~', $t_string);
        $camelized = '';
        foreach ($parts as $part_num => $part) {
            if ($part_num === 0 && $first_upper === false) {
                $camelized .= $part;
            } elseif ($part === '') {
                $camelized .= '_';
            } else {
                $camelized .= ucfirst($part);
            }
        }
        return $camelized;
    }
  
    public function supports($packageType)
    {
        return in_array($packageType, $this->types);
    }
}