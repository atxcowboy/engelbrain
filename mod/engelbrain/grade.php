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
 * Handles grading for mod_engelbrain.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/locallib.php');
require_once($CFG->dirroot . '/lib/formslib.php');

// Course module id.
$id = required_param('id', PARAM_INT);
// Submission id.
$sid = required_param('sid', PARAM_INT);

// Get the course module.
$cm = get_coursemodule_from_id('engelbrain', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$engelbrain = $DB->get_record('engelbrain', array('id' => $cm->instance), '*', MUST_EXIST);

// Check if the user has access to the activity.
require_login($course, true, $cm);

// Set up the page.
$PAGE->set_url('/mod/engelbrain/grade.php', array('id' => $cm->id, 'sid' => $sid));
$PAGE->set_title('Bewertung - ' . format_string($engelbrain->name));
$PAGE->set_heading(format_string($course->fullname));
$context = context_module::instance($cm->id);
$PAGE->set_context($context);

// Check if the user has grading capability.
require_capability('mod/engelbrain:grade', $context);

// Get the submission.
$submission = $DB->get_record('engelbrain_submissions', array('id' => $sid, 'engelbrainid' => $engelbrain->id), '*', MUST_EXIST);
$student = $DB->get_record('user', array('id' => $submission->userid), '*', MUST_EXIST);

// Define the grading form.
class engelbrain_grading_form extends moodleform {
    protected function definition() {
        $mform = $this->_form;
        $submission = $this->_customdata['submission'];
        
        // Add a hidden field for the submission id.
        $mform->addElement('hidden', 'sid', $submission->id);
        $mform->setType('sid', PARAM_INT);
        
        // Add a hidden field for the course module id.
        $mform->addElement('hidden', 'id', $this->_customdata['cmid']);
        $mform->setType('id', PARAM_INT);
        
        // Add a field for the grade.
        $mform->addElement('text', 'grade', 'Bewertung (von 0 bis 100)', array('size' => 5));
        $mform->setType('grade', PARAM_INT);
        $mform->addRule('grade', 'Bitte geben Sie eine Zahl ein', 'numeric', null, 'client');
        $mform->addRule('grade', 'Bitte geben Sie einen Wert zwischen 0 und 100 ein', 'range', array(0, 100), 'client');
        if (isset($submission->grade)) {
            $mform->setDefault('grade', $submission->grade);
        }
        
        // Add a field for the feedback.
        $mform->addElement('textarea', 'feedback', 'Feedback', array('rows' => 10, 'cols' => 50));
        $mform->setType('feedback', PARAM_TEXT);
        if (isset($submission->feedback)) {
            $mform->setDefault('feedback', $submission->feedback);
        }
        
        // Add the submit buttons.
        $buttonarray = array();
        $buttonarray[] = $mform->createElement('submit', 'save', 'Speichern');
        $buttonarray[] = $mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
}

// Create the grading form.
$grading_form = new engelbrain_grading_form(null, array(
    'submission' => $submission,
    'cmid' => $cm->id
));

// Handle form submission.
if ($data = $grading_form->get_data()) {
    // Update the submission with the grade and feedback.
    $submission->grade = $data->grade;
    $submission->feedback = $data->feedback;
    $submission->status = 'graded';
    $submission->timemodified = time();
    
    // Save the changes to the database.
    $DB->update_record('engelbrain_submissions', $submission);
    
    // Redirect back to the view page.
    redirect(
        new moodle_url('/mod/engelbrain/view.php', array('id' => $cm->id)),
        'Die Bewertung wurde gespeichert.',
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
} else if ($grading_form->is_cancelled()) {
    // Redirect back to the view page if cancelled.
    redirect(new moodle_url('/mod/engelbrain/view.php', array('id' => $cm->id)));
}

// Output starts here.
echo $OUTPUT->header();
echo $OUTPUT->heading('Einreichung bewerten');

// Display student information.
echo html_writer::tag('p', 'Student: ' . fullname($student));
echo html_writer::tag('p', 'Eingereicht am: ' . userdate($submission->timecreated));

// Display the submission content.
echo html_writer::tag('h4', 'Einreichungsinhalt');
if (!empty($submission->submission_content)) {
    echo html_writer::tag('div', format_text($submission->submission_content, FORMAT_HTML), array('class' => 'submission-content'));
} else {
    echo html_writer::tag('p', 'Keine Inhalte vorhanden.');
}

// Display the grading form.
echo html_writer::tag('h4', 'Bewertung und Feedback');
$grading_form->display();

echo $OUTPUT->footer(); 