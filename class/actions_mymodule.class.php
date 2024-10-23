<?php

// Ensure Dolibarr environment is loaded
include_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';

class ActionsMymodule
{
    /**
     * Hook that adds a reason for cheque rejection to the payment/cheque/card.php.
     * 
     * @param array $parameters Hook parameters
     * @param object $object The object being operated on
     * @param string $action The action being performed
     * @param HookManager $hookmanager Hook manager instance
     * 
     * @return void
     */
    function formObjectOptions($parameters, &$object, &$action, $hookmanager)
    {
        global $langs, $db, $conf;

        // Check if we are on the cheque card page
        if ($parameters['context'] == 'paymentchequecard' && !empty($object->id)) {

            // Load the existing cheque object
            $sql = "SELECT reason_rejet_cheque FROM " . MAIN_DB_PREFIX . "paiement WHERE rowid = " . (int) $object->id;
            $resql = $db->query($sql);

            if ($resql) {
                $objp = $db->fetch_object($resql);

                // Add the field to the form
                print '<tr><td>'.$langs->trans("ReasonForRejection").'</td>';
                print '<td>';
                if ($action == 'edit') {
                    // In edit mode, display input field
                    print '<input type="text" name="reason_rejet_cheque" value="'.dol_escape_htmltag($objp->reason_rejet_cheque).'">';
                } else {
                    // Display the value in non-edit mode
                    print dol_escape_htmltag($objp->reason_rejet_cheque);
                }
                print '</td></tr>';
            }
        }

        return 0;
    }

    /**
     * Hook that saves the reason for cheque rejection when the form is submitted.
     * 
     * @param array $parameters Hook parameters
     * @param object $object The object being operated on
     * @param string $action The action being performed
     * @param HookManager $hookmanager Hook manager instance
     * 
     * @return void
     */
    function beforePDFCreation($parameters, &$object, &$action, $hookmanager)
    {
        global $db;

        if ($parameters['context'] == 'paymentchequecard') {
            // Save reason_rejet_cheque field when form is submitted
            if (GETPOST('action') == 'edit' || GETPOST('action') == 'setref') {
                $reason_rejet_cheque = dol_html_entity_decode(GETPOST('reason_rejet_cheque'), ENT_QUOTES);

                $sql = "UPDATE " . MAIN_DB_PREFIX . "paiement 
                        SET reason_rejet_cheque = '" . $db->escape($reason_rejet_cheque) . "'
                        WHERE rowid = " . (int) $object->id;
                $db->query($sql);
            }
        }

        return 0;
    }
}
