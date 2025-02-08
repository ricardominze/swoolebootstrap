<?php

declare(strict_types=1);

function UpdateAutoloadPaths(string $directory, callable $blacklist): void
{

  $autoload = array();
  $dirsIterator = new RecursiveTreeIterator(new RecursiveDirectoryIterator($directory));

  foreach ($dirsIterator as $dir => $dirTree) {

    if (is_dir($dir) && $blacklist($dir)) {
      $d = str_replace('\\', '/', str_replace($directory, '', $dir));
      $d = explode('/', $d);
      array_shift($d);

      $n = array_map(function ($i) {
        return ucfirst($i);
      }, $d);
      $autoload[implode('\\', $n) . '\\'] = implode('/', $d) . '/';
    }
  }

  $json = file_get_contents($directory . DIRECTORY_SEPARATOR . 'composer.json');
  $array = json_decode($json, true);

  $fp = fopen('composer.json', 'w+');
  ksort($autoload);
  $array['autoload']['psr-4'] = $autoload;

  fwrite($fp, json_encode($array, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
  fclose($fp);
}