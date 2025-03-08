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
 * Deutsche Übersetzung für klausurenweb.de
 *
 * @package     mod_engelbrain
 * @category    string
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'klausurenweb.de Integration';
$string['modulename'] = 'klausurenweb.de Aufgabe';
$string['modulenameplural'] = 'klausurenweb.de Aufgaben';
$string['pluginadministration'] = 'klausurenweb.de Administration';

// Einstellungen
$string['settings_header'] = 'klausurenweb.de Integrationseinstellungen';
$string['school_api_key'] = 'Schul-API-Schlüssel';
$string['school_api_key_desc'] = 'Geben Sie den von klausurenweb.de bereitgestellten Schul-API-Schlüssel ein';
$string['api_endpoint'] = 'API-Endpunkt';
$string['api_endpoint_desc'] = 'Die Endpunkt-URL für die klausurenweb.de-API';
$string['default_api_endpoint'] = 'https://klausurenweb.de/api/v1';

// Aktivitätseinstellungen
$string['teacher_api_key'] = 'Lehrer-API-Schlüssel';
$string['teacher_api_key_desc'] = 'Geben Sie Ihren persönlichen Lehrer-API-Schlüssel ein';
$string['teacher_api_key_help'] = 'Dies ist Ihr persönlicher Lehrer-API-Schlüssel, den Sie von klausurenweb.de erhalten haben. Er wird benötigt, um Aufgaben mit Ihrem Lehrer-Konto zu verknüpfen.';
$string['lerncode'] = 'Lerncode';
$string['lerncode_desc'] = 'Geben Sie den klausurenweb.de-Lerncode für diese Aufgabe ein';
$string['lerncode_help'] = 'Der Lerncode ist eine eindeutige Kennung für die Aufgabe in klausurenweb.de. Schüler verwenden diesen Code, um auf die zugewiesene Aufgabe zuzugreifen.';

// Index-Seite
$string['nonewmodules'] = 'Keine klausurenweb.de-Aufgaben gefunden';

// Einreichungsbezogen
$string['submit_to_engelbrain'] = 'An klausurenweb.de übermitteln';
$string['submission_successful'] = 'Erfolgreich an klausurenweb.de übermittelt';
$string['submission_failed'] = 'Übermittlung an klausurenweb.de fehlgeschlagen: {$a}';
$string['feedback_from_engelbrain'] = 'Feedback von klausurenweb.de';
$string['submissions'] = 'Einreichungen';
$string['feedback'] = 'Feedback';
$string['view_submissions'] = 'Einreichungen anzeigen';
$string['submitwork'] = 'Arbeit einreichen';
$string['submissioncontent'] = 'Einreichungsinhalt';
$string['gradinginterface'] = 'Bewertungsschnittstelle';
$string['nosubmissions'] = 'Noch keine Einreichungen';
$string['submissionstatus'] = 'Einreichungsstatus';
$string['status_submitted'] = 'Eingereicht';
$string['submissiondate'] = 'Einreichungsdatum';
$string['student'] = 'Student';
$string['status'] = 'Status';
$string['actions'] = 'Aktionen';
$string['grade'] = 'Bewertung';

// Datenschutz
$string['privacy:metadata:engelbrain'] = 'Um mit klausurenweb.de zu integrieren, müssen Benutzerdaten mit diesem Dienst ausgetauscht werden.';
$string['privacy:metadata:engelbrain:userid'] = 'Die Benutzer-ID wird von Moodle gesendet, damit Sie auf Ihre Daten auf klausurenweb.de zugreifen können.';
$string['privacy:metadata:engelbrain:submissioncontent'] = 'Der Inhalt Ihrer Einreichungen wird zur Verarbeitung an klausurenweb.de gesendet.';

// Fehlermeldungen
$string['missingparameter'] = 'Fehlender erforderlicher Parameter';
$string['invalidcoursemodule'] = 'Ungültiges Kursmodul';
$string['api_error'] = '{$a}';
$string['api_error_unknown'] = 'Unbekannter API-Fehler (HTTP Code: {$a})';
$string['api_error_invalid_json'] = 'Ungültige Antwort vom klausurenweb.de-Server';
$string['unsupported_http_method'] = 'Nicht unterstützte HTTP-Methode'; 
