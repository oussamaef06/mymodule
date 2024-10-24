<?php
include_once DOL_DOCUMENT_ROOT . '/core/modules/DolibarrModules.class.php';

class modMymodule extends DolibarrModules
{
    function __construct($db)
    {
        global $langs;
        $this->db = $db;

        $this->numero = 104000; 
        $this->rights_class = 'mymodule';
        $this->family = "financial";
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "Module for cheque rejection with reason.";
        $this->version = '1.0';
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        $this->picto='mymodule@mymodule';
        $this->module_parts = array();

        // Data directories to create when module is enabled
        $this->dirs = array();

        // Config pages
        $this->config_page_url = array("mymodule_setup.php@multicompany");

        $this->langfiles = array("mymodule@mymodule");

        // Dependencies
        $this->depends = array();
        $this->requiredby = array();
        $this->phpmin = array(5, 3);
        $this->need_dolibarr_version = array(3, 0);

        // Constants
        $this->const = array();

        // Define boxes
        $this->boxes = array();

        // Define rights
        $this->rights = array();
        $r = 0;
        $this->rights[$r][0] = 104001;
        $this->rights[$r][1] = 'Read mymodule';
        $this->rights[$r][2] = 'r';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'read';
        $r++;

        // Define menus
        $this->menu = array();
        $r = 0;

        // Main menu entry
        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=compta',
            'type' => 'top',
            'titre' => 'My Module',
            'mainmenu' => 'mymodule',
            'leftmenu' => 'mymodule_top',
            'url' => '/custom/mymodule/page.php',
            'langs' => 'mymodule@mymodule',
            'position' => 50010, // Ensure unique value
            'enabled' => '$conf->mymodule->enabled',
            'perms' => '1',
            'target' => '',
            'user' => 2
        );
        $r++;

