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
 * Private sectionmatrix module utility functions
 *
 * @package    mod
 * @subpackage sectionmatrix
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/mod/sectionmatrix/lib.php");
require_once("$CFG->libdir/filelib.php");

/**
 * File browsing support class
 */
class sectionmatrix_content_file_info extends file_info_stored {

    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }

    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}

//-- add-s ----
function sectionmatrix_disp_edit_matrix(&$mform, $config = null) {
    if ( empty($mform) ) { return false; }
    if ( empty($config) ) { $config = get_config('sectionmatrix'); }
    
    $mform->addElement('textarea', 'course_summary', $config->outlinename, 'wrap="virtual" rows="10" cols="100"');

    $mform->addElement('html', '<div class="fitem">');
    $mform->addElement('html', '<div class="fitemtitle"><label>'.get_string('sectionhead', 'sectionmatrix').'</label></div>');
    $mform->addElement('html', '<table class="f-sectionmatrix">');
    for ($i = 1; $i <= $config->matrixdefrow; $i++) {
        $mform->addElement('html', '<tr><th style="border:solid #000 1px;">'.sectionmatrix_get_row_name($config,$i).'</th>');
        $mform->addElement('html', '<td style="border:solid #000 1px;">');
        $mform->addElement('text', 'sec_title'.$i, null, array('size'=>'20'));
        $mform->addRule('sec_title'.$i, get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addElement('html', '</td>');
        $mform->addElement('html', '<td style="border:solid #000 1px;">');
        $mform->addElement('text', 'sec_comment'.$i, null, array('size'=>'48'));
        $mform->addRule('sec_comment'.$i, get_string('maximumchars', '', 500), 'maxlength', 500, 'client');
        $mform->addElement('html', '</td>');
        $mform->addElement('html', '</tr>');
    }
    $mform->addElement('html', '</table></div>');
}

function sectionmatrix_get_row_name($config, $num) {
    $result = '';
    foreach ($config as $key => $value) {
        if ( $key == matrixdefrowname . $num ) {
            $result = $value;
            break;
        }
    }
    return $result;
}
//-- add-e ----
