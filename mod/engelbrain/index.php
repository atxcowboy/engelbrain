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
 * Display information about all the mod_engelbrain modules in the requested course.
 *
 * @package     mod_engelbrain
 * @copyright   2025 Panomity GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');
require_once(__DIR__.'/locallib.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);
require_course_login($course);

$coursecontext = context_course::instance($course->id);

$event = \mod_engelbrain\event\course_module_instance_list_viewed::create(array(
    'context' => $coursecontext
));
$event->trigger();

$PAGE->set_url('/mod/engelbrain/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

echo $OUTPUT->header();

$modulenameplural = get_string('modulenameplural', 'mod_engelbrain');
echo $OUTPUT->heading($modulenameplural);

$engelbrains = get_all_instances_in_course('engelbrain', $course);

if (empty($engelbrains)) {
    notice(get_string('nonewmodules', 'mod_engelbrain'), new moodle_url('/course/view.php', array('id' => $course->id)));
    exit;
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

$table->head = array(
    get_string('name'),
    get_string('duedate', 'assignment'),
    get_string('description')
);

$table->align = array(
    'left',
    'left',
    'left'
);

foreach ($engelbrains as $engelbrain) {
    $cm = get_coursemodule_from_instance('engelbrain', $engelbrain->id, $course->id);
    $context = context_module::instance($cm->id);
    
    $link = html_writer::link(
        new moodle_url('/mod/engelbrain/view.php', array('id' => $cm->id)),
        format_string($engelbrain->name, true, array('context' => $context))
    );

    $duedate = '';
    if ($engelbrain->duedate) {
        $duedate = userdate($engelbrain->duedate);
    }

    $description = format_module_intro('engelbrain', $engelbrain, $cm->id);

    $table->data[] = array(
        $link,
        $duedate,
        $description
    );
}

echo html_writer::table($table);
echo $OUTPUT->footer(); 