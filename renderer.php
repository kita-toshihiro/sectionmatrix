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
 * SectionMatrix module renderer
 *
 * @package    mod
 * @subpackage sectionmatrix
 * @copyright  2014 deki
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once ($CFG->dirroot.'/mod/sectionmatrix/locallib.php');

class mod_sectionmatrix_renderer extends plugin_renderer_base {

    /**
     * Returns html to display the content of mod_sectionmatrix
     * (Description, sectionmatrix files and optionally Edit button)
     *
     * @param stdClass $sectionmatrix record from 'sectionmatrix' table (please note
     *     it may not contain fields 'revision' and 'timemodified')
     * @return string
     */
    public function display_sectionmatrix(stdClass $sectionmatrix) {

        $output = '';
        $sectionmatrixinstances = get_fast_modinfo($sectionmatrix->course)->get_instances_of('sectionmatrix');
        if (!isset($sectionmatrixinstances[$sectionmatrix->id]) ||
                !($cm = $sectionmatrixinstances[$sectionmatrix->id]) ||
                !($context = context_module::instance($cm->id))) {
            // Some error in parameters.
            // Don't throw any errors in renderer, just return empty string.
            // Capability to view module must be checked before calling renderer.
            return $output;
        }

        if (trim($sectionmatrix->intro)) {
            if ($sectionmatrix->display != FOLDER_DISPLAY_INLINE) {
                $output .= $this->output->box(format_module_intro('sectionmatrix', $sectionmatrix, $cm->id), 'generalbox', 'intro');
            } else if ($cm->showdescription) {
                // for "display inline" do not filter, filters run at display time.
                $output .= format_module_intro('sectionmatrix', $sectionmatrix, $cm->id, false);
            }
        }

        $sectionmatrix = new section_matrix($sectionmatrix, $cm);
        if ($sectionmatrix->display == FOLDER_DISPLAY_INLINE) {
            // Display module name as the name of the root directory.
            $sectionmatrix->dir['dirname'] = $cm->get_formatted_name();
        }
        $output .= $this->output->box($this->render($sectionmatrix), 'generalbox sectionmatrix');

        // Do not append the edit button on the course page.
        if ($sectionmatrix->display != FOLDER_DISPLAY_INLINE && has_capability('mod/sectionmatrix:managefiles', $context)) {
            $output .= $this->output->container(
                    $this->output->single_button(new moodle_url('/mod/sectionmatrix/edit.php',
                    array('id' => $cm->id)), get_string('edit')),
                    'mdl-align sectionmatrix-edit-button');
            $output .= $this->output->container(
                    $this->output->single_button(new moodle_url('/mod/sectionmatrix/load_syllabus.php',
                    array('id' => $cm->id)), get_string('loadsyllabus')),
                    'mdl-align sectionmatrix-edit-button');
            $output .= $this->output->container(
                    $this->output->single_button(new moodle_url('/mod/sectionmatrix/update_sections.php',
                    array('id' => $cm->id)), get_string('updatesections')),
                    'mdl-align sectionmatrix-edit-button');
        }
        return $output;
    }

    public function render_section_matrix(section_matrix $matrix) {
        static $matrixcounter = 0;

        $content = '';
        $id = 'section_matrix'. ($matrixcounter++);
        $content .= '<div id="'.$id.'">';
        $content .= $this->htmllize_matrix($matrix, array('files' => array(), 'subdirs' => array($matrix->dir)));
        $content .= '</div>';

        return $content;
    }

    /**
     * Internal function - creates htmls structure suitable for section matrix.
     */
    protected function htmllize_matrix($matrix) {
        global $CFG;
        $result = '';

        if (empty($matrix)) {
            return '';
        }
        
        $config = get_config('sectionmatrix');
        
        $sectionmatrix = $matrix->sectionmatrix;
        if ( !isset($sectionmatrix->course_summary) ) {
            global $DB;
            $sectionmatrix = $DB->get_record('sectionmatrix', array('id'=>$sectionmatrix->id), '*', MUST_EXIST);
            $result .= '<h3 class="main">'.$sectionmatrix->name.'</h3>';
        } else {
            $result .= '<h3 class="main">'.$config->outlinename.'</h3>';
            $result .= '<div id="intro" class="box">'.$sectionmatrix->course_summary.'</div>';
            $result .= '<h3 class="main">'.get_string('sectionhead', 'sectionmatrix').'</h3>';
        }
        
        $result .= '<table class="f-sectionmatrix">';
        for ( $i = 1; $i <= $config->matrixdefrow; $i++ ) {
            $result .= '<tr><th style="border:solid #000 1px;">'.sectionmatrix_get_row_name($config,$i).'</th>';
            $result .= '<td style="border:solid #000 1px;">';
            $result .= $sectionmatrix->{'sec_title'.$i};
            $result .= '</td>';
            $result .= '<td style="border:solid #000 1px;">';
            $result .= $sectionmatrix->{'sec_comment'.$i};
            $result .= '</td>';
            $result .= '</tr>';
        }
        $result .= '</table>';
        
        return $result;
    }
}

class section_matrix implements renderable {
    public $context;
    public $sectionmatrix;
    public $cm;

    public function __construct($sectionmatrix, $cm) {
        $this->sectionmatrix = $sectionmatrix;
        $this->cm     = $cm;

        $this->context = context_module::instance($cm->id);
    }
}
