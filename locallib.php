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
require_once("$CFG->dirroot/course/lib.php");

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



function sectionmatrix_loadsyllabus($sectionmatrix, $course){
    global $DB;
   
    for($i=0; $i<2; $i++){
        $a= explode('-',$course->idnumber);
        $nendo =   $a[0];
        $shozoku = $a[1];
        $jcode=    $a[2];
	// $nendo = "2016";  $shozoku = "58";  $jcode= "00601";
        $data0 = json_decode(file_get_contents('http://syllabus.kumamoto-u.ac.jp/rest/auth/syllabusKakukaiView.json?locale=ja&nendo='.$nendo.'&jikanwari_shozokucd='.$shozoku.'&jikanwaricd='.$jcode));
        if ( count($data0)>0 ){
	    break;
	}else{
	    // for parent courses (with no syllabus data), find the child course 
            $course_child = $DB->get_record("enrol", array('enrol'=>'meta','courseid'=>$course->id));
            $course = $DB->get_record( "course", array('id'=>$course_child->customint1) );
	}
    }
    // echo "<pre>"; var_dump($data0);
    foreach($data0 as $data){
      // echo $data->kai." ".$data->kakukai_theme." ".$data->kakukai_summary."\n";
        $sectionid= $data->kai;
        $sec_title= "sec_title".$sectionid;
        $sec_comment= "sec_comment".$sectionid;
        $sectionmatrix->$sec_title = $data->kakukai_theme;
        $sectionmatrix->$sec_comment = $data->kakukai_summary;
    }
    $DB->update_record('sectionmatrix', $sectionmatrix);
}

function sectionmatrix_updatesection($sectionmatrix, $course, $forceupdate=null){
    global $DB;

    $courseid = $course->id;
    if ( !$forceupdate ){
      if ($DB->get_record_select("course_sections", '(course = '.$courseid.') AND ( (name <> "" AND name IS NOT NULL) OR (summary <> "" AND summary IS NOT NULL) )')){
            return -1;
	}
    }
    // TODO: コースのセクション数が少ない場合は増やす処理をここに

    for($sectionid= 1; $sectionid<=20; $sectionid++){
        $sec_title= "sec_title".$sectionid;
        $sec_comment= "sec_comment".$sectionid;
        if ( $section = $DB->get_record("course_sections", array("course"=>$courseid, "section"=>$sectionid)) ){
            course_update_section($courseid, $section, array('name' => $sectionmatrix->$sec_title, 'summary' => $sectionmatrix->$sec_comment));
        }
    }
}

    
