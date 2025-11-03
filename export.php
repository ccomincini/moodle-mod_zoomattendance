<?php
define('AJAX_SCRIPT', true);
require('../../config.php');

while (ob_get_level()) {
    ob_end_clean();
}

$id = optional_param('id', 0, PARAM_INT);
$format = optional_param('format', 'csv', PARAM_ALPHA);

if (!$id || !in_array($format, ['csv', 'xlsx'])) {
    http_response_code(400);
    die('Invalid parameters');
}

try {
    list($course, $cm) = get_course_and_cm_from_cmid($id, 'zoomattendance');
    require_login($course, true, $cm);
    
    $context = context_module::instance($cm->id);
    require_capability('mod/zoomattendance:view', $context);

    global $DB;
    $session = $DB->get_record('zoomattendance', ['id' => $cm->instance], '*', MUST_EXIST);
    $records = $DB->get_records('zoomattendance_data', ['sessionid' => $session->id]);

    function format_duration($seconds) {
        $hours = floor($seconds / 3600);
        $remaining = $seconds % 3600;
        $minutes = floor($remaining / 60);
        $secs = $remaining % 60;
        if ($secs >= 30) $minutes++;
        if ($minutes >= 60) { $hours++; $minutes = 0; }
        return sprintf('%dh%dm', $hours, $minutes);
    }

    $threshold = (int)$session->required_attendance;
    $expected_duration = $session->end_datetime - $session->start_datetime;
    
    $data = [];
    foreach ($records as $record) {
        $user = $record->userid ? $DB->get_record('user', ['id' => $record->userid]) : false;
        $percentage = $expected_duration > 0 ? round(($record->attendance_duration / $expected_duration) * 100) : 0;
        $is_sufficient = $percentage >= $threshold;
        
        $data[] = [
            'Cognome' => $user ? $user->lastname : '',
            'Nome' => $user ? $user->firstname : '',
            'ID Number' => $user ? $user->idnumber : '',
            'Utente Zoom' => $record->name,
            'Tipo' => $record->manually_assigned ? 'Manuale' : ($record->userid ? 'Automatico' : 'Non assegnato'),
            'Durata' => format_duration($record->attendance_duration),
            'Percentuale' => $percentage . '%',
            'Sufficiente' => $is_sufficient ? 'Sì' : 'No'
        ];
    }

    if (empty($data)) {
        http_response_code(204);
        die('No data to export');
    }

    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="attendance_' . $session->id . '_' . date('Y-m-d_H-i-s') . '.csv"');
        echo "\xEF\xBB\xBF";
        $output = fopen('php://output', 'w');
        fputcsv($output, array_keys($data[0]), ';');
        foreach ($data as $row) {
            fputcsv($output, $row, ';');
        }
        fclose($output);
    } 
    elseif ($format === 'xlsx') {
        // Percorso CORRETTO al PHPOffice dentro il plugin
        $autoload_path = __DIR__ . '/phpoffice/autoload.php';
        
        if (!file_exists($autoload_path)) {
            throw new Exception('PHPOffice not found at: ' . $autoload_path);
        }
        
        require_once($autoload_path);
        
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Attendance');
        
        // Header
        $headers = array_keys($data[0]);
        foreach ($headers as $col => $header) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
        }
        
        // Data
        $row = 2;
        foreach ($data as $record) {
            $col = 1;
            foreach ($record as $value) {
                $sheet->setCellValueByColumnAndRow($col, $row, $value);
                $col++;
            }
            $row++;
        }
        
        // Auto-width columns
        foreach ($sheet->getColumnIterator() as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="attendance_' . $session->id . '_' . date('Y-m-d_H-i-s') . '.xlsx"');
        $writer->save('php://output');
    }
    
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain');
    die('Error: ' . $e->getMessage());
}
