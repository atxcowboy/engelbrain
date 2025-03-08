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
 * Local functions for mod_engelbrain.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Make sure the event classes are loaded.
$eventfiles = glob(dirname(__FILE__) . '/classes/event/*.php');
foreach ($eventfiles as $eventfile) {
    require_once($eventfile);
}

/**
 * Get all submissions for a specific engelbrain assignment.
 *
 * @param int $engelbrainid The ID of the engelbrain assignment.
 * @return array An array of submission records.
 */
function engelbrain_get_submissions($engelbrainid) {
    global $DB;
    
    return $DB->get_records('engelbrain_submissions', array('engelbrainid' => $engelbrainid), 'timecreated DESC');
}

/**
 * Get a specific submission for a user in an engelbrain assignment.
 *
 * @param int $engelbrainid The ID of the engelbrain assignment.
 * @param int $userid The ID of the user.
 * @return object|bool The submission record or false if not found.
 */
function engelbrain_get_user_submission($engelbrainid, $userid) {
    global $DB;
    
    return $DB->get_record('engelbrain_submissions', array('engelbrainid' => $engelbrainid, 'userid' => $userid));
}

/**
 * Check if a user can submit to an engelbrain assignment.
 *
 * @param object $engelbrain The engelbrain assignment record.
 * @param object $context The context object.
 * @param int $userid The ID of the user.
 * @return bool True if the user can submit, false otherwise.
 */
function engelbrain_can_submit($engelbrain, $context, $userid = null) {
    global $USER;
    
    if ($userid === null) {
        $userid = $USER->id;
    }
    
    // Check if the user has the capability to submit.
    if (!has_capability('mod/engelbrain:submit', $context, $userid)) {
        return false;
    }
    
    // Check due date if set.
    if ($engelbrain->duedate && $engelbrain->duedate < time()) {
        return false;
    }
    
    return true;
} 