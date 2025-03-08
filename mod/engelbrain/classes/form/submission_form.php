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
 * The submission form for mod_engelbrain.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_engelbrain\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * The submission form for mod_engelbrain.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submission_form extends \moodleform {
    /**
     * Define the form elements.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;
        $customdata = $this->_customdata;
        
        // Get the activity and submission data.
        $cmid = $customdata['id'];
        $engelbrain = $customdata['engelbrain'];
        $submission = $customdata['submission'] ?? null;
        
        // Add a hidden field for the course module ID.
        $mform->addElement('hidden', 'id', $cmid);
        $mform->setType('id', PARAM_INT);
        
        // Add a text editor for the submission content.
        $mform->addElement('editor', 'submission_content', get_string('submissioncontent', 'mod_engelbrain'), array('rows' => 10), array(
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'noclean' => true,
            'context' => context_module::instance($cmid),
            'subdirs' => true
        ));
        $mform->setType('submission_content', PARAM_RAW);
        $mform->addRule('submission_content', get_string('required'), 'required', null, 'client');
        
        // If there's an existing submission, set the default value.
        if ($submission && !empty($submission->submission_content)) {
            $mform->setDefault('submission_content', array(
                'text' => $submission->submission_content,
                'format' => FORMAT_HTML
            ));
        }
        
        // Add file upload for the submission.
        $mform->addElement('filemanager', 'submission_files', get_string('submissionfiles', 'mod_engelbrain'), null, array(
            'subdirs' => 0,
            'maxbytes' => get_config('mod_engelbrain', 'maxbytes') ?? $CFG->maxbytes,
            'maxfiles' => 10,
            'accepted_types' => array('.pdf', '.doc', '.docx', '.txt')
        ));
        
        // Add the submission button.
        $submitlabel = $submission ? get_string('updatesubmission', 'mod_engelbrain') : get_string('savesubmission', 'mod_engelbrain');
        $mform->addElement('submit', 'submitbutton', $submitlabel);
    }

    /**
     * Validate the form data.
     *
     * @param array $data The form data
     * @param array $files The form files
     * @return array The errors array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Make sure the submission is not empty.
        if (empty($data['submission_content']['text'])) {
            $errors['submission_content'] = get_string('required');
        }

        return $errors;
    }
} 