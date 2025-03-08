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
 * Deutsche Übersetzung für engelbrain
 *
 * @package     mod_engelbrain
 * @category    string
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'engelbrain Integration';
$string['modulename'] = 'engelbrain Aufgabe';
$string['modulenameplural'] = 'engelbrain Aufgaben';
$string['pluginadministration'] = 'engelbrain Administration';

// Einstellungen
$string['settings_header'] = 'engelbrain Integrationseinstellungen';
$string['school_api_key'] = 'Schul-API-Schlüssel';
$string['school_api_key_desc'] = 'Geben Sie den von engelbrain.de bereitgestellten Schul-API-Schlüssel ein';
$string['api_endpoint'] = 'API-Endpunkt';
$string['api_endpoint_desc'] = 'Die Endpunkt-URL für die engelbrain-API';
$string['default_api_endpoint'] = 'https://engelbrain.de/api/v1';

// Aktivitätseinstellungen
$string['teacher_api_key'] = 'Lehrer-API-Schlüssel';
$string['teacher_api_key_desc'] = 'Geben Sie Ihren persönlichen Lehrer-API-Schlüssel ein';
$string['lerncode'] = 'Lerncode';
$string['lerncode_desc'] = 'Geben Sie den engelbrain-Lerncode für diese Aufgabe ein';

// Einreichungsbezogen
$string['submit_to_engelbrain'] = 'An engelbrain übermitteln';
$string['submission_successful'] = 'Erfolgreich an engelbrain übermittelt';
$string['submission_failed'] = 'Übermittlung an engelbrain fehlgeschlagen: {$a}';
$string['feedback_from_engelbrain'] = 'Feedback von engelbrain';

// Datenschutz
$string['privacy:metadata:engelbrain'] = 'Um mit engelbrain zu integrieren, müssen Benutzerdaten mit diesem Dienst ausgetauscht werden.';
$string['privacy:metadata:engelbrain:userid'] = 'Die Benutzer-ID wird von Moodle gesendet, damit Sie auf Ihre Daten auf engelbrain zugreifen können.';
$string['privacy:metadata:engelbrain:submissioncontent'] = 'Der Inhalt Ihrer Einreichungen wird zur Verarbeitung an engelbrain gesendet.'; 