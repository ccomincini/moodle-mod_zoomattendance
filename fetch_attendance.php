<?php
define('AJAX_SCRIPT', true);
require('../../config.php');
require_once($CFG->dirroot.'/mod/zoomattendance/classes/suggestion_engine.php');

// Blinda l'output per sicurezza AJAX
@ob_end_clean();
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$id = required_param('id', PARAM_INT);

try {
    $cm = get_coursemodule_from_id('zoomattendance', $id, 0, false, MUST_EXIST);
    $context = context_module::instance($cm->id);
    $session = $DB->get_record('zoomattendance', ['id' => $cm->instance], '*', MUST_EXIST);
    require_login($cm->course, false, $cm);
    require_capability('mod/zoomattendance:addinstance', $context);

    $zoom_details = $DB->get_records('zoom_meeting_details', ['meeting_id' => $session->meeting_id]);
    $all_participants = [];
    foreach ($zoom_details as $detail) {
        $participants = $DB->get_records('zoom_meeting_participants', ['detailsid' => $detail->id]);
        $all_participants = array_merge($all_participants, $participants);
    }

    $context_course = context_course::instance($cm->course);
    $enrolled_users = get_enrolled_users($context_course, '', 0, 'u.id, u.firstname, u.lastname, u.email');
    $sugg_engine = new suggestion_engine($enrolled_users);

    // Pulisci i record precedenti di questa sessione
    $DB->delete_records('zoomattendance_data', ['sessionid' => $session->id]);

    $imported = 0;

    foreach ($all_participants as $raw) {
        $zoom_email = strtolower($raw->user_email ?? '');
        $zoom_name = trim($raw->name);
        $userid = 0;

        // 1. Match email esatta (blindato)
        $users = array_filter($enrolled_users, fn($u) => strtolower($u->email) === $zoom_email);
        if ($zoom_email && count($users) === 1) {
            $userid = reset($users)->id;
        } else {
            // 2. Usa suggestion_engine SOLO se scorings high-confidence
            $suggestions = $sugg_engine->generate_suggestions([(object)['id'=>0,'name'=>$zoom_name,'user_email'=>$zoom_email]]);
            if (!empty($suggestions) && is_array($suggestions)) {
                $first = reset($suggestions);
                if ($first['confidence'] === 'high') {
                    $userid = $first['user']->id;
                }
            }
        }

        // Crea record finale
        $rec = new stdClass();
        $rec->sessionid = $session->id;
        $rec->userid = $userid;
        $rec->name = $zoom_name;
        $rec->user_email = $zoom_email;

        // Converti join/leave a timestamp se necessario
        $join_ts = !empty($raw->join_time) ? (int)$raw->join_time : 0;
        $leave_ts = !empty($raw->leave_time) ? (int)$raw->leave_time : 0;

        // Clip join/leave al range del registro
        $clip_join = max($join_ts, $session->start_datetime);
        $clip_leave = min($leave_ts, $session->end_datetime);

        // Salva i timestamp originali per audit
        $rec->join_time = $join_ts;
        $rec->leave_time = $leave_ts;


        // Calcola durata solo se sovrapposto al range
        if ($clip_leave > $clip_join) {
            $rec->attendance_duration = $clip_leave - $clip_join;
        } else {
            $rec->attendance_duration = 0;
        }
        $rec->manually_assigned = 0;
        $rec->timecreated = time();

        $DB->insert_record('zoomattendance_data', $rec);
        $imported++;
    }

    echo json_encode([
        'success' => true,
        'imported' => $imported,
        'message' => " $imported meeting attendance sessions were downloaded from Zoom. These will be compared to the log settings to verify attendance within the specified date range."
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit;
}
exit;
