<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The form for submitting work to engelbrain.
 *
 * @package    mod_engelbrain
 * @copyright  2025 Panomity GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_engelbrain\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/mod/engelbrain/locallib.php');

use moodleform;
use context_module;

/**
 * The form for submitting work to engelbrain.
 *
 * @package    mod_engelbrain
 * @copyright  2025 Panomity GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_form extends moodleform {

    /**
     * Define the form.
     */
    public function definition() {
        global $CFG;
        
        $mform = $this->_form;
        $engelbrain = $this->_customdata['engelbrain'];
        $cm = $this->_customdata['cm'];
        $context = context_module::instance($cm->id);
        
        // Add the submission content field.
        $mform->addElement('editor', 'submission_content', get_string('submissioncontent', 'mod_engelbrain'), 
            array('rows' => 15), array('maxfiles' => EDITOR_UNLIMITED_FILES, 
            'noclean' => true, 'context' => $context, 'subdirs' => true));
        $mform->setType('submission_content', PARAM_RAW);
        $mform->addRule('submission_content', get_string('required'), 'required', null, 'client');
        
        // Add the submission buttons.
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('submitwork', 'mod_engelbrain'));
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
    
    /**
     * Validate the form data.
     *
     * @param array $data form data
     * @param array $files form files
     * @return array errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        // Add your custom validation here if needed.
        
        return $errors;
    }
} 