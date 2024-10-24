<?php

require '../../main.inc.php';
require_once DOL_DOCUMENT_ROOT.'/custom/mymodule/class/mymodule.class.php';

llxHeader('', 'My Module Page');

print_fiche_titre("My Module Page");

$module = new MyModuleClass($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = GETPOST('id');
    $reason_rejection = GETPOST('reason_rejection', 'alpha');
    $result = $module->rejectCheck($id, $reason_rejection);
    if ($result) {
        setEventMessages("Cheque rejected with reason", null);
    } else {
        setEventMessages("Failed to reject cheque", null, 'errors');
    }
}

print '<form method="POST" action="'.$_SERVER["PHP_SELF"].'">';
print 'Enter your reason for cheque rejection: ';
print '<input type="text" name="reason_rejection">';
print '<input type="hidden" name="id" value="1">';  // replace with dynamic ID if needed
print '<input type="submit" value="Submit">';
print '</form>';

llxFooter();
