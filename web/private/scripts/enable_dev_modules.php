<?php
/**
 * This example enables the devel module when a database is cloned to a dev environment.
 *
 * This script should be configured into the clone_database operation in pantheon.yml
 */

echo "Now enabling development modules...";

$devel_modules = array('devel', 'field_ui', 'views_ui', 'stage_file_proxy', 'bean_admin_ui', 'context_ui', 'maillog', 'og_ui', 'spaces_ui', 'views_ui');

// The clone_database may be triggered on any environment, but we only want
// to automatically enable the devel module when this event happens a dev
// or multidev environment.
if (isset($_POST['environment']) && !in_array($_POST['environment'], array('live'))) {
  // First, let's retrieve a list of disabled modules with drush pm-list.
  // shell_exec() will return the output of an executable as a string.
  // Pass the --format=json flag into the drush command so the output can be converted into an array with json_decode().
  $modules = json_decode(shell_exec('drush pm-list --format=json'));

  // Now let's enable devel if it is installed and not already enabled.
  foreach ($devel_modules as $module) {
    if (isset($modules->$module) && $modules->$module->status !== 'Enabled') {
      // This time let's just passthru() to run the drush command so the command output prints to the workflow log.
      passthru('drush pm-enable -y ' . escapeshellarg($module));
    }
  }
}

echo "Development modules are enabled.";
