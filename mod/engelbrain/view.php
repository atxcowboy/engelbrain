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
 * Displays the engelbrain activity.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/locallib.php');

// Course module id.
$id = required_param('id', PARAM_INT);

// Get the course module.
$cm = get_coursemodule_from_id('engelbrain', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$engelbrain = $DB->get_record('engelbrain', array('id' => $cm->instance), '*', MUST_EXIST);

// Check if the user has access to the activity.
require_login($course, true, $cm);

// Set up the page.
$PAGE->set_url('/mod/engelbrain/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($engelbrain->name));
$PAGE->set_heading(format_string($course->fullname));
$context = context_module::instance($cm->id);
$PAGE->set_context($context);

require_capability('mod/engelbrain:view', $context);

// Trigger the course module viewed event.
$event = \mod_engelbrain\event\course_module_viewed::create(array(
    'objectid' => $engelbrain->id,
    'context' => $context,
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('engelbrain', $engelbrain);
$event->trigger();

// Start output.
echo $OUTPUT->header();

// Display the activity description.
if (!empty($engelbrain->intro)) {
    echo $OUTPUT->box(format_module_intro('engelbrain', $engelbrain, $cm->id), 'generalbox', 'intro');
}

// Show the activity status.
$now = time();
$duedate = $engelbrain->duedate;
$isoverdue = $duedate && $duedate < $now;

if ($isoverdue) {
    echo $OUTPUT->notification(get_string('submissionsclosed', 'mod_engelbrain', userdate($duedate)), 'notifyproblem');
} else if ($duedate) {
    echo $OUTPUT->notification(get_string('submissionsopen', 'mod_engelbrain', userdate($duedate)), 'notifymessage');
}

// Get the current user's submission if it exists.
$submission = $DB->get_record('engelbrain_submissions', array(
    'engelbrainid' => $engelbrain->id,
    'userid' => $USER->id
));

// Display the submission form if the due date hasn't passed or there's no due date.
if (!$isoverdue || has_capability('mod/engelbrain:grade', $context)) {
    // Check if the user has submit capability.
    if (has_capability('mod/engelbrain:submit', $context)) {
        // Display the submission form.
        echo $OUTPUT->box_start('generalbox', 'submission-form');
        echo html_writer::tag('h3', get_string('submitwork', 'mod_engelbrain'));
        
        // Create the submission form.
        $submissionform = new \mod_engelbrain\form\submission_form(new moodle_url('/mod/engelbrain/submit.php'), array(
            'cm' => $cm,
            'engelbrain' => $engelbrain,
            'submission' => $submission
        ));
        
        // Display the form.
        $submissionform->display();
        
        echo $OUTPUT->box_end();
    }
}

// Display the current submission status if it exists.
if ($submission) {
    echo $OUTPUT->box_start('generalbox', 'submission-status');
    
    // Use hardcoded text instead of get_string
    echo html_writer::tag('h3', 'Einreichungsstatus');
    
    // Show the status.
    $submissiondate = userdate($submission->timecreated);
    
    // Use a table for better formatting.
    $table = new html_table();
    // Use hardcoded text for column names
    $table->head = array('Status', 'Einreichungsdatum');
    // Use hardcoded text for status
    $table->data[] = array('Eingereicht', $submissiondate);
    
    echo html_writer::table($table);
    
    // Display the submission content.
    if (!empty($submission->submission_content)) {
        echo html_writer::tag('p', 'Einreichungsinhalt');
        echo html_writer::tag('div', format_text($submission->submission_content, FORMAT_HTML), array('class' => 'submission-content'));
    }
    
    // Display the feedback if it exists and the submission has been graded.
    if ($submission->status == 'graded' && !empty($submission->feedback)) {
        echo html_writer::tag('h4', 'Feedback von klausurenweb.de');
        echo html_writer::tag('div', format_text($submission->feedback, FORMAT_HTML), array('class' => 'feedback'));
    }
    
    echo $OUTPUT->box_end();
}

// Display the grading interface for teachers.
if (has_capability('mod/engelbrain:grade', $context)) {
    echo $OUTPUT->box_start('generalbox', 'grading-interface');
    echo html_writer::tag('h3', 'Bewertungsschnittstelle');
    
    // Get all submissions for this activity.
    $submissions = $DB->get_records('engelbrain_submissions', array('engelbrainid' => $engelbrain->id));
    
    if (empty($submissions)) {
        echo html_writer::tag('p', 'Noch keine Einreichungen');
    } else {
        // Create a table to display the submissions.
        $table = new html_table();
        // Use standard core strings or hardcoded texts
        $table->head = array(
            get_string('fullname'),  // Core Moodle string
            'Einreichungsdatum',
            'Status',
            'Aktionen'
        );
        $table->data = array();
        
        foreach ($submissions as $submission) {
            // Get the user information.
            $user = $DB->get_record('user', array('id' => $submission->userid));
            
            // Create the actions.
            $actions = html_writer::link(
                new moodle_url('/mod/engelbrain/grade.php', array('id' => $cm->id, 'sid' => $submission->id)),
                'Bewerten'  // Hardcoded text
            );
            
            // Add the submission to the table.
            $table->data[] = array(
                fullname($user),
                userdate($submission->timecreated),
                'Eingereicht',  // Hardcoded text
                $actions
            );
        }
        
        // Display the table.
        echo html_writer::table($table);
    }
    
    echo $OUTPUT->box_end();
}

// Finish the page.
echo $OUTPUT->footer(); 