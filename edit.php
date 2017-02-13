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
 * Manage files in sectionmatrix module instance
 *
 * @package    mod
 * @subpackage sectionmatrix
 * @copyright  2014 deki
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require('../../config.php');
require_once("$CFG->dirroot/mod/sectionmatrix/locallib.php");
require_once("$CFG->dirroot/mod/sectionmatrix/edit_form.php");
require_once("$CFG->dirroot/repository/lib.php");

$id = required_param('id', PARAM_INT);  // Course module ID
$course_id_num = optional_param('cin','', PARAM_TEXT);  // course_id_num

if ( $course_id_num != '' && $id == 0 ) {
	$course = $DB->get_record('course', array('idnumber'=>$course_id_num), '*', MUST_EXIST);
	$cms = get_course_mods($course->id, MUST_EXIST);
	foreach ($cms as $cmid => $tmpcm) {
		if ( $tmpcm->modname == 'sectionmatrix' ) {
			$cm = $tmpcm;
			$id = $cmid;
			break;
		}
	}
	$context = context_course::instance($course->id, MUST_EXIST);
	$sectionmatrix = $DB->get_record('sectionmatrix', array('id'=>$cm->instance), '*', MUST_EXIST);
} else {
	if ( $cm = get_coursemodule_from_id('sectionmatrix', $id, 0, true) ) {
		$context = context_module::instance($cm->id, MUST_EXIST);
		$sectionmatrix = $DB->get_record('sectionmatrix', array('id'=>$cm->instance), '*', MUST_EXIST);
		$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);
	} else {
		$sectionmatrix = $DB->get_record('sectionmatrix', array('id'=>$id), '*', MUST_EXIST);
		$course = $DB->get_record('course', array('id'=>$sectionmatrix->course), '*', MUST_EXIST);
		$cms = get_course_mods($course->id, MUST_EXIST);
		foreach ($cms as $cmid => $tmpcm) {
			if ( $tmpcm->modname == 'sectionmatrix' ) {
				$cm = $tmpcm;
				$id = $cmid;
				break;
			}
		}
		$context = context_course::instance($course->id, MUST_EXIST);
	}
}

require_login($course, false, $cm);
require_capability('mod/sectionmatrix:managefiles', $context);

$PAGE->set_url('/mod/sectionmatrix/edit.php', array('id' => $cm->id));
$PAGE->set_title($course->shortname.': '.$sectionmatrix->name);
$PAGE->set_heading($course->fullname);
$PAGE->set_activity_record($sectionmatrix);

$mform = new mod_sectionmatrix_edit_form(null, array('matrix'=>$sectionmatrix));
if ($sectionmatrix->display == FOLDER_DISPLAY_INLINE) {
    $redirecturl = course_get_url($cm->course, $cm->sectionnum);
} else {
    $redirecturl = new moodle_url('/mod/sectionmatrix/view.php', array('id' => $cm->id));
}

if ($mform->is_cancelled()) {
    redirect($redirecturl);
} else if ($formdata = $mform->get_data()) {
    $DB->set_field('sectionmatrix', 'revision', $sectionmatrix->revision+1, array('id'=>$sectionmatrix->id));
    $DB->set_field('sectionmatrix', 'course_summary', $formdata->course_summary, array('id'=>$sectionmatrix->id));
    for ($i = 1; $i <= 20; $i++) {
        $DB->set_field('sectionmatrix', 'sec_title'  .$i, $formdata->{'sec_title'  .$i}, array('id'=>$sectionmatrix->id));
        $DB->set_field('sectionmatrix', 'sec_comment'.$i, $formdata->{'sec_comment'.$i}, array('id'=>$sectionmatrix->id));
    }
    $DB->set_field('sectionmatrix', 'timemodified', time(), array('id'=>$user->id));

    add_to_log($course->id, 'filematrix', 'edit', 'edit.php?id='.$cm->id, $filematrix->id, $cm->id);

    redirect($redirecturl);
}

echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox sectionmatrix');
$mform->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
