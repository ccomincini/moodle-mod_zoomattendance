<?php
require('../../config.php');
require_once('graph_api.php');
require_once($CFG->dirroot . '/mod/zoomattendance/lib.php');

$id = required_param('id', PARAM_INT);

list($course, $cm) = get_course_and_cm_from_cmid($id, 'zoomattendance');
$zoom = $DB->get_record('zoomattendance', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/zoomattendance:addinstance', $context);

$PAGE->set_url('/mod/zoomattendance/fetch_attendance.php', ['id' => $id]);
$PAGE->set_context($context);

try {
    // Get zoom meeting record from mdl_zoom table
    $zoom_meeting = $DB->get_record('zoom', ['meeting_id' => $zoom->meeting_id], '*', MUST_EXIST);

    if (empty($zoom_meeting->host_id)) {
        throw new moodle_exception('error', 'mod_zoomattendance', '', 'Zoom meeting UUID (host_id) not found');
    }

    // Fetch participants from Zoom API using the meeting_id
    $participants = get_zoom_meeting_participants($zoom_meeting->meeting_id, $zoom_meeting->webinar);

    if (empty($participants)) {
        throw new moodle_exception('error', 'mod_zoomattendance', '', 'No participants found');
    }

    // Store participants in database
    $stored = 0;
    foreach ($participants as $participant) {
        error_log("DEBUG: Participant - Name: " . $participant->name . " | Email: " . $participant->user_email . " | UserID: " . $participant->user_id);
        try {
            // Match email to find Moodle user
            $moodle_user = $DB->get_record('user', ['email' => $participant->user_email], 'id', IGNORE_MISSING);
            if (!empty($participant->user_email) && $moodle_user) {
                $userid = $moodle_user->id;
            } else {
                $userid = 0;
            }

            $record = new stdClass();
            $record->sessionid = $zoom->id;
            $record->userid = $userid;
            $record->zoom_user_id = $participant->id ?? '';
            $record->zoom_user_email = $participant->user_email ?? '';
            $record->name = $participant->name ?? 'Unknown';
            $record->join_time = isset($participant->join_time) ? strtotime($participant->join_time) : 0;
            $record->leave_time = isset($participant->leave_time) ? strtotime($participant->leave_time) : 0;
            $record->duration = $participant->duration ?? 0;
            $record->manually_assigned = 0;
            $record->timecreated = time();

            error_log("DEBUG: Salvo: name=" . $record->name . ", userid=" . $record->userid . ", email=" . $record->zoom_user_email . ", zoom_user_id=" . $record->zoom_user_id);

            // Check if already exists
            $existing = $DB->get_record('zoomattendance_data', [
                'sessionid' => $zoom->id,
                'zoom_user_id' => $record->zoom_user_id,
                'join_time' => $record->join_time
            ]);

            if (!$existing) {
                $DB->insert_record('zoomattendance_data', $record);
                $stored++;
            }
        } catch (Exception $inner_e) {
            error_log("ERROR storing participant: " . $inner_e->getMessage());
            continue;
        }
    }

    // REDIRECT AFTER LOOP COMPLETES
    redirect(
        new moodle_url('/mod/zoomattendance/view.php', ['id' => $id]),
        "{$stored} records saved successfully",
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );

} catch (Exception $e) {
    redirect(
        new moodle_url('/mod/zoomattendance/view.php', ['id' => $id]),
        'Error: ' . $e->getMessage(),
        null,
        \core\output\notification::NOTIFY_ERROR
    );
}

