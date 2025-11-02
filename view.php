<?php
require('../../config.php');

$id = required_param('id', PARAM_INT);
list($course, $cm) = get_course_and_cm_from_cmid($id, 'zoomattendance');
require_login($course, true, $cm);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['assign_record_id']) && !empty($_POST['assign_userid'])) {
    $rid = (int)$_POST['assign_record_id'];
    $uid = (int)$_POST['assign_userid'];
    $rec = $DB->get_record('zoomattendance_data', ['id' => $rid]);
    if ($rec && $rec->userid == 0) {
        $rec->userid = $uid;
        $rec->manually_assigned = 1;
        $DB->update_record('zoomattendance_data', $rec);
        redirect(new moodle_url('/mod/zoomattendance/view.php', ['id' => $cm->id, 'filter' => 'unassigned']));
    }
}

$context = context_module::instance($cm->id);
require_capability('mod/zoomattendance:view', $context);

$PAGE->set_url('/mod/zoomattendance/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($cm->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->requires->css('/mod/zoomattendance/styles/view_attendance.css');

echo $OUTPUT->header();
echo '<div class="mod_zoomattendance">';

$session = $DB->get_record('zoomattendance', ['id' => $cm->instance], '*', MUST_EXIST);
$filter = optional_param('filter', 'all', PARAM_ALPHA);

$automatic_records = $DB->count_records_select('zoomattendance_data', 'sessionid = ? AND userid > 0 AND manually_assigned = 0', [$session->id]);
$manual_records = $DB->count_records_select('zoomattendance_data', 'sessionid = ? AND userid > 0 AND manually_assigned = 1', [$session->id]);
$unassigned_count = $DB->count_records_select('zoomattendance_data', 'sessionid = ? AND userid = 0', [$session->id]);
$total_records = $automatic_records + $manual_records + $unassigned_count;

echo '<div class="stats-container">';
echo '<div class="stats-card card-total">';
echo '<h4>' . get_string('total_records', 'mod_zoomattendance') . '</h4>';
echo '<div class="metric">' . $total_records . '</div>';
echo '</div>';
echo '<div class="stats-card card-automatic">';
echo '<h4>' . get_string('automatic_assignments', 'mod_zoomattendance') . '</h4>';
echo '<div class="metric">' . $automatic_records . '</div>';
echo '</div>';
echo '<div class="stats-card card-manual">';
echo '<h4>' . get_string('manual_assignments', 'mod_zoomattendance') . '</h4>';
echo '<div class="metric">' . $manual_records . '</div>';
echo '</div>';
echo '<div class="stats-card card-unassigned">';
echo '<h4>' . get_string('unassigned_records', 'mod_zoomattendance') . '</h4>';
echo '<div class="metric">' . $unassigned_count . '</div>';
echo '</div>';
echo '</div>';

echo '<div class="action-buttons-container">';
echo '<div class="action-card card-fetch">';
echo '<h4>' . get_string('fetch_zoom_data', 'mod_zoomattendance') . '</h4>';
echo '<a class="btn btn-secondary" href="/mod/zoomattendance/fetch_attendance.php?id=' . $cm->id . '">' . get_string('fetch_zoom_data', 'mod_zoomattendance') . '</a>';
echo '</div>';
echo '<div class="action-card card-manage">';
echo '<h4>' . get_string('manage_unassigned', 'mod_zoomattendance') . '</h4>';
echo '<a class="btn btn-warning" href="/mod/zoomattendance/manage_unassigned.php?id=' . $cm->id . '">' . get_string('manage_unassigned', 'mod_zoomattendance') . '</a>';
echo '</div>';
echo '<div class="action-card card-reset">';
echo '<h4>' . get_string('reset_assignments', 'mod_zoomattendance') . '</h4>';
echo '<a class="btn btn-primary" href="#">' . get_string('reset_assignments', 'mod_zoomattendance') . '</a>';
echo '</div>';
echo '</div>';

echo '<div class="export-buttons-container">';
echo '<a class="btn btn-secondary" href="#">' . get_string('export_csv', 'mod_zoomattendance') . '</a>';
echo '<a class="btn btn-secondary" href="#">' . get_string('export_excel', 'mod_zoomattendance') . '</a>';
echo '</div>';

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
echo '<div class="filters mb-3">';
echo '<a class="btn btn-light" href="?id='.$cm->id.'&filter=all">' . get_string('filter_all', 'mod_zoomattendance') . '</a> ';
echo '<a class="btn btn-warning" href="?id='.$cm->id.'&filter=unassigned">' . get_string('filter_unassigned', 'mod_zoomattendance') . '</a> ';
echo '<a class="btn btn-success" href="?id='.$cm->id.'&filter=assigned">' . get_string('filter_assigned', 'mod_zoomattendance') . '</a>';
echo '</div>';
echo '<table class="table table-striped table-hover">';
echo '<thead><tr>
<th>' . get_string('cognome', 'mod_zoomattendance') . '</th>
<th>' . get_string('nome', 'mod_zoomattendance') . '</th>
<th>' . get_string('idnumber', 'mod_zoomattendance') . '</th>
<th>' . get_string('teams_user', 'mod_zoomattendance') . '</th>
<th>Join</th>
<th>Leave</th>
<th>' . get_string('assignment_type', 'mod_zoomattendance') . '</th>
<th>' . get_string('actions', 'mod_zoomattendance') . '</th>
</tr></thead><tbody>';
foreach ($records as $record) {
    $user = $record->userid ? $DB->get_record('user', ['id' => $record->userid]) : false;
    $lastname = $user ? $user->lastname : '';
    $firstname = $user ? $user->firstname : '';
    $idnumber = $user ? $user->idnumber : '';
    $row_class = $record->manually_assigned == 1 ? 'manual-assignment' : 'automatic-assignment';
    $badge = $record->manually_assigned == 1 ?
        '<span class="badge badge-warning" title="' . get_string('manually_assigned_tooltip', 'mod_zoomattendance') . '">' . get_string('manual', 'mod_zoomattendance') . '</span>' :
        ($record->userid ? '<span class="badge badge-success" title="' . get_string('automatically_assigned_tooltip', 'mod_zoomattendance') . '">' . get_string('automatic', 'mod_zoomattendance') . '</span>' : '<span class="badge badge-warning">' . get_string('type_unassigned', 'mod_zoomattendance') . '</span>');

    echo "<tr class=\"$row_class\">
        <td>$lastname</td>
        <td>$firstname</td>
        <td>$idnumber</td>
        <td>{$record->name}</td>
        <td>" . userdate($record->join_time) . "</td>
        <td>" . userdate($record->leave_time) . "</td>
        <td>$badge</td>";

    // Azioni vuote per ora, gestite in manage_unassigned.php 
    echo '<td></td>';

    echo '</tr>';
}

echo '</tbody></table>';

echo '</div>';
echo $OUTPUT->footer();
