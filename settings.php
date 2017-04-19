<?php

$ADMIN->add('root', new admin_category('tweaks', 'Custom urls'));
$ADMIN->add('tweaks', new admin_externalpage('customurls', 'Manage urls',
$CFG->wwwroot.'/local/customurls/edit.php','local/customurls:managecustomurls'));
