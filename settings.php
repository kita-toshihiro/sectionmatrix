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
 * SectionMatrix module admin settings and defaults
 *
 * @package    mod
 * @subpackage sectionmatrix
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configcheckbox('sectionmatrix/requiremodintro',
        get_string('requiremodintro', 'admin'), get_string('configrequiremodintro', 'admin'), 1));

//-- dlt-s ---
//    $settings->add(new admin_setting_configcheckbox('sectionmatrix/showexpanded',
//            get_string('showexpanded', 'sectionmatrix'),
//            get_string('showexpanded_help', 'sectionmatrix'), 1));
//-- dlt-e ---

    //--- original settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('sectionmatrix/rowheading',
            get_string('rowhead', 'sectionmatrix')));

//-- add-s ---
    $settings->add(new admin_setting_configtext('sectionmatrix/outlinename',
            get_string('defoutlinename', 'sectionmatrix'),
            get_string('defoutlinename_help', 'sectionmatrix'),
            get_string('coursesummary') ));

    $options = array();
    for ($i = 1; $i <= 20; $i++) {
        $options[$i.'']      = $i.get_string('defrowunit', 'sectionmatrix');
    }
    $settings->add(new admin_setting_configselect('sectionmatrix/matrixdefrow',
            get_string('defrow', 'sectionmatrix'),
            get_string('defrow_help', 'sectionmatrix'), 15, $options));

    for ($i = 1; $i <= 20; $i++) {
        $settings->add(new admin_setting_configtext('sectionmatrix/matrixdefrowname'.$i,
                get_string('defrowname'.$i, 'sectionmatrix'),
                get_string('defrowname_help', 'sectionmatrix'), substr('000'.$i, -2)));
    }

//-- add-e ---
}
