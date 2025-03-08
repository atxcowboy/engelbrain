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
 * engelbrain API client class.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_engelbrain\api;

defined('MOODLE_INTERNAL') || die();

/**
 * engelbrain API client class.
 *
 * This class handles all communication with the engelbrain API.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class client {
    /** @var string The API endpoint URL */
    private $endpoint;

    /** @var string The API key */
    private $apikey;

    /**
     * Constructor.
     *
     * @param string $apikey The API key to use for requests.
     * @param string $endpoint The API endpoint URL.
     */
    public function __construct($apikey, $endpoint = null) {
        global $CFG;

        $this->apikey = $apikey;

        if (empty($endpoint)) {
            // Get the API endpoint from the plugin settings.
            $this->endpoint = get_config('mod_engelbrain', 'api_endpoint');
            if (empty($this->endpoint)) {
                // Use the default endpoint if no endpoint is set.
                $this->endpoint = 'https://klausurenweb.de/api/v1';
            }
        } else {
            $this->endpoint = $endpoint;
        }
    }

    /**
     * Validate a lerncode with the engelbrain API.
     *
     * @param string $lerncode The lerncode to validate.
     * @return array The API response.
     */
    public function validate_lerncode($lerncode) {
        return $this->request('GET', "/lerncode/{$lerncode}/validate");
    }

    /**
     * Submit student work to engelbrain for evaluation.
     *
     * @param string $lerncode The lerncode to submit to.
     * @param string $content The content to submit.
     * @param string $studentname The name of the student.
     * @param array $metadata Additional metadata to include with the submission.
     * @return array The API response.
     */
    public function submit_work($lerncode, $content, $studentname, $metadata = array()) {
        $data = array(
            'content' => $content,
            'student_name' => $studentname,
            'metadata' => $metadata
        );

        return $this->request('POST', "/submissions/{$lerncode}", $data);
    }

    /**
     * Get feedback for a submission.
     *
     * @param string $submissionid The ID of the submission.
     * @return array The API response.
     */
    public function get_feedback($submissionid) {
        return $this->request('GET', "/submissions/{$submissionid}/feedback");
    }

    /**
     * Make a request to the engelbrain API.
     *
     * @param string $method The HTTP method to use.
     * @param string $path The API path to request.
     * @param array $data The data to send with the request.
     * @return array The API response.
     */
    private function request($method, $path, $data = null) {
        // Prepare the full URL
        $url = rtrim($this->endpoint, '/') . '/' . ltrim($path, '/');
        
        // Log request details for debugging
        debugging('API-Anfrage: ' . $method . ' ' . $url, DEBUG_DEVELOPER);
        if ($data) {
            debugging('API-Anfragedaten: ' . json_encode($data), DEBUG_DEVELOPER);
        }
        
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        
        // Set timeout values
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        
        // Set API key header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-API-KEY: ' . $this->apikey,
            'Content-Type: application/json',
            'Accept: application/json'
        ));
        
        // Set method and data if needed
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } else if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        // Execute the request
        $response = curl_exec($ch);
        
        // Get response info
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        $curl_errno = curl_errno($ch);
        $total_time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        
        // Log response details
        debugging('API-Antwort-Status: ' . $status . ', Zeit: ' . $total_time . 's', DEBUG_DEVELOPER);
        if ($response) {
            debugging('API-Antwort-Körper: ' . substr($response, 0, 500) . (strlen($response) > 500 ? '...' : ''), DEBUG_DEVELOPER);
        }
        
        // Handle connection errors
        if ($response === false) {
            debugging('cURL-Fehler: ' . $curl_error . ' (Code: ' . $curl_errno . ')', DEBUG_DEVELOPER);
            
            // Handle specific curl errors with user-friendly messages
            switch ($curl_errno) {
                case CURLE_OPERATION_TIMEOUTED:
                    throw new \moodle_exception('api_error', 'mod_engelbrain', '', 
                        'Zeitüberschreitung bei der Verbindung zu klausurenweb.de. Die LLM-Verarbeitung kann bis zu 5 Minuten dauern. ' .
                        'Bitte versuchen Sie es erneut oder prüfen Sie später das Ergebnis. Technische Details: ' . $curl_error);
                    
                case CURLE_COULDNT_CONNECT:
                    throw new \moodle_exception('api_error', 'mod_engelbrain', '', 
                        'Verbindung zu klausurenweb.de konnte nicht hergestellt werden. Bitte prüfen Sie Ihre Internetverbindung ' .
                        'oder versuchen Sie es später erneut. Technische Details: ' . $curl_error);
                    
                case CURLE_COULDNT_RESOLVE_HOST:
                    throw new \moodle_exception('api_error', 'mod_engelbrain', '', 
                        'Der Server "klausurenweb.de" konnte nicht gefunden werden. Bitte prüfen Sie Ihre DNS-Einstellungen ' .
                        'oder versuchen Sie es später erneut. Technische Details: ' . $curl_error);
                    
                default:
                    throw new \moodle_exception('api_error', 'mod_engelbrain', '', 
                        'Ein Fehler ist bei der Verbindung zu klausurenweb.de aufgetreten. Fehlercode: ' . $curl_errno . 
                        '. Technische Details: ' . $curl_error);
            }
        }
        
        // Handle HTTP errors
        if ($status >= 400) {
            debugging('HTTP-Fehler: ' . $status, DEBUG_DEVELOPER);
            
            // Try to parse the error response
            $error_data = json_decode($response, true);
            $error_message = '';
            
            // Versuche zuerst "detail" zu finden (typisch für FastAPI/Django), dann "message"
            if (isset($error_data['detail'])) {
                $error_message = $error_data['detail'];
            } else if (isset($error_data['message'])) {
                $error_message = $error_data['message'];
            } else {
                $error_message = 'Unbekannter Fehler';
            }
            
            debugging('Fehleranalyse - HTTP-Status: ' . $status . ', Fehlermeldung: ' . $error_message, DEBUG_DEVELOPER);
            
            // Build detailed error message based on HTTP status
            switch ($status) {
                case 401:
                    $fehlermeldung = 'Authentifizierungsfehler: Der API-Schlüssel ist ungültig oder fehlt. ' .
                        'Bitte überprüfen Sie den API-Schlüssel in den Einstellungen. Server-Antwort: ' . $error_message;
                    break;
                
                case 403:
                    $fehlermeldung = 'Zugriff verweigert: Sie haben keine Berechtigung für diese Operation. ' .
                        'Bitte kontaktieren Sie den Support oder überprüfen Sie Ihre Berechtigungen. Server-Antwort: ' . $error_message;
                    break;
                    
                case 404:
                    $fehlermeldung = 'Die angeforderte Ressource wurde nicht gefunden. ' .
                        'Bitte überprüfen Sie den Lerncode oder die Einreichungs-ID. Server-Antwort: ' . $error_message;
                    break;
                    
                case 429:
                    $fehlermeldung = 'Zu viele Anfragen an die API. Bitte warten Sie einen Moment und versuchen Sie es erneut. ' .
                        'Server-Antwort: ' . $error_message;
                    break;
                    
                case 500:
                case 502:
                case 503:
                case 504:
                    $fehlermeldung = 'Ein Serverfehler ist bei klausurenweb.de aufgetreten. Bitte versuchen Sie es später erneut. ' .
                        'HTTP-Status: ' . $status . '. Server-Antwort: ' . $error_message;
                    break;
                    
                default:
                    $fehlermeldung = 'Ein unerwarteter Fehler ist aufgetreten. HTTP-Status: ' . $status . '. Server-Antwort: ' . $error_message;
            }
            
            debugging('Finale Fehlermeldung: ' . $fehlermeldung, DEBUG_DEVELOPER);
            throw new \moodle_exception('api_error', 'mod_engelbrain', '', $fehlermeldung);
        }
        
        curl_close($ch);
        
        // Try to parse the response
        $response_data = json_decode($response, true);
        if ($response && $response_data === null) {
            debugging('Ungültiges JSON in der API-Antwort: ' . substr($response, 0, 500), DEBUG_DEVELOPER);
            throw new \moodle_exception('api_error', 'mod_engelbrain', '', 
                'Die Antwort von klausurenweb.de enthielt ungültiges JSON. Technische Details: ' . 
                substr($response, 0, 200) . (strlen($response) > 200 ? '...' : ''));
        }
        
        return $response_data;
    }
} 
