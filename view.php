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
 * SectionMatrix module main user interface
 *
 * @package    mod
 * @subpackage sectionmatrix
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/mod/sectionmatrix/locallib.php");
require_once("$CFG->dirroot/repository/lib.php");
require_once($CFG->libdir . '/completionlib.php');

$id = optional_param('id', 0, PARAM_INT);  // Course module ID
$f  = optional_param('f', 0, PARAM_INT);   // SectionMatrix instance id

if ($f) {  // Two ways to specify the module
    $sectionmatrix = $DB->get_record('sectionmatrix', array('id'=>$f), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('sectionmatrix', $sectionmatrix->id, $sectionmatrix->course, true, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('sectionmatrix', $id, 0, true, MUST_EXIST);
    $sectionmatrix = $DB->get_record('sectionmatrix', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/sectionmatrix:view', $context);
if ($sectionmatrix->display == FOLDER_DISPLAY_INLINE) {
    redirect(course_get_url($sectionmatrix->course, $cm->sectionnum));
}

add_to_log($course->id, 'sectionmatrix', 'view', 'view.php?id='.$cm->id, $sectionmatrix->id, $cm->id);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/sectionmatrix/view.php', array('id' => $cm->id));

$PAGE->set_title($course->shortname.': '.$sectionmatrix->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($sectionmatrix);


$output = $PAGE->get_renderer('mod_sectionmatrix');

echo $output->header();

echo $output->heading(format_string($sectionmatrix->name), 2);

echo $output->display_sectionmatrix($sectionmatrix);

echo $output->footer();
