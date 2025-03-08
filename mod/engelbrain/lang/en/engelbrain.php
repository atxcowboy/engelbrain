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
 * English strings for engelbrain
 *
 * @package     mod_engelbrain
 * @category    string
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'engelbrain Integration';
$string['modulename'] = 'engelbrain Assignment';
$string['modulenameplural'] = 'engelbrain Assignments';
$string['pluginadministration'] = 'engelbrain administration';

// Settings
$string['settings_header'] = 'engelbrain Integration Settings';
$string['school_api_key'] = 'School API Key';
$string['school_api_key_desc'] = 'Enter the School API Key provided by engelbrain.de';
$string['api_endpoint'] = 'API Endpoint';
$string['api_endpoint_desc'] = 'The endpoint URL for the engelbrain API';
$string['default_api_endpoint'] = 'https://engelbrain.de/api/v1';

// Activity settings
$string['teacher_api_key'] = 'Teacher API Key';
$string['teacher_api_key_desc'] = 'Enter your personal Teacher API Key';
$string['lerncode'] = 'Lerncode';
$string['lerncode_desc'] = 'Enter the engelbrain Lerncode for this assignment';

// Submission related
$string['submit_to_engelbrain'] = 'Submit to engelbrain';
$string['submission_successful'] = 'Successfully submitted to engelbrain';
$string['submission_failed'] = 'Failed to submit to engelbrain: {$a}';
$string['feedback_from_engelbrain'] = 'Feedback from engelbrain';

// Privacy
$string['privacy:metadata:engelbrain'] = 'In order to integrate with engelbrain, user data needs to be exchanged with that service.';
$string['privacy:metadata:engelbrain:userid'] = 'The userid is sent from Moodle to allow you to access your data on engelbrain.';
$string['privacy:metadata:engelbrain:submissioncontent'] = 'The content of your submissions is sent to engelbrain for processing.';

// German translations
$string['de:pluginname'] = 'engelbrain Integration';
$string['de:modulename'] = 'engelbrain Aufgabe';
$string['de:modulenameplural'] = 'engelbrain Aufgaben';
$string['de:pluginadministration'] = 'engelbrain Administration';
$string['de:settings_header'] = 'engelbrain Integrationseinstellungen';
$string['de:school_api_key'] = 'Schul-API-Schlüssel';
$string['de:school_api_key_desc'] = 'Geben Sie den von engelbrain.de bereitgestellten Schul-API-Schlüssel ein';
$string['de:api_endpoint'] = 'API-Endpunkt';
$string['de:api_endpoint_desc'] = 'Die Endpunkt-URL für die engelbrain-API';
$string['de:teacher_api_key'] = 'Lehrer-API-Schlüssel';
$string['de:teacher_api_key_desc'] = 'Geben Sie Ihren persönlichen Lehrer-API-Schlüssel ein';
$string['de:lerncode'] = 'Lerncode';
$string['de:lerncode_desc'] = 'Geben Sie den engelbrain-Lerncode für diese Aufgabe ein';
$string['de:submit_to_engelbrain'] = 'An engelbrain übermitteln';
$string['de:submission_successful'] = 'Erfolgreich an engelbrain übermittelt';
$string['de:submission_failed'] = 'Übermittlung an engelbrain fehlgeschlagen: {$a}';
$string['de:feedback_from_engelbrain'] = 'Feedback von engelbrain'; 