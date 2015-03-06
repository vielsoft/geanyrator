<?php
/**
 * @author
 *   Gerald Villorente, X-Team
 *
 * @description
 *   A PHP snippet to scan a given project (directory) recursively to generate
 *   tags for Geany's auto-complete functionality.
 *
 * @param
 *   Target project directory.
 *
 * @usage
 *   php geanyrator.php -d /path/to/drupal/module -e module -e inc -e php > /path/to/geany/tags/dir/project.php.tags
 *
 *   Ex: php geanyrator.php -d /var/www/drupal/sites/all/modules/contrib/views/ -e module -e inc > ~/.config/geany/tags/views.php.tags
 */

// Get the user options.
$opt = getopt("d:e:");
// Flag to determine if the file is whitelisted.
$flag = FALSE;
// Flag to determine if the operation is successful.
$success = FALSE;
if (isset($opt['d']) && is_dir($opt['d']) && isset($opt['e'])) {
  $scandir = new RecursiveDirectoryIterator($opt['d']);
  $tags = NULL;
  // Make sure that extensions is array.
  (is_array($opt['e'])) ? $extensions = $opt['e'] : $extensions = array($opt['e']);
  foreach (new RecursiveIteratorIterator($scandir) as $file) {
    if (!is_dir($file)) {
      if (in_array(strtolower(array_pop(explode('.', $file))), $extensions)) {
        $success = TRUE;
        $lines = file($file);
        foreach ($lines as $line) {
          if (preg_match_all('[function (.*)\((.*)\) ]U', $line, $matches)) {
            $tags .= $matches[1][0] . '||' . $matches[2][0] . "|\n";
          }
        }
      }
      else {
        $flag = TRUE;
      }
    }
  }

  if ($flag && !$success) {
    print "Can't find the specified extensions.\n";
  }
  else {
    $contents = "# format=pipe\n";
    $contents .= $tags;
    print $contents;
  }
}
else {
  print "Make sure that the directory is existing or the arguments you passed are correct.\n";
}
