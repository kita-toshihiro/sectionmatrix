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
 * SectionMatrix configuration form
 *
 * @package    mod
 * @subpackage sectionmatrix
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once ($CFG->dirroot.'/mod/sectionmatrix/locallib.php');

class mod_sectionmatrix_mod_form extends moodleform_mod {
    function definition() {

        global $CFG;
        $mform = $this->_form;

        $config = get_config('sectionmatrix');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->add_intro_editor($config->requiremodintro);

        //-------------------------------------------------------
        $mform->addElement('header', 'content', get_string('contentheader', 'sectionmatrix'));

//-- add-s ----
        sectionmatrix_disp_edit_matrix($mform, $config);
//-- add-e ----

        $mform->addElement('select', 'display', get_string('display', 'mod_sectionmatrix'),
                array(FOLDER_DISPLAY_PAGE => get_string('displaypage', 'mod_sectionmatrix'),
                    FOLDER_DISPLAY_INLINE => get_string('displayinline', 'mod_sectionmatrix')));
        $mform->addHelpButton('display', 'display', 'mod_sectionmatrix');

//-- add-s ----
//        $mform->addElement('file', 'datafile', get_string('datafile', 'sectionmatrix'));
//-- add-e ----

        $mform->setExpanded('content');

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();

        //-------------------------------------------------------
        $mform->addElement('hidden', 'revision');
        $mform->setType('revision', PARAM_INT);
        $mform->setDefault('revision', 1);
        

//-- add-s ----
        global $PAGE;
        $jscode = 'var input_file = Y.one("#id_datafile");input_file.on("change", function (e) { M.mod_sectionmatrix.xml_file_check(Y, e);});';
        $module = array();
        $module['name'] = 'event';
        $module['fullpath'] = $CFG->urlroot.'/mod/sectionmatrix/module.js';
        $PAGE->requires->js_init_code($jscode, false, $module);
        $PAGE->requires->js('/mod/sectionmatrix/module.js');
//-- add-e ----
    }

    function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Completion: Automatic on-view completion can not work together with
        // "display inline" option
        if (empty($errors['completion']) &&
                array_key_exists('completion', $data) &&
                $data['completion'] == COMPLETION_TRACKING_AUTOMATIC &&
                !empty($data['completionview']) &&
                $data['display'] == FOLDER_DISPLAY_INLINE) {
            $errors['completion'] = get_string('noautocompletioninline', 'mod_sectionmatrix');
        }

        return $errors;
    }

}
