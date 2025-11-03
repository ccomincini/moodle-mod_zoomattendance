<?php
// NO require() - accedi a config direttamente
define('AJAX_SCRIPT', true);
define('REQUIRE_LOGIN', false);

require('../../config.php');

// Clean output
while (ob_get_level()) ob_end_clean();

// Set headers FIRST
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');

$id = optional_param('id', 0, PARAM_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing id']);
    exit;
}

try {
    // Carica il modulo senza require_login
    $cm = get_coursemodule_from_id('zoomattendance', $id, 0, false, MUST_EXIST);
    $course = get_course($cm->course);
    $context = context_module::instance($cm->id);
    
    // Verifica manuale accesso
    if (isguestuser() || !$USER->id) {
        throw new Exception('Access denied');
    }
    
    require_capability('mod/zoomattendance:addinstance', $context);
    
    global $DB;
    $session = $DB->get_record('zoomattendance', ['id' => $cm->instance], '*', MUST_EXIST);
    
    // Debug: Log che siamo partiti
    error_log('FETCH START: Session ID = ' . $session->id);
    
    // Istanzia la classe
    $merger = new \mod_zoomattendance\interval_merger();
    error_log('MERGER CLASS INSTANTIATED OK');
        
    
    // STEP 1: Recupera TUTTI i record raw dei partecipanti nelle sessioni sovrapposte
    $sql = "SELECT 
            COALESCE(zmp.user_email, CONCAT('unknown_', zmp.name)) as email_key,
            zmp.name,
            zmp.user_email,
            zmp.join_time,
            zmp.leave_time
            FROM {zoom_meeting_participants} zmp
            JOIN {zoom_meeting_details} zmd ON zmp.detailsid = zmd.id
            JOIN {zoom} z ON zmd.zoomid = z.id
            WHERE z.meeting_id = ?
            AND zmd.start_time < ?
            AND zmd.end_time > ?
            AND zmp.leave_time > ?
            AND zmp.join_time < ?
            ORDER BY email_key, zmp.join_time";
    
    $params = [
        $session->meeting_id,
        $session->end_datetime,
        $session->start_datetime,
        $session->start_datetime,
        $session->end_datetime
    ];
    
    $raw_records = $DB->get_records_sql($sql, $params);
    
    // STEP 2: Raggruppa per partecipante e applica interval merging
    $merger = new \mod_zoomattendance\interval_merger();
    $aggregated = [];
    $current_user = null;
    $intervals = [];
    $user_data = [];
    
    foreach ($raw_records as $record) {
        // Nuovo utente
        if ($current_user !== $record->email_key) {
            // Processa gli intervalli precedenti
            if ($current_user !== null && !empty($intervals)) {
                $total_duration = $merger->total_for_range(
                    $intervals,
                    $session->start_datetime,
                    $session->end_datetime
                );
                
                $aggregated[] = (object)[
                    'email_key' => $current_user,
                    'name' => $user_data['name'] ?? 'Unknown',
                    'user_email' => $user_data['user_email'] ?? null,
                    'total_duration' => $total_duration
                ];
            }
            
            // Reset per nuovo utente
            $current_user = $record->email_key;
            $user_data = [
                'name' => $record->name,
                'user_email' => $record->user_email
            ];
            $intervals = [];
        }
        
        // Accumula gli intervalli per questo utente
        $intervals[] = [
            'join_time' => $record->join_time,
            'leave_time' => $record->leave_time
        ];
    }
    
    // Processa l'ultimo utente
    if ($current_user !== null && !empty($intervals)) {
        $total_duration = $merger->total_for_range(
            $intervals,
            $session->start_datetime,
            $session->end_datetime
        );
        
        $aggregated[] = (object)[
            'email_key' => $current_user,
            'name' => $user_data['name'] ?? 'Unknown',
            'user_email' => $user_data['user_email'] ?? null,
            'total_duration' => $total_duration
        ];
    }
    
    // STEP 3: Salva nel database
    $DB->delete_records('zoomattendance_data', ['sessionid' => $session->id]);
    
    $stored = 0;
    foreach ($aggregated as $p) {
        $users = $DB->get_records('user', ['email' => $p->user_email], '', 'id', 0, 1);
        $moodle_user = reset($users) ?: null;
        
        $rec = new stdClass();
        $rec->sessionid = $session->id;
        $rec->userid = $moodle_user ? $moodle_user->id : 0;
        $rec->name = $p->name;
        $rec->join_time = $session->start_datetime;
        $rec->leave_time = $session->end_datetime;
        $rec->attendance_duration = $p->total_duration;
        $rec->actual_attendance = 0;
        $rec->completion_met = 0;
        $rec->timecreated = time();
        
        $DB->insert_record('zoomattendance_data', $rec);
        $stored++;
    }
    
    echo json_encode([
        'success' => true,
        'message' => get_string('fetch_success', 'zoomattendance', $stored),
        'count' => $stored
    ]);

} catch (Throwable $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}