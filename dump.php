<?php

require_once "src/infra/util/UpdateAutoloadPaths.php";

UpdateAutoloadPaths(__DIR__,  function($dir):bool{
  return (!strstr($dir, '.') && !strstr($dir, '..') && !strstr($dir, 'git') && !strstr($dir, 'test') && !strstr($dir, 'vendor') && !strstr($dir, 'assets'));
});