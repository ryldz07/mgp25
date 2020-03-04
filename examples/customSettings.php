<?php

set_time_limit(0);
date_default_timezone_set('UTC');

require __DIR__.'/../vendor/autoload.php';

/////// CONFIG ///////
$debug = true;
$truncatedDebug = false;
//////////////////////

/*
 * This example demonstrates how to use and customize user settings storages.
 *
 * By default, if you don't give us any custom configuration, we will always use
 * the "File" storage backend, which keeps all data in regular files on disk. It
 * is a rock-solid backend and will be very good for most people.
 *
 * However, other people may want to use something more advanced, such as one of
 * the other built-in storage backends ("MySQL", "SQLite" and "Memcached"). Or
 * perhaps you'd even like to build your own backend (doing so is very easy).
 */

echo "You are not supposed to execute this script. Read it in a text editor to see various storage methods.\n";
exit;

// These points will give you a basic overview of the process. But you should
// read the code in src/Settings/ for the full details. It is well documented.

// 1. Choosing a built-in storage backend (one of "file", "mysql", "sqlite" or
// "memcached"), and using the automatic, default settings for that backend:
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug, [
    'storage' => 'mysql',
]);

// 2. You can read src/Settings/Factory.php for valid settings for each backend.
// Here's an example of how to change the default storage location for "file":
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug, [
    'storage'    => 'file',
    'basefolder' => 'some/path/',
]);

// 3. And here's an example of how to change the default database filename and
// the default database table name for the "sqlite" backend:
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug, [
    'storage'     => 'sqlite',
    'dbfilename'  => 'some/path/foo.db',
    'dbtablename' => 'mysettings',
]);

// 4. If you read src/Settings/Factory.php, you'll notice that you can choose
// the storage backends and most of their parameters via the command line or
// environment variables instead. For example: "SETTINGS_STORAGE=mysql php
// yourscript.php" would set the "storage" parameter via the environment, and
// typing "php yourscript.php --settings_storage=mysql" would set it via the
// command line. The command-line arguments have the highest precedence, then
// the environment variables, and lastly the code within your script. This
// precedence order is so that you can easily override your script's code to
// test other backends or change their parameters without modifying your code.

// 5. Very advanced users can look in src/Settings/StorageHandler.php to read
// about hasUser(), moveUser() and deleteUser(). Three very, VERY DANGEROUS
// commands which let you rename or delete account settings in your storage.
// Carefully read through their descriptions and use them wisely. If you're sure
// that you dare to use them, then you can access them via $ig->settings->...

// 6. Yet another super advanced topic is the ability to copy data between
// backends. For example if you want to move all of your data from a File
// backend to a database, or vice versa. That kind of action is not supported
// natively by us, but you CAN do it by directly interfacing with the storages!

// First, you MUST manually build a list of all users you want to migrate.
// You can either hardcode this list. Or get it via something like a directory
// scan (to look at existing folders in a File backend storage path), or a
// database query to get all "username" values from the old database. If you're
// using a database, you will have to connect to it manually and query it
// yourself! There's no way to do it automatically! Just build this array any
// way you want to do it!
$migrateUsers = [
    'someuser',
    'another.user',
    'very_awesome_user123',
];

// Secondly, you must connect to your old and new storages. These are just
// example values. The array format is the exact same as what's given to the
// `Instagram()` constructor! And if you give an empty array, you'll use the
// same default File backend that the main class uses! So if you want to migrate
// from that, you should just set oldStorage to `createHandler([])`!
$oldStorage = \InstagramAPI\Settings\Factory::createHandler([
    'storage'     => 'sqlite',
    'dbfilename'  => 'app/instagram.sqlite',
    'dbtablename' => 'instagram_sessions',
]);
$newStorage = \InstagramAPI\Settings\Factory::createHandler([
    'storage'     => 'file',
    'basefolder'  => 'some/path/',
]);

// Now just run the migration process. This will copy all cookies and settings
// from the old storage to the new storage, for all of the "migrateUsers".
foreach ($migrateUsers as $user) {
    if (!$oldStorage->hasUser($user)) {
        die("Unable to migrate '{$user}' from old storage (user doesn't exist).\n");
    }

    echo "Migrating '{$user}'.\n";

    $oldStorage->setActiveUser($user);
    $newStorage->setActiveUser($user);

    $newStorage->setCookies((string) $oldStorage->getCookies());
    foreach (\InstagramAPI\Settings\StorageHandler::PERSISTENT_KEYS as $key) {
        $newStorage->set($key, (string) $oldStorage->get($key));
    }
}

// 7. Lastly... if you want to implement your own completely CUSTOM STORAGE,
// then you simply have to do one thing: Implement the StorageInterface class
// interface. But be very sure to STRICTLY follow ALL rules for storage backends
// described in that interface's docs, otherwise your custom backend WON'T work.
//
// See the overview in src/Settings/StorageInterface.php, and then read through
// the various built-in storage backends in src/Settings/Storage/ to see perfect
// implementations that completely follow the required interface specification.
//
// Also note that PDO-based backends should be derived from our "PDOStorage"
// storage sub-component, so that the logic is perfectly implemented and not
// duplicated. That's exactly how our "sqlite" and "mysql" PDO backends work!
//
// To use your custom storage backend, you would simply create your own class
// similar to the built-in backends. But do NOT put your own class in our
// src/Settings/Storage/ folder. Store your class inside your own project.
//
// Then simply provide your custom storage class instance as the storage class:
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug, [
    'storage' => 'custom',
    'class'   => new MyCustomStorage(), // Whatever you've named your class.
]);

// That's it! This should get you started on your journey. :-)

// And please think about contributing your WELL-WRITTEN storage backends to
// this project! If you had a reason to write your own, then there's probably
// someone else out there with the same need. Remember to SHARE with the open
// source community! ;-)
