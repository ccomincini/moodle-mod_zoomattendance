<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);
$cm = get_coursemodule_from_id('zoomattendance', $id, 0, false, MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($cm->course, false, $cm);
require_capability('mod/zoomattendance:addinstance', $context);

global $DB;
$session = $DB->get_record('zoomattendance', ['id' => $cm->instance], '*', MUST_EXIST);

// Resetta SOLO le assegnazioni manuali: azzerare userid E manually_assigned
$DB->execute("UPDATE {zoomattendance_data} 
             SET manually_assigned = 0, userid = 0
             WHERE sessionid = ? AND manually_assigned = 1", [$session->id]);

// Redirect con messaggio
$SESSION->zoom_fetch_message = get_string('reset_success', 'mod_zoomattendance');
redirect(new moodle_url('/mod/zoomattendance/view.php', ['id' => $cm->id]));
