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
// Action to fetch automatic feedback.
$fetch_api_feedback = optional_param('fetch_api_feedback', 0, PARAM_BOOL);

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

// Flag to track if an API operation was performed
$api_operation_performed = false;
$api_feedback_message = '';

// Try to get API feedback if requested
if ($fetch_api_feedback) {
    try {
        // Get API key - first try teacher API key, then school API key
        $api_key = $engelbrain->teacher_api_key;
        if (empty($api_key)) {
            $api_key = get_config('mod_engelbrain', 'school_api_key');
        }
        
        if (empty($api_key)) {
            $api_feedback_message = 'API-Schlüssel fehlt. Bitte konfigurieren Sie einen Lehrer-API-Schlüssel oder Schul-API-Schlüssel.';
            echo $OUTPUT->notification($api_feedback_message, \core\output\notification::NOTIFY_ERROR);
            // Log the error
            debugging('API-Schlüssel fehlt. Kein Lehrer- oder Schul-API-Schlüssel konfiguriert.', DEBUG_DEVELOPER);
            goto show_page; // Skip API operations but show the page
        }
        
        // Create API client with verbose error handling
        debugging('Erstelle API-Client mit Schlüssel: ' . substr($api_key, 0, 5) . '...', DEBUG_DEVELOPER);
        $api_client = new \mod_engelbrain\api\client($api_key);
        
        // Test the API connection first
        try {
            // Simple request to verify the API connection - validate the lerncode
            debugging('Validiere Lerncode: ' . $engelbrain->lerncode, DEBUG_DEVELOPER);
            
            if (empty($engelbrain->lerncode)) {
                $api_feedback_message = 'Lerncode fehlt. Bitte konfigurieren Sie einen Lerncode für diese Aktivität.';
                echo $OUTPUT->notification($api_feedback_message, \core\output\notification::NOTIFY_ERROR);
                debugging('Lerncode fehlt. Aktivitäts-ID: ' . $engelbrain->id, DEBUG_DEVELOPER);
                goto show_page; // Skip API operations but show the page
            }
            
            $validation_response = $api_client->validate_lerncode($engelbrain->lerncode);
            debugging('API-Antwort für Lerncode-Validierung: ' . json_encode($validation_response), DEBUG_DEVELOPER);
            
            if (empty($validation_response) || !isset($validation_response['valid'])) {
                $api_feedback_message = 'API-Verbindungstest fehlgeschlagen. Die API-Antwort ist ungültig: ' . 
                    json_encode($validation_response);
                echo $OUTPUT->notification($api_feedback_message, \core\output\notification::NOTIFY_ERROR);
                debugging('Ungültige API-Antwort: ' . json_encode($validation_response), DEBUG_DEVELOPER);
                goto show_page; // Skip API operations but show the page
            }
            
            if (!$validation_response['valid']) {
                $message = isset($validation_response['message']) ? $validation_response['message'] : 'Unbekannter Fehler';
                $api_feedback_message = 'Der Lerncode "' . $engelbrain->lerncode . '" ist ungültig: ' . $message;
                echo $OUTPUT->notification($api_feedback_message, \core\output\notification::NOTIFY_ERROR);
                debugging('Ungültiger Lerncode: ' . $engelbrain->lerncode . ', Nachricht: ' . $message, DEBUG_DEVELOPER);
                goto show_page; // Skip API operations but show the page
            }
        } catch (\Exception $e) {
            $api_feedback_message = 'Fehler beim Testen der API-Verbindung: ' . $e->getMessage();
            echo $OUTPUT->notification($api_feedback_message, \core\output\notification::NOTIFY_ERROR);
            debugging('API-Verbindungsfehler: ' . $e->getMessage() . "\n" . $e->getTraceAsString(), DEBUG_DEVELOPER);
            goto show_page; // Skip API operations but show the page
        }
        
        // Check if there's a submission ID stored from the API
        if (!empty($submission->kw_submission_id)) {
            // Get the feedback from the API for this submission
            try {
                debugging('Hole Feedback für Einreichung: ' . $submission->kw_submission_id, DEBUG_DEVELOPER);
                $feedback_data = $api_client->get_feedback($submission->kw_submission_id);
                debugging('API-Antwort für Feedback: ' . json_encode($feedback_data), DEBUG_DEVELOPER);
                
                // Update the submission with the feedback and grade from the API
                if (!empty($feedback_data) && isset($feedback_data['feedback'])) {
                    $submission->feedback = $feedback_data['feedback'];
                    if (isset($feedback_data['score'])) {
                        // Convert score to grade (assuming API returns score between 0-100)
                        $submission->grade = $feedback_data['score'];
                    }
                    $submission->status = 'graded';
                    $submission->timemodified = time();
                    
                    // Save to database
                    $DB->update_record('engelbrain_submissions', $submission);
                    
                    $api_operation_performed = true;
                    $api_feedback_message = 'Automatisches Feedback wurde erfolgreich von klausurenweb.de abgerufen.';
                } else if (!empty($feedback_data) && $feedback_data['status'] === 'graded') {
                    // Alte API-Version unterstützen
                    $submission->feedback = isset($feedback_data['feedback_text']) ? $feedback_data['feedback_text'] : json_encode($feedback_data);
                    if (isset($feedback_data['score'])) {
                        // Convert score to grade (assuming API returns score between 0-100)
                        $submission->grade = $feedback_data['score'];
                    }
                    $submission->status = 'graded';
                    $submission->timemodified = time();
                    
                    // Save to database
                    $DB->update_record('engelbrain_submissions', $submission);
                    
                    $api_operation_performed = true;
                    $api_feedback_message = 'Automatisches Feedback wurde erfolgreich von klausurenweb.de abgerufen.';
                } else {
                    // Für den Fall, dass noch kein Feedback verfügbar ist
                    $status = isset($feedback_data['status']) ? $feedback_data['status'] : 'unbekannt';
                    $api_feedback_message = 'Kein Feedback von klausurenweb.de verfügbar oder die Bewertung ist noch in Bearbeitung. Status: ' . 
                        $status . '. Sie können später erneut versuchen, das Feedback abzurufen.';
                    debugging('Leere Feedback-Antwort: ' . json_encode($feedback_data), DEBUG_DEVELOPER);
                }
            } catch (\Exception $e) {
                $api_feedback_message = 'Fehler beim Abrufen des Feedbacks: ' . $e->getMessage();
                echo $OUTPUT->notification($api_feedback_message, \core\output\notification::NOTIFY_ERROR);
                debugging('Feedback-Abruf-Fehler: ' . $e->getMessage() . "\n" . $e->getTraceAsString(), DEBUG_DEVELOPER);
            }
        } else {
            // If no kw_submission_id exists, try to submit the content to the API first
            try {
                $student_name = fullname($student);
                $submission_content = $submission->submission_content;
                
                debugging('Übermittle Arbeit an API - Schüler: ' . $student_name . ', Inhaltslänge: ' . 
                    (is_string($submission_content) ? strlen($submission_content) : 'nicht vorhanden'), DEBUG_DEVELOPER);
                
                // Prüfe und korrigiere HTML
                if (is_string($submission_content)) {
                    // Entferne leere Zeilen am Anfang und Ende
                    $submission_content = trim($submission_content);
                    
                    // Prüfe, ob der Inhalt mit <p> beginnt, falls nicht und HTML vorhanden ist
                    if (!empty($submission_content) && stripos($submission_content, '<p>') === false && 
                        (stripos($submission_content, '</p>') !== false || stripos($submission_content, '<br') !== false)) {
                        $submission_content = '<p>' . $submission_content;
                    }
                    
                    // Prüfe, ob der Inhalt mit </p> endet, falls nicht und HTML vorhanden ist
                    if (!empty($submission_content) && stripos($submission_content, '</p>') === false && 
                        (stripos($submission_content, '<p>') !== false || stripos($submission_content, '<br') !== false)) {
                        $submission_content = $submission_content . '</p>';
                    }
                    
                    // Wenn der Inhalt keine HTML-Tags enthält, in <p>-Tags einschließen
                    if (!empty($submission_content) && stripos($submission_content, '<') === false) {
                        $submission_content = '<p>' . $submission_content . '</p>';
                    }
                    
                    debugging('Korrigierter Inhalt für API: ' . $submission_content, DEBUG_DEVELOPER);
                }
                
                if (!empty($submission_content) && !empty($engelbrain->lerncode)) {
                    $metadata = array(
                        'moodle_submission_id' => $submission->id,
                        'course_id' => $course->id,
                        'course_name' => $course->fullname,
                        'activity_name' => $engelbrain->name
                    );
                    
                    debugging('API-Anfrage-Metadaten: ' . json_encode($metadata), DEBUG_DEVELOPER);
                    
                    $response = $api_client->submit_work($engelbrain->lerncode, $submission_content, $student_name, $metadata);
                    debugging('API-Antwort für Einreichung: ' . json_encode($response), DEBUG_DEVELOPER);
                    
                    if (!empty($response) && isset($response['submission_id'])) {
                        // Store the klausurenweb.de submission ID
                        $submission->kw_submission_id = $response['submission_id'];
                        $DB->update_record('engelbrain_submissions', $submission);
                        
                        $api_operation_performed = true;
                        $api_feedback_message = 'Die Einreichung wurde an klausurenweb.de übermittelt. Bitte aktualisieren Sie später, um das Feedback abzurufen. Einreichungs-ID: ' . $response['submission_id'];
                    } else {
                        $api_feedback_message = 'Fehler beim Übermitteln der Einreichung an klausurenweb.de. API-Antwort: ' . json_encode($response);
                        debugging('Ungültige Submit-Antwort: ' . json_encode($response), DEBUG_DEVELOPER);
                    }
                } else {
                    $api_feedback_message = 'Die Einreichung enthält keinen Inhalt oder es ist kein Lerncode konfiguriert. Lerncode: "' . 
                        $engelbrain->lerncode . '", Inhaltsvorhanden: ' . (!empty($submission_content) ? 'Ja' : 'Nein');
                    debugging($api_feedback_message, DEBUG_DEVELOPER);
                }
            } catch (\Exception $e) {
                $api_feedback_message = 'Fehler beim Übermitteln der Arbeit: ' . $e->getMessage();
                echo $OUTPUT->notification($api_feedback_message, \core\output\notification::NOTIFY_ERROR);
                debugging('Übermittlungsfehler: ' . $e->getMessage() . "\n" . $e->getTraceAsString(), DEBUG_DEVELOPER);
            }
        }
    } catch (\Exception $e) {
        $api_feedback_message = 'API-Fehler: ' . $e->getMessage();
        echo $OUTPUT->notification($api_feedback_message, \core\output\notification::NOTIFY_ERROR);
        debugging('Allgemeiner API-Fehler: ' . $e->getMessage() . "\n" . $e->getTraceAsString(), DEBUG_DEVELOPER);
    }
}

// Show page label for goto statements
show_page:

// Display API feedback message if any
if (!empty($api_feedback_message)) {
    $notification_type = $api_operation_performed ? 
        \core\output\notification::NOTIFY_SUCCESS : 
        \core\output\notification::NOTIFY_WARNING;
    
    echo $OUTPUT->notification($api_feedback_message, $notification_type);
}

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

// Display API integration options.
echo $OUTPUT->box_start('generalbox', 'api-options');
echo html_writer::tag('h4', 'Automatische Bewertung mit klausurenweb.de');

$api_url = new moodle_url('/mod/engelbrain/grade.php', array(
    'id' => $cm->id, 
    'sid' => $sid,
    'fetch_api_feedback' => 1
));

echo html_writer::tag('p', 'Klicken Sie auf den Button unten, um die Einreichung an klausurenweb.de zu senden und automatisches Feedback zu erhalten.');
echo html_writer::tag('p', '<strong>Hinweis:</strong> Die KI-gestützte Analyse kann bis zu 5 Minuten dauern. Bitte haben Sie Geduld und aktualisieren Sie die Seite nicht während der Verarbeitung.', array('class' => 'alert alert-info'));
echo html_writer::link(
    $api_url,
    'Automatisches Feedback holen',
    array('class' => 'btn btn-primary')
);

if (!empty($submission->kw_submission_id)) {
    echo html_writer::tag('p', 'klausurenweb.de Einreichungs-ID: ' . $submission->kw_submission_id, array('class' => 'mt-2'));
}

echo $OUTPUT->box_end();

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