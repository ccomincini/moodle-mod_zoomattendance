<?php
require('../../config.php');

// Display any fetch messages
if (!empty($SESSION->zoom_fetch_message)) {
    \core\notification::add($SESSION->zoom_fetch_message, \core\output\notification::NOTIFY_SUCCESS);
    unset($SESSION->zoom_fetch_message);
}
if (!empty($SESSION->zoom_fetch_error)) {
    \core\notification::add($SESSION->zoom_fetch_error, \core\output\notification::NOTIFY_ERROR);
    unset($SESSION->zoom_fetch_error);
}

$id = required_param('id', PARAM_INT);
list($course, $cm) = get_course_and_cm_from_cmid($id, 'zoomattendance');
require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/zoomattendance:view', $context);

$PAGE->set_url('/mod/zoomattendance/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($cm->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->requires->css('/mod/zoomattendance/styles/view_attendance.css');

global $DB;
$session = $DB->get_record('zoomattendance', ['id' => $cm->instance], '*', MUST_EXIST);

echo $OUTPUT->header();

// ========== HELPER FUNCTIONS ==========
function format_duration($seconds) {
    $hours = floor($seconds / 3600);
    $remaining = $seconds % 3600;
    $minutes = floor($remaining / 60);
    $secs = $remaining % 60;
    if ($secs >= 30) $minutes++;
    if ($minutes >= 60) { $hours++; $minutes = 0; }
    return sprintf('%dh%dm', $hours, $minutes);
}

echo '<div class="mod_zoomattendance">';

// ========== HEADER INFORMATIVO ==========
$duration_seconds = $session->end_datetime - $session->start_datetime;
$threshold = (int)$session->required_attendance;

// Carica il meeting Zoom: mdl_zoomattendance.meeting_id = mdl_zoom.meeting_id
$meeting_name = 'N/A';
if ($session->meeting_id) {
    $zoom_meeting = $DB->get_record('zoom', ['meeting_id' => $session->meeting_id], '*', false);
    if ($zoom_meeting) {
        $meeting_name = $zoom_meeting->name;
    }
}

// Formatta le date di inizio e fine
$start_date = userdate($session->start_datetime, get_string('strftimedaydatetime', 'langconfig'));
$end_date = userdate($session->end_datetime, get_string('strftimedaydatetime', 'langconfig'));

$title_data = (object)[
    'meeting_name' => $meeting_name,
    'start_date' => $start_date,
    'end_date' => $end_date,
    'duration' => format_duration($duration_seconds),
    'threshold' => $threshold
];
echo '<h5>' . get_string('attendance_register_title', 'mod_zoomattendance', $title_data) . '<br></h5>';

// ========== STATS CARDS ==========
$automatic_records = $DB->count_records_select('zoomattendance_data', 'sessionid = ? AND userid > 0 AND manually_assigned = 0', [$session->id]);
$manual_records = $DB->count_records_select('zoomattendance_data', 'sessionid = ? AND userid > 0 AND manually_assigned = 1', [$session->id]);
$unassigned_count = $DB->count_records_select('zoomattendance_data', 'sessionid = ? AND userid = 0', [$session->id]);
$total_records = $automatic_records + $manual_records + $unassigned_count;

echo '<div class="stats-container">';
echo '<div class="stats-card card-total"><h4>' . get_string('total_records', 'mod_zoomattendance') . '</h4><div class="metric">' . $total_records . '</div></div>';
echo '<div class="stats-card card-automatic"><h4>' . get_string('automatic_assignments', 'mod_zoomattendance') . '</h4><div class="metric">' . $automatic_records . '</div></div>';
echo '<div class="stats-card card-manual"><h4>' . get_string('manual_assignments', 'mod_zoomattendance') . '</h4><div class="metric">' . $manual_records . '</div></div>';
echo '<div class="stats-card card-unassigned"><h4>' . get_string('unassigned_records', 'mod_zoomattendance') . '</h4><div class="metric">' . $unassigned_count . '</div></div>';
echo '</div>';

// ========== ACTION BUTTONS ==========
echo '<div class="action-buttons-container">';
echo '<div class="action-card card-fetch"><h4>' . get_string('fetch_zoom_data', 'mod_zoomattendance') . '</h4>';
echo '<a class="btn btn-secondary" id="fetch-btn" href="#">' . get_string('fetch_zoom_data', 'mod_zoomattendance') . '</a></div>';
echo '<div class="action-card card-manage"><h4>' . get_string('manage_unassigned', 'mod_zoomattendance') . '</h4>';
echo '<a class="btn btn-warning" href="/mod/zoomattendance/manage_unassigned.php?id=' . $cm->id . '">' . get_string('manage_unassigned', 'mod_zoomattendance') . '</a></div>';
echo '<div class="action-card card-reset"><h4>' . get_string('reset_assignments', 'mod_zoomattendance') . '</h4>';
echo '<a class="btn btn-primary" href="#">' . get_string('reset_assignments', 'mod_zoomattendance') . '</a></div>';
echo '</div>';

// ========== FILTERS & EXPORT ==========
$filter = optional_param('filter', 'all', PARAM_ALPHA);
$sort_by = optional_param('sort', 'lastname', PARAM_ALPHA);
$sort_dir = optional_param('dir', 'asc', PARAM_ALPHA);

// Valida parametri
if (!in_array($sort_by, ['lastname', 'firstname', 'percentage', 'duration'])) {
    $sort_by = 'lastname';
}
if (!in_array($sort_dir, ['asc', 'desc'])) {
    $sort_dir = 'asc';
}

echo '<div class="filters-export-container" style="display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-bottom: 20px;">';

// FILTRI (SINISTRA)
echo '<div class="filters-section">';
echo '<p style="font-size: 0.95em; color: #666; margin-bottom: 10px;">' . get_string('filter_table_by_type', 'mod_zoomattendance') . '</p>';
echo '<div class="filters mb-3">';
echo '<a class="btn btn-light" href="?id='.$cm->id.'&filter=all&sort='.$sort_by.'&dir='.$sort_dir.'">' . get_string('filter_all', 'mod_zoomattendance') . '</a> ';
echo '<a class="btn btn-warning" href="?id='.$cm->id.'&filter=unassigned&sort='.$sort_by.'&dir='.$sort_dir.'">' . get_string('filter_unassigned', 'mod_zoomattendance') . '</a> ';
echo '<a class="btn btn-success" href="?id='.$cm->id.'&filter=assigned&sort='.$sort_by.'&dir='.$sort_dir.'">' . get_string('filter_assigned', 'mod_zoomattendance') . '</a>';
echo '</div></div>';

// EXPORT (DESTRA)
echo '<div class="export-section" style="flex-shrink: 0;">';
echo '<div class="export-buttons-container" style="display: flex; gap: 10px;">';
echo '<a class="btn btn-secondary" href="' . new moodle_url('/mod/zoomattendance/export.php', ['id' => $cm->id, 'format' => 'csv']) . '">' . get_string('export_csv', 'mod_zoomattendance') . '</a>';
echo '<a class="btn btn-secondary" href="' . new moodle_url('/mod/zoomattendance/export.php', ['id' => $cm->id, 'format' => 'xlsx']) . '">' . get_string('export_excel', 'mod_zoomattendance') . '</a>';
echo '</div></div></div>';

// ========== CARICA RECORDS ==========
switch ($filter) {
    case 'unassigned':
        $records = $DB->get_records_select('zoomattendance_data', 'sessionid = ? AND userid = 0', [$session->id]);
        break;
    case 'assigned':
        $records = $DB->get_records_select('zoomattendance_data', 'sessionid = ? AND userid > 0', [$session->id]);
        break;
    case 'all':
    default:
        $records = $DB->get_records('zoomattendance_data', ['sessionid' => $session->id]);
        break;
}

// ========== ORDINA RECORDS ==========
$records_array = array_values($records);
$threshold = (int)$session->required_attendance;
$expected_duration = $session->end_datetime - $session->start_datetime;

usort($records_array, function($a, $b) use ($sort_by, $sort_dir, $DB, $expected_duration) {
    $user_a = $a->userid ? $DB->get_record('user', ['id' => $a->userid]) : false;
    $user_b = $b->userid ? $DB->get_record('user', ['id' => $b->userid]) : false;
    
    $val_a = 0;
    $val_b = 0;
    
    if ($sort_by === 'lastname') {
        $val_a = $user_a ? $user_a->lastname : '';
        $val_b = $user_b ? $user_b->lastname : '';
    } elseif ($sort_by === 'firstname') {
        $val_a = $user_a ? $user_a->firstname : '';
        $val_b = $user_b ? $user_b->firstname : '';
    } elseif ($sort_by === 'idnumber') {
        $val_a = $user_a ? $user_a->idnumber : '';
        $val_b = $user_b ? $user_b->idnumber : '';
    } elseif ($sort_by === 'teams_user') {
        $val_a = $a->name;
        $val_b = $b->name;
    } elseif ($sort_by === 'assignment') {
        $val_a = $a->manually_assigned ? 1 : ($a->userid ? 0 : 2);
        $val_b = $b->manually_assigned ? 1 : ($b->userid ? 0 : 2);
    } elseif ($sort_by === 'percentage') {
        $val_a = $expected_duration > 0 ? round(($a->attendance_duration / $expected_duration) * 100) : 0;
        $val_b = $expected_duration > 0 ? round(($b->attendance_duration / $expected_duration) * 100) : 0;
    } elseif ($sort_by === 'duration') {
        $val_a = $a->attendance_duration;
        $val_b = $b->attendance_duration;
    } elseif ($sort_by === 'sufficient') {
        $threshold = (int)$a->sessionid; // Placeholder, usa sempre threshold globale
        $val_a = $expected_duration > 0 ? round(($a->attendance_duration / $expected_duration) * 100) : 0;
        $val_b = $expected_duration > 0 ? round(($b->attendance_duration / $expected_duration) * 100) : 0;
    }
    
    if ($sort_dir === 'asc') {
        return ($val_a < $val_b) ? -1 : (($val_a > $val_b) ? 1 : 0);
    } else {
        return ($val_a > $val_b) ? -1 : (($val_a < $val_b) ? 1 : 0);
    }
});



// ========== TABELLA ==========
echo '<table class="table table-hover">';
echo '<thead><tr style="vertical-align: top;">';

// Funzione per colonne ORDINABILI
$make_sort_link = function($col, $label) use ($cm, $sort_by, $sort_dir, $filter) {
    $new_dir = ($sort_by === $col && $sort_dir === 'asc') ? 'desc' : 'asc';
    // Icone: ▼▲ (sovrapposti) / ▲ (ascendente) / ▼ (discendente)
    $icon = ($sort_by === $col) ? ($sort_dir === 'asc' ? ' ▲' : ' ▼') : ' ▼<span style="margin-left: -5px; display: inline-block;">▲</span>';
    $url = new moodle_url('view.php', ['id' => $cm->id, 'filter' => $filter, 'sort' => $col, 'dir' => $new_dir]);
    return '<a href="' . $url . '" style="text-decoration: none; color: inherit; cursor: pointer;">' . $label . $icon . '</a>';
};

echo '<th style="vertical-align: top;">' . $make_sort_link('lastname', get_string('cognome', 'mod_zoomattendance')) . '</th>';
echo '<th style="vertical-align: top;">' . $make_sort_link('firstname', get_string('nome', 'mod_zoomattendance')) . '</th>';
echo '<th style="vertical-align: top;">' . get_string('idnumber', 'mod_zoomattendance') . '</th>';
echo '<th style="vertical-align: top;">' . get_string('teams_user', 'mod_zoomattendance') . '</th>';
echo '<th style="vertical-align: top;">' . get_string('assignment_type', 'mod_zoomattendance') . '</th>';
echo '<th style="vertical-align: top;">' . $make_sort_link('duration', get_string('duration_participation', 'mod_zoomattendance')) . '</th>';
echo '<th style="vertical-align: top;">' . $make_sort_link('percentage', get_string('attendance_percentage', 'mod_zoomattendance')) . '</th>';
echo '<th style="vertical-align: top;">' . get_string('minimum_threshold', 'mod_zoomattendance') . '</th>';
echo '</tr></thead><tbody>';

// ========== LOOP DATI ==========
foreach ($records_array as $record) {
    $user = $record->userid ? $DB->get_record('user', ['id' => $record->userid]) : false;
    $lastname = $user ? $user->lastname : '';
    $firstname = $user ? $user->firstname : '';
    $idnumber = $user ? $user->idnumber : '';
    
    $row_class = $record->manually_assigned == 1 ? 'manual-assignment' : ($record->userid ? 'automatic-assignment' : 'unassigned-row');
    
    $badge = $record->manually_assigned == 1 ?
        '<span class="badge badge-warning">' . get_string('manual', 'mod_zoomattendance') . '</span>' :
        ($record->userid ? '<span class="badge badge-success">' . get_string('automatic', 'mod_zoomattendance') . '</span>' : '<span class="badge badge-warning">' . get_string('type_unassigned', 'mod_zoomattendance') . '</span>');

    $percentage = $expected_duration > 0 ? round(($record->attendance_duration / $expected_duration) * 100) : 0;
    $is_sufficient = $percentage >= $threshold;
    
    if ($is_sufficient && $record->userid) {
        $row_class = 'sufficient-attendance';
    }
    
    $percentage_class = $is_sufficient ? 'percentage-sufficient' : 'percentage-insufficient';
    
    $suffix_icon = $is_sufficient ? 
        '<i class="fa fa-check-circle" style="color: #0d3818; font-size: 1.2em;"></i>' :
        '<i class="fa fa-times-circle" style="color: #d32f2f; font-size: 1.2em;"></i>';
    
    $duration_formatted = format_duration($record->attendance_duration);

    echo "<tr class=\"$row_class\">
        <td>$lastname</td>
        <td>$firstname</td>
        <td>$idnumber</td>
        <td>{$record->name}</td>
        <td>$badge</td>
        <td>$duration_formatted</td>
        <td><span class=\"$percentage_class\">$percentage%</span></td>
        <td>$suffix_icon " . ($is_sufficient ? 'Sì' : 'No') . "</td>
    </tr>";
}

echo '</tbody></table>';
echo '</div>';
?>

<script>
document.getElementById('fetch-btn').addEventListener('click', function(e) {
    e.preventDefault();
    this.disabled = true;
    this.textContent = '<?php echo get_string('loading', 'core'); ?>';
    
    fetch('<?php echo new moodle_url('/mod/zoomattendance/fetch_attendance.php', ['id' => $cm->id]); ?>')
        .then(r => r.json())
        .then(d => {
            alert(d.message || d.error);
            if (d.success) location.reload();
            else this.disabled = false;
        })
        .catch(e => {
            alert('Error: ' + e);
            this.disabled = false;
        });
});
</script>

<?php
echo $OUTPUT->footer();
