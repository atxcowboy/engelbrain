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
 * Handles submission processing for mod_engelbrain.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

// Course module id.
$id = required_param('id', PARAM_INT);

// Get the course module.
$cm = get_coursemodule_from_id('engelbrain', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$engelbrain = $DB->get_record('engelbrain', array('id' => $cm->instance), '*', MUST_EXIST);

// Check if the user has access to the activity.
require_login($course, true, $cm);

// Set up the page.
$PAGE->set_url('/mod/engelbrain/submit.php', array('id' => $cm->id));
$PAGE->set_title(format_string($engelbrain->name));
$PAGE->set_heading(format_string($course->fullname));
$context = context_module::instance($cm->id);
$PAGE->set_context($context);

require_capability('mod/engelbrain:submit', $context);

// Check if submissions are closed.
$now = time();
$duedate = $engelbrain->duedate;
$isoverdue = $duedate && $duedate < $now;

if ($isoverdue && !has_capability('mod/engelbrain:grade', $context)) {
    redirect(
        new moodle_url('/mod/engelbrain/view.php', array('id' => $cm->id)),
        get_string('submissionsclosed', 'mod_engelbrain', userdate($duedate)),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

// Get the current user's submission if it exists.
$submission = $DB->get_record('engelbrain_submissions', array(
    'engelbrainid' => $engelbrain->id,
    'userid' => $USER->id
));

// Create a new submission form.
$submissionform = new \mod_engelbrain\form\submission_form(null, array(
    'cm' => $cm,
    'engelbrain' => $engelbrain,
    'submission' => $submission
));

// Handle form submission.
if ($data = $submissionform->get_data()) {
    // Process the submission.
    $now = time();
    
    // Get the submission content.
    $submissioncontent = $data->submission_content['text'];
    
    // Prepare the submission data.
    $submissiondata = new stdClass();
    $submissiondata->engelbrainid = $engelbrain->id;
    $submissiondata->userid = $USER->id;
    $submissiondata->timecreated = $now;
    $submissiondata->timemodified = $now;
    $submissiondata->status = 'submitted';
    $submissiondata->submission_content = $submissioncontent;

    // Create or update the submission record.
    if ($submission) {
        // Update the existing submission.
        $submissiondata->id = $submission->id;
        $DB->update_record('engelbrain_submissions', $submissiondata);
    } else {
        // Create a new submission.
        $submissionid = $DB->insert_record('engelbrain_submissions', $submissiondata);
        $submissiondata->id = $submissionid;
    }

    // Submit the work to engelbrain.de.
    try {
        // Create the API client.
        $apikey = $engelbrain->teacher_api_key;
        if (empty($apikey)) {
            // Use the school API key if no teacher API key is set.
            $apikey = get_config('mod_engelbrain', 'school_api_key');
        }

        if (!empty($apikey)) {
            $client = new \mod_engelbrain\api\client($apikey);
            
            // Prepare metadata for the submission.
            $metadata = array(
                'course_name' => $course->fullname,
                'activity_name' => $engelbrain->name,
                'moodle_submission_id' => $submissiondata->id
            );
            
            // Submit the work to engelbrain.
            $response = $client->submit_work(
                $engelbrain->lerncode,
                $submissioncontent,
                fullname($USER),
                $metadata
            );
            
            // Check if the submission was successful.
            if ($response && isset($response['id'])) {
                // Save the engelbrain submission ID.
                $submissiondata->kw_submission_id = $response['id'];
                $DB->update_record('engelbrain_submissions', $submissiondata);
                
                // Redirect to the view page with a success message.
                redirect(
                    new moodle_url('/mod/engelbrain/view.php', array('id' => $cm->id)),
                    get_string('submission_successful', 'mod_engelbrain'),
                    null,
                    \core\output\notification::NOTIFY_SUCCESS
                );
            } else {
                // Redirect to the view page with an error message.
                redirect(
                    new moodle_url('/mod/engelbrain/view.php', array('id' => $cm->id)),
                    get_string('submission_failed', 'mod_engelbrain', 'Unknown error'),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
            }
        } else {
            // Redirect to the view page with an error message.
            redirect(
                new moodle_url('/mod/engelbrain/view.php', array('id' => $cm->id)),
                get_string('submission_failed', 'mod_engelbrain', 'No API key configured'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }
    } catch (Exception $e) {
        // Redirect to the view page with an error message.
        redirect(
            new moodle_url('/mod/engelbrain/view.php', array('id' => $cm->id)),
            get_string('submission_failed', 'mod_engelbrain', $e->getMessage()),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
} else {
    // Redirect back to the view page if the form wasn't submitted.
    redirect(new moodle_url('/mod/engelbrain/view.php', array('id' => $cm->id)));
} 