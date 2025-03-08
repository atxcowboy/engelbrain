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
 * English strings for klausurenweb.de
 *
 * @package     mod_engelbrain
 * @category    string
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'klausurenweb.de Integration';
$string['modulename'] = 'klausurenweb.de Assignment';
$string['modulenameplural'] = 'klausurenweb.de Assignments';
$string['pluginadministration'] = 'klausurenweb.de administration';

// Settings
$string['settings_header'] = 'klausurenweb.de Integration Settings';
$string['school_api_key'] = 'School API Key';
$string['school_api_key_desc'] = 'Enter the School API Key provided by klausurenweb.de';
$string['api_endpoint'] = 'API Endpoint';
$string['api_endpoint_desc'] = 'The endpoint URL for the klausurenweb.de API';
$string['default_api_endpoint'] = 'https://klausurenweb.de/api/v1';

// Activity settings
$string['teacher_api_key'] = 'Teacher API Key';
$string['teacher_api_key_desc'] = 'Enter your personal Teacher API Key';
$string['teacher_api_key_help'] = 'This is your personal Teacher API Key provided by klausurenweb.de. It is needed to link assignments with your teacher account.';
$string['lerncode'] = 'Lerncode';
$string['lerncode_desc'] = 'Enter the klausurenweb.de Lerncode for this assignment';
$string['lerncode_help'] = 'The Lerncode is a unique identifier for the assignment in klausurenweb.de. Students use this code to access the assigned task.';

// Index page
$string['nonewmodules'] = 'No klausurenweb.de assignments found';

// Submission related
$string['submit_to_engelbrain'] = 'Submit to klausurenweb.de';
$string['submission_successful'] = 'Successfully submitted to klausurenweb.de';
$string['submission_failed'] = 'Failed to submit to klausurenweb.de: {$a}';
$string['feedback_from_engelbrain'] = 'Feedback from klausurenweb.de';
$string['submissions'] = 'Submissions';
$string['feedback'] = 'Feedback';
$string['view_submissions'] = 'View submissions';
$string['submitwork'] = 'Submit work';
$string['submissioncontent'] = 'Submission content';
$string['gradinginterface'] = 'Grading interface';
$string['nosubmissions'] = 'No submissions yet';
$string['submissionstatus'] = 'Submission status';
$string['status_submitted'] = 'Submitted';
$string['submissiondate'] = 'Submission date';
$string['student'] = 'Student';
$string['status'] = 'Status';
$string['actions'] = 'Actions';
$string['grade'] = 'Grade';

// Privacy
$string['privacy:metadata:engelbrain'] = 'In order to integrate with klausurenweb.de, user data needs to be exchanged with that service.';
$string['privacy:metadata:engelbrain:userid'] = 'The userid is sent from Moodle to allow you to access your data on klausurenweb.de.';
$string['privacy:metadata:engelbrain:submissioncontent'] = 'The content of your submissions is sent to klausurenweb.de for processing.';

// Error messages
$string['missingparameter'] = 'Missing required parameter';
$string['invalidcoursemodule'] = 'Invalid course module';

// German translations
$string['de:pluginname'] = 'klausurenweb.de Integration';
$string['de:modulename'] = 'klausurenweb.de Aufgabe';
$string['de:modulenameplural'] = 'klausurenweb.de Aufgaben';
$string['de:pluginadministration'] = 'klausurenweb.de Administration';
$string['de:settings_header'] = 'klausurenweb.de Integrationseinstellungen';
$string['de:school_api_key'] = 'Schul-API-Schlüssel';
$string['de:school_api_key_desc'] = 'Geben Sie den von klausurenweb.de bereitgestellten Schul-API-Schlüssel ein';
$string['de:api_endpoint'] = 'API-Endpunkt';
$string['de:api_endpoint_desc'] = 'Die Endpunkt-URL für die klausurenweb.de-API';
$string['de:teacher_api_key'] = 'Lehrer-API-Schlüssel';
$string['de:teacher_api_key_desc'] = 'Geben Sie Ihren persönlichen Lehrer-API-Schlüssel ein';
$string['de:lerncode'] = 'Lerncode';
$string['de:lerncode_desc'] = 'Geben Sie den klausurenweb.de-Lerncode für diese Aufgabe ein';
$string['de:submit_to_engelbrain'] = 'An klausurenweb.de übermitteln';
$string['de:submission_successful'] = 'Erfolgreich an klausurenweb.de übermittelt';
$string['de:submission_failed'] = 'Übermittlung an klausurenweb.de fehlgeschlagen: {$a}';
$string['de:feedback_from_engelbrain'] = 'Feedback von klausurenweb.de'; 
