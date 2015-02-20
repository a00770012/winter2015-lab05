<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Admin extends Application {

    function __construct() {
        parent::__construct();

        // load the helper to display the form
        $this->load->helper('formfields');
    }

    function index() {
        // use new admin_list to render the page
        $this->data['title'] = 'Quotations Maintenance';
        $this->data['quotes'] = $this->quotes->all();
        $this->data['pagebody'] = 'admin_list';
        $this->render();
    }

    // Present a quotation for adding/editing
    function present($quote) {
        // disable the ID# field from being edited.
        $this->data['fid'] = makeTextField('ID#', 'id', $quote->id, "Unique quote identifier, system-assigned", 10, 10, true);

        $this->data['fwho'] = makeTextField('Author', 'who', $quote->who);
        $this->data['fmug'] = makeTextField('Picture', 'mug', $quote->mug);
        $this->data['fwhat'] = makeTextArea('The Quote', 'what', $quote->what);
        $this->data['pagebody'] = 'quote_edit';

        // added the button with the formfileds helper
        $this->data['fsubmit'] = makeSubmitButton('Process Quote', "Click here to validate the quotation data", 'btn-success');
        $this->render();
    }

    // Add a new quotation
    function add() {
        $quote = $this->quotes->create();
        $this->present($quote);
    }

    // process a quotation edit
    function confirm() {
        $record = $this->quotes->create();
        // Extract submitted fields
        $record->id = $this->input->post('id');
        $record->who = $this->input->post('who');
        $record->mug = $this->input->post('mug');
        $record->what = $this->input->post('what');

        // validation
        if (empty($record->who))
            $this->errors[] = 'You must specify an author.';
        if (strlen($record->what) < 20)
            $this->errors[] = 'A quotation must be at least 20 characters long.';

        
        // redisplay if any errors
        if (count($this->errors) > 0) {
            $this->present($record);
            return; // make sure we don't try to save anything
        }

        // Save stuff
        if (empty($record->id))
            $this->quotes->add($record);
        else
            $this->quotes->update($record);
        redirect('/admin');
    }

}
