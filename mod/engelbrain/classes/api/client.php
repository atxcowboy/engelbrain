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
        $url = $this->endpoint . $path;

        $curl = new \curl();
        $curl->setHeader('X-API-Key: ' . $this->apikey);
        $curl->setHeader('Accept: application/json');
        
        // Set timeout values - LLM responses can take several minutes
        $curl->setopt([
            'CURLOPT_CONNECTTIMEOUT' => 20,     // 20 seconds connection timeout
            'CURLOPT_TIMEOUT' => 300,           // 5 minutes total timeout for LLM processing
            'CURLOPT_SSL_VERIFYPEER' => true,   // Verify SSL
            'CURLOPT_FAILONERROR' => false      // Don't fail on error HTTP status
        ]);

        $response = null;
        if ($method === 'GET') {
            $response = $curl->get($url);
        } else if ($method === 'POST') {
            $curl->setHeader('Content-Type: application/json');
            $response = $curl->post($url, json_encode($data));
        } else {
            throw new \moodle_exception('unsupported_http_method', 'mod_engelbrain');
        }

        // Check for connection errors
        if ($curl->errno != 0) {
            $errormsg = "cURL error ({$curl->errno}): {$curl->error}";
            if ($curl->errno == CURLE_OPERATION_TIMEOUTED) {
                $errormsg = "Zeitüberschreitung bei der Verbindung zu klausurenweb.de. Die LLM-Verarbeitung kann bis zu 5 Minuten dauern. Bitte versuchen Sie es erneut oder prüfen Sie später das Ergebnis.";
            } else if ($curl->errno == CURLE_COULDNT_CONNECT || $curl->errno == CURLE_COULDNT_RESOLVE_HOST) {
                $errormsg = "Verbindung zur klausurenweb.de API nicht möglich. Bitte überprüfen Sie Ihre Internetverbindung und die API-Einstellungen.";
            }
            throw new \moodle_exception('api_error', 'mod_engelbrain', '', $errormsg);
        }
        
        // Check for HTTP errors.
        $info = $curl->get_info();
        if ($info['http_code'] >= 400) {
            $error = json_decode($response, true);
            if (isset($error['detail'])) {
                throw new \moodle_exception('api_error', 'mod_engelbrain', '', $error['detail']);
            } else {
                throw new \moodle_exception('api_error_unknown', 'mod_engelbrain', '', $info['http_code']);
            }
        }

        // Parse the response.
        $result = json_decode($response, true);
        if ($result === null) {
            throw new \moodle_exception('api_error_invalid_json', 'mod_engelbrain');
        }

        return $result;
    }
} 
