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
 * Javascript helper function for SectionMatrix module
 *
 * @package    mod
 * @subpackage sectionmatrix
 * @copyright  2014 deki
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.mod_sectionmatrix = {};
M.mod_sectionmatrix.xml_file_check = function(Y, e) {
    var uploadFile = document.getElementById ("id_datafile");
    if ( !uploadFile.value ) { return false; }
    if ( !uploadFile.files[0] ) { return false; }
    var fTarget = uploadFile.files[0];
    if ( !confirm("ファイル\n\n"+fTarget.name+"\n\nの値を反映させますか？") ) { return false; }

    var reader = new FileReader();
    reader.readAsText(fTarget);
    //読込終了後の処理
    reader.onload = function(ev){
    
        Y.use("node", "datatype-xml", function (Y) {
            var xmlDoc = Y.XML.parse(reader.result);
            if ( !xmlDoc ) { alert("対象となるデータがありません。"); return false; }
            if ( !xmlDoc.childNodes[0] ) { alert("対象となるデータがありません。"); return false; }
            var infoElement = xmlDoc.childNodes[0];
            if ( infoElement.nodeName != "info" ) { alert("対象となるデータがありません。"); return false; }
            if ( !infoElement.childNodes[0] ) { alert("対象となるデータがありません。"); return false; }
            
            //ノードの数だけループ
            for(var i = 0; i < infoElement.childNodes.length; i++) {
                var eTarget = infoElement.childNodes[i];
                if (eTarget.nodeName == "summary") {
                    document.getElementById ("id_course_summary").value = eTarget.innerHTML;
                } else
                if (eTarget.nodeName == "sections") {
                    for(var j = 0; j < eTarget.childNodes.length; j++) {
                        var sectionElement = eTarget.childNodes[j];
                        if (sectionElement.nodeName == "section") {
                            var no = "";
                            var comment = "";
                            var title = "";
                            for(var k = 0; k < sectionElement.childNodes.length; k++) {
                                var eTarget2 = sectionElement.childNodes[k];
                                if (eTarget2.nodeName == "no") {
                                    no = eTarget2.innerHTML;
                                } else
                                if (eTarget2.nodeName == "title") {
                                    title = eTarget2.innerHTML;
                                } else
                                if (eTarget2.nodeName == "comment") {
                                    comment = eTarget2.innerHTML;
                                }
                                if ( document.getElementById("id_sec_title"+no) && title != "") {
                                    document.getElementById("id_sec_title"+no).value = title;
                                }
                                if ( document.getElementById("id_sec_comment"+no) && comment != "") {
                                    document.getElementById("id_sec_comment"+no).value = comment;
                                }
                            }
                        }
                    }
                }
            }
            alert("ファイル\n\n"+fTarget.name+"\n\nの値を反映しました。");
        }); // end Y.use("node", "datatype-xml"
    };
};
