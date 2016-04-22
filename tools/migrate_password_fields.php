<?php
require dirname(__FILE__) . '/inc/cli.php';

echo "Upgrading password fields lengths";

query('ALTER TABLE ``mods`` CHANGE `password` `password` VARCHAR(255) NOT NULL;') or error(db_error());
query('ALTER TABLE ``mods`` CHANGE `salt` `salt` VARCHAR(64) NOT NULL;') or error(db_error());

echo ". Done!\n";
