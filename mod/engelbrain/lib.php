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
 * Library of interface functions and constants.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function engelbrain_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_GROUPMEMBERSONLY:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_engelbrain into the database.
 *
 * @param stdClass $engelbrain Submitted data from the form.
 * @param mod_engelbrain_mod_form $mform The form.
 * @return int The instance id of the new engelbrain.
 */
function engelbrain_add_instance(stdClass $engelbrain, mod_engelbrain_mod_form $mform = null) {
    global $DB;

    $engelbrain->timecreated = time();
    $engelbrain->timemodified = time();

    $id = $DB->insert_record('engelbrain', $engelbrain);

    return $id;
}

/**
 * Updates an instance of the mod_engelbrain in the database.
 *
 * @param stdClass $engelbrain Submitted data from the form.
 * @param mod_engelbrain_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function engelbrain_update_instance(stdClass $engelbrain, mod_engelbrain_mod_form $mform = null) {
    global $DB;

    $engelbrain->timemodified = time();
    $engelbrain->id = $engelbrain->instance;

    return $DB->update_record('engelbrain', $engelbrain);
}

/**
 * Removes an instance of the mod_engelbrain from the database.
 *
 * @param int $id The ID of the engelbrain instance.
 * @return bool True if successful, false otherwise.
 */
function engelbrain_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('engelbrain', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('engelbrain', array('id' => $id));

    return true;
}

/**
 * Returns the lists of all browsable file areas within the given module context.
 *
 * @param stdClass $course Course object.
 * @param stdClass $cm Course module object.
 * @param stdClass $context Context object.
 * @return string[] Array of file areas.
 */
function engelbrain_get_file_areas($course, $cm, $context) {
    return array(
        'submissions' => get_string('submissions', 'mod_engelbrain'),
        'feedback' => get_string('feedback', 'mod_engelbrain'),
    );
}

/**
 * File browsing support for mod_engelbrain file areas.
 *
 * @param file_browser $browser File browser instance.
 * @param array $areas File areas.
 * @param stdClass $course Course object.
 * @param stdClass $cm Course module object.
 * @param stdClass $context Context object.
 * @param string $filearea File area.
 * @param int $itemid Item ID.
 * @param string $filepath File path.
 * @param string $filename File name.
 * @return file_info Instance or null if not found.
 */
function engelbrain_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the mod_engelbrain file areas.
 *
 * @param stdClass $course Course object.
 * @param stdClass $cm Course module object.
 * @param stdClass $context Context object.
 * @param string $filearea File area.
 * @param array $args Extra arguments.
 * @param bool $forcedownload Whether or not to force download.
 * @param array $options Additional options affecting the file serving.
 * @return bool False if file not found, does not return if found - just send the file.
 */
function engelbrain_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    // Check the contextlevel is as expected.
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'submissions' && $filearea !== 'feedback') {
        return false;
    }

    // Make sure the user is logged in and has access to the module.
    require_login($course, true, $cm);

    // Check the relevant capabilities.
    $canview = has_capability('mod/engelbrain:view', $context);
    if (!$canview) {
        return false;
    }

    // Get the file.
    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_engelbrain/$filearea/$relativepath";
    $file = $fs->get_file_by_hash(sha1($fullpath));
    if (!$file || $file->is_directory()) {
        return false;
    }

    // Send the file.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}

/**
 * Extend the navigation for the engelbrain module.
 *
 * @param navigation_node $engelbrainnode The navigation node to extend.
 * @param stdClass $course The course object.
 * @param stdClass $module The course module object.
 * @param stdClass $cm The course module info object.
 */
function engelbrain_extend_navigation($engelbrainnode, $course, $module, $cm) {
    // Add a custom node after the Edit settings node.
    $engelbrainnode->add(
        get_string('view_submissions', 'mod_engelbrain'),
        new moodle_url('/mod/engelbrain/submissions.php', array('id' => $cm->id)),
        navigation_node::TYPE_SETTING
    );
} 