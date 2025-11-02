<?php
require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$id = required_param('id', PARAM_INT);
require_login();
require_sesskey();
$context = context_module::instance($id);
require_capability('mod/zoomattendance:manageattendance', $context);

$cm = get_coursemodule_from_id('zoomattendance', $id, 0, false, MUST_EXIST);
$session = $DB->get_record('zoomattendance', ['id' => $cm->instance], '*', MUST_EXIST);
$DB->set_field('zoomattendance', 'status', 'open', ['id' => $session->id]);

// Log the action
\core\event\course_module_updated::create([
    'objectid' => $cm->instance,
    'context' => $context,
    'other' => [
        'action' => 'reopen_register'
    ]
])->trigger();

redirect(new moodle_url('/mod/zoomattendance/view.php', ['id' => $id]), get_string('register_reopened', 'mod_zoomattendance'));
