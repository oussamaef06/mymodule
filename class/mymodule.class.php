<?php

class MyModuleClass
{
    private $db;

    function __construct($db)
    {
        $this->db = $db;
    }

    function rejectCheck($id, $reason_rejection)
    {
        // Logic to reject the cheque and save the reason
        $sql = "UPDATE llx_paiement SET reason_rejet_cheque = '" . $this->db->escape($reason_rejection) . "' WHERE rowid = " . (int) $id;
        $res = $this->db->query($sql);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }
}
