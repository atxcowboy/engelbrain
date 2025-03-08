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
 * The main mod_engelbrain configuration form.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_engelbrain
 * @copyright  2025 Panomity GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_engelbrain_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;
        
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('name'), array('size' => '64'));
        
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Add engelbrain specific settings.
        $mform->addElement('header', 'engelbrainfieldset', get_string('pluginname', 'mod_engelbrain'));

        // Teacher API Key.
        $school_api_key = get_config('mod_engelbrain', 'school_api_key');
        if (empty($school_api_key)) {
            $mform->addElement('static', 'no_school_api_key', 
                get_string('school_api_key', 'mod_engelbrain'),
                get_string('school_api_key_desc', 'mod_engelbrain') . ' ' .
                get_string('no_school_api_key_configured', 'mod_engelbrain')
            );
        }

        $mform->addElement('text', 'teacher_api_key', get_string('teacher_api_key', 'mod_engelbrain'));
        $mform->setType('teacher_api_key', PARAM_TEXT);
        $mform->addHelpButton('teacher_api_key', 'teacher_api_key', 'mod_engelbrain');
        $mform->addRule('teacher_api_key', get_string('required'), 'required', null, 'client');

        // Lerncode.
        $mform->addElement('text', 'lerncode', get_string('lerncode', 'mod_engelbrain'));
        $mform->setType('lerncode', PARAM_TEXT);
        $mform->addHelpButton('lerncode', 'lerncode', 'mod_engelbrain');
        $mform->addRule('lerncode', get_string('required'), 'required', null, 'client');

        // Due date.
        $mform->addElement('date_time_selector', 'duedate', get_string('duedate', 'assignment'),
            array('optional' => true));
        $mform->addHelpButton('duedate', 'duedate', 'assignment');

        // Grade.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }

    /**
     * Validate the form data.
     *
     * @param array $data form data
     * @param array $files files uploaded
     * @return array errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate the lerncode.
        if (!empty($data['lerncode'])) {
            // Create a client to validate the lerncode.
            $apikey = $data['teacher_api_key'];
            
            if (empty($apikey)) {
                // Try to use the school API key if no teacher API key is set.
                $apikey = get_config('mod_engelbrain', 'school_api_key');
            }
            
            if (!empty($apikey)) {
                try {
                    $client = new \mod_engelbrain\api\client($apikey);
                    $response = $client->validate_lerncode($data['lerncode']);
                    
                    if (!$response['valid']) {
                        $errors['lerncode'] = get_string('invalid_lerncode', 'mod_engelbrain', $response['message']);
                    }
                } catch (\Exception $e) {
                    $errors['lerncode'] = get_string('lerncode_validation_error', 'mod_engelbrain', $e->getMessage());
                }
            }
        }

        return $errors;
    }
} 