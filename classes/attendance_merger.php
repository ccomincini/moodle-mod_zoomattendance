<?php
namespace mod_zoomattendance;

defined('MOODLE_INTERNAL') || die();

class attendance_merger {
    private $zoomattendance;
    private $interval_merger;
    
    public function __construct($zoomattendance) {
        $this->zoomattendance = $zoomattendance;
        $this->interval_merger = new interval_merger();
    }
    
    /**
     * Consolida record duplicati dello stesso utente per una sessione.
     * Usa interval_merger per eliminare sovrapposizioni.
     */
    public function consolidate_duplicates($userid, $sessionid) {
        global $DB;
        
        $duplicates = $DB->get_records('zoomattendance_data', [
            'userid' => $userid,
            'sessionid' => $sessionid
        ], 'id ASC');
        
        if (count($duplicates) <= 1) {
            return 0;
        }
        
        // Recupera i report associati per ogni record
        $all_intervals = [];
        $keep_record_id = null;
        
        foreach ($duplicates as $rec) {
            if ($keep_record_id === null) {
                $keep_record_id = $rec->id;
            }
            
            // Leggi i report associati
            $reports = $DB->get_records('zoomattendance_reports', ['data_id' => $rec->id]);
            foreach ($reports as $report) {
                $all_intervals[] = [
                    'join_time' => (int)$report->join_time,
                    'leave_time' => (int)$report->leave_time
                ];
            }
        }
        
        // Calcola durata consolidata senza sovrapposizioni
        $mergedduration = $this->clip_and_sum_intervals(
            $allintervals, 
            $this->zoomattendance->startdatetime, 
            $this->zoomattendance->enddatetime
        );

        
        // Aggiorna il record da mantenere
        $keep_record = new \stdClass();
        $keep_record->id = $keep_record_id;
        $keep_record->attendance_duration = $merged_duration;
        $DB->update_record('zoomattendance_data', $keep_record);
        
        // Elimina i duplicati
        $deleted = 0;
        foreach ($duplicates as $rec) {
            if ($rec->id !== $keep_record_id) {
                $DB->delete_records('zoomattendance_data', ['id' => $rec->id]);
                $DB->delete_records('zoomattendance_reports', ['data_id' => $rec->id]);
                $deleted++;
            }
        }
        
        return $deleted;
    }
    
    /**
     * Limita e somma gli intervalli di presenza all'interno del range registro (timestamp in secondi).
     * @param array $intervals [[join, leave], ...]
     * @param int $registro_start
     * @param int $registro_end
     * @return int seconds in range
     */
    private function clip_and_sum_intervals(array $intervals, int $registro_start, int $registro_end): int {
        // Applica il clipping e merge agli intervalli come da patch discussa.
        $clipped = [];
        foreach ($intervals as $ival) {
            $join = max($ival[0], $registro_start);
            $leave = min($ival[1], $registro_end);
            if ($leave > $join) $clipped[] = [$join, $leave];
        }
        usort($clipped, function($a, $b) { return $a[0]-$b[0]; });
        $merged = [];
        foreach ($clipped as $c) {
            if (empty($merged) || $c[0] > $merged[count($merged)-1][1]) $merged[] = $c;
            else $merged[count($merged)-1][1] = max($merged[count($merged)-1][1], $c[1]);
        }
        $total = 0;
        foreach ($merged as $m) $total += ($m[1]-$m[0]);
        return $total;
    }

}
?>