        // Submenu entry
        $this->menu[$r] = array(
            'fk_menu' => 'fk_mainmenu=mymodule',
            'type' => 'left',
            'titre' => 'Cheque Rejection',
            'mainmenu' => 'mymodule',
            'leftmenu' => 'mymodule_rejection',
            'url' => '/custom/mymodule/subpage.php',
            'langs' => 'mymodule@mymodule',
            'position' => 50011, // Ensure unique value
            'enabled' => '$conf->mymodule->enabled',
            'perms' => '1',
            'target' => '',
            'user' => 2
        );
    }

    function applyChangesToCard()
    {
        $logFile = DOL_DOCUMENT_ROOT . '/custom/mymodule/debug.log';
        file_put_contents($logFile, "applyChangesToCard() called\n", FILE_APPEND);
    
        $file = DOL_DOCUMENT_ROOT . '/compta/paiement/cheque/card.php';
        $backup = $file . '.bak';
    
        if (file_exists($file)) {
            copy($file, $backup);
            file_put_contents($logFile, "Backup created\n", FILE_APPEND);
    
            $contents = file_get_contents($file);
    
            // Insert confirm_reject_check block
            // Replace reject_check block
        if (strpos($contents, "if (\$action == 'reject_check') {") !== false) {
            $contents = str_replace(
                "if (\$action == 'reject_check') {    \$formquestion = array(        array('type' => 'hidden', 'name' => 'bankid', 'value' => GETPOSTINT('lineid')),        array('type' => 'date', 'name' => 'rejectdate_', 'label' => \$langs->trans(\"RejectCheckDate\"), 'value' => dol_now()));    print \$form->formconfirm(\$_SERVER[\"PHP_SELF\"].'?id='.\$object->id, \$langs->trans(\"RejectCheck\"), \$langs->trans(\"ConfirmRejectCheck\"), 'confirm_reject_check', \$formquestion, '', 1);}",
                "if (\$action == 'reject_check') {    \$formquestion = array(        array('type' => 'hidden', 'name' => 'bankid', 'value' => GETPOSTINT('lineid')),        array('type' => 'date', 'name' => 'rejectdate_', 'label' => \$langs->trans(\"RejectCheckDate\"), 'value' => dol_now()),        array('type' => 'text', 'name' => 'reason_rejet_cheque', 'label' => \$langs->trans(\"ReasonForRejection\"), 'value' => '')    );    print \$form->formconfirm(\$_SERVER[\"PHP_SELF\"].'?id='.\$object->id, \$langs->trans(\"RejectCheck\"), \$langs->trans(\"ConfirmRejectCheck\"), 'confirm_reject_check', \$formquestion, '', 1);}",
                $contents
            );
            file_put_contents($logFile, "Updated reject_check block\n", FILE_APPEND);
        }
    
            // Insert reject_check confirmation
            if (strpos($contents, "if (\$action == 'reject_check') {") === false) {
                $contents = str_replace(
                    "if (\$action == 'reject_check') {
		\$formquestion = array(
			array('type' => 'hidden', 'name' => 'bankid', 'value' => GETPOSTINT('lineid')),
			array('type' => 'date', 'name' => 'rejectdate_', 'label' => \$langs->trans(\"RejectCheckDate\"), 'value' => dol_now())
		);
		print \$form->formconfirm(\$_SERVER[\"PHP_SELF\"].'?id='.\$object->id, \$langs->trans(\"RejectCheck\"), \$langs->trans(\"ConfirmRejectCheck\"), 'confirm_reject_check', \$formquestion, '', 1);
	}",
                    "
    if (\$action == 'reject_check') {
        \$formquestion = array(
            array('type' => 'hidden', 'name' => 'bankid', 'value' => GETPOSTINT('lineid')),
            array('type' => 'date', 'name' => 'rejectdate_', 'label' => \$langs->trans(\"RejectCheckDate\"), 'value' => dol_now()),
            array('type' => 'text', 'name' => 'reason_rejet_cheque', 'label' => \$langs->trans(\"ReasonForRejection\"), 'value' => '')
        );
        print \$form->formconfirm(\$_SERVER[\"PHP_SELF\"] . '?id=' . \$object->id, \$langs->trans(\"RejectCheck\"), \$langs->trans(\"ConfirmRejectCheck\"), 'confirm_reject_check', \$formquestion, '', 1);
    }",
                    $contents
                );
                file_put_contents($logFile, "Added reject_check confirmation\n", FILE_APPEND);
            }
    
            // Modify SQL query to include reason_rejet_cheque
            if (strpos($contents, "\$sql .= \" p.statut, p.reason_rejet_cheque\";") === false) {
                $contents = str_replace(
                    "\$sql .= \" p.rowid as pid, p.ref as pref, ba.rowid as bid, p.statut\";",
                    "\$sql .= \" p.rowid as pid, p.ref as pref, ba.rowid as bid, p.statut, p.reason_rejet_cheque\";",
                    $contents
                );
                file_put_contents($logFile, "Modified SQL query to include reason_rejet_cheque\n", FILE_APPEND);
            }

            //
            
            //

            file_put_contents($file, $contents);
            file_put_contents($logFile, "File updated\n", FILE_APPEND);
        } else {
            file_put_contents($logFile, "File does not exist\n", FILE_APPEND);
        }
    }
    

    

    function init($options = '')
    {
        $sql = array();
        
        $result = $this->db->query("SHOW COLUMNS FROM llx_paiement LIKE 'reason_rejet_cheque'");
        if ($this->db->num_rows($result) == 0) {
            $sql[] = "ALTER TABLE llx_paiement ADD reason_rejet_cheque TEXT;";
        }

        $res = $this->_init($sql, $options);

        if ($res) {
            $this->applyChangesToCard();
        }

        return $res;
    }

    function remove($options = '')
    {
        $sql = array();
        $sql[] = "ALTER TABLE llx_paiement DROP COLUMN reason_rejet_cheque;";

        $res = $this->_remove($sql, $options);

        // Optionally, restore the backup file
        $file = DOL_DOCUMENT_ROOT . '/compta/paiement/cheque/card.php';
        $backup = $file . '.bak';

        if (file_exists($backup)) {
            copy($backup, $file);
            unlink($backup);
        }

        return $res;
    }
}
