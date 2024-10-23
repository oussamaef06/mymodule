<?php

include_once DOL_DOCUMENT_ROOT .'/core/modules/DolibarrModules.class.php';

class modMyModule extends DolibarrModules
{
    function __construct($db)
    {
        global $langs, $conf;

        $this->db = $db;

        // Module name
        $this->numero = 104000; // You need to get a free number
        $this->rights_class = 'mymodule';

        $this->family = "other";
        $this->module_position = 500;
        $this->name = preg_replace('/^mod/i','',get_class($this));
        $this->description = "Module to add cheque rejection reason";

        $this->version = '1.0';

        $this->const = array();

        $this->rights = array(); // Define permissions
        $this->menus = array();  // Define menus

        // Dependences
        $this->depends = array();  // List of modules that must be enabled
        $this->requiredby = array();  // List of modules that require this module
    }

    /**
     * Function called when module is enabled.
     * It creates tables, keys, etc. required for the module.
     *
     * @param string $options Options when enabling the module ('', 'noboxes', 'nomenus')
     * @return int 1 if OK, 0 if KO
     */
    function init($options = '')
    {
        $sql = array();
        
        // Add the new column to the database
        $sql[] = "ALTER TABLE llx_paiement ADD reason_rejet_cheque TEXT;";
        
        // Run SQL commands
        return $this->_load_tables('/mymodule/sql/');
    }

    /**
     * Function called when module is disabled.
     * It removes tables, keys, etc. from the database.
     *
     * @param string $options Options when disabling the module ('', 'noboxes', 'nomenus')
     * @return int 1 if OK, 0 if KO
     */
    function remove($options = '')
    {
        $sql = array();

        // You can add code here to remove any changes you made to the database during installation.

        return $this->_remove_tables('/mymodule/sql/');
    }

    class modMymodule extends DolibarrModules
{
    // Module constructor
    public function __construct($db)
    {
        $this->db = $db;
        $this->numero = 104000;  // Make sure to choose a unique module number

        $this->rights_class = 'mymodule';
        $this->family = "mymodule";  // Your module family

        // Hooks to register
        $this->module_parts = array(
            'hooks' => array(
                'paymentchequecard'   // This is the context for the card.php file in the cheque module
            ),
        );
    }
}

}
