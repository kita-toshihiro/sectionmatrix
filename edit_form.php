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
 * A moodle form to manage sectionmatrix files
 *
 * @package    mod
 * @subpackage sectionmatrix
 * @copyright  2014 deki
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");
require_once("$CFG->dirroot/mod/sectionmatrix/locallib.php");

class mod_sectionmatrix_edit_form extends moodleform {

    function definition() {

        $mform = $this->_form;

        $sectionmatrix    = $this->_customdata['matrix'];

        $mform->addElement('hidden', 'id', $sectionmatrix->id);
        $mform->setType('id', PARAM_INT);
        
        sectionmatrix_disp_edit_matrix($mform, $config);
        
//        $mform->addElement('file', 'datafile', get_string('datafile', 'sectionmatrix'));
        
        $submit_string = get_string('savechanges');
        $this->add_action_buttons(true, $submit_string);

        $this->set_data($sectionmatrix);

        global $PAGE;
        $jscode = 'var input_file = Y.one("#id_datafile");input_file.on("change", function (e) { M.mod_sectionmatrix.xml_file_check(Y, e);});';
        $module = array();
        $module['name'] = 'event';
        $module['fullpath'] = $CFG->urlroot.'/mod/sectionmatrix/module.js';
        $PAGE->requires->js_init_code($jscode, false, $module);
        $PAGE->requires->js('/mod/sectionmatrix/module.js');

    }

}
