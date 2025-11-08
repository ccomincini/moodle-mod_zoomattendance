<?php
namespace mod_zoomattendance;

defined('MOODLE_INTERNAL') || die();

class zoom_report_handler {

    private $session;
    private $merger;
    private $logfile = './zoomlog.txt';

    public function __construct($session) {
        $this->session = $session;
        $this->merger = new interval_merger();
    }

    /**
     * Recupera i partecipanti da Zoom DB tramite il meeting_id del registro.
     * Raccoglie tutti i partecipanti su tutte le occorrenze di meeting Zoom collegato.
     */
    public function fetch_participants_raw() {
        global $DB;

        try {
            $details = $DB->get_records('zoom_meeting_details', ['meeting_id' => $this->session->meeting_id]);
            if (empty($details)) {
                throw new \Exception('No meeting details found for meeting_id: ' . $this->session->meeting_id);
            }
            $this->log("Found " . count($details) . " meeting details");

            $all_participants = [];
            foreach ($details as $detail) {
                $participants = $DB->get_records('zoom_meeting_participants', ['detailsid' => $detail->id]);
                $all_participants = array_merge($all_participants, $participants);
            }
            $this->log("Fetch from mod_zoom DB: " . count($all_participants) . " total participant records");
            return $all_participants;
        } catch (\Throwable $e) {
            $this->log("ERROR fetching participants: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Aggrega partecipanti Zoom, clippa intervalli su finestra registro.
     * @param array $participants
     * @return array
     */
    public function aggregate_participants($participants) {
        $grouped = [];
        $registro_start = $this->session->start_datetime;
        $registro_end = $this->session->end_datetime;

        foreach ($participants as $p) {
            $email = strtolower(trim($p->user_email ?? ''));
            $key = !empty($email) ? $email : strtolower(trim($p->name ?? 'unknown'));

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'name' => $p->name ?? 'Unknown',
                    'user_email' => $p->user_email ?? null,
                    'intervals' => []
                ];
            }

            // Solo sessioni sovrapposte alla finestra registro
            if ($p->join_time > 0 && $p->leave_time > 0 &&
                $p->leave_time > $registro_start && $p->join_time < $registro_end) {

                $clip_join = max((int)$p->join_time, $registro_start);
                $clip_leave = min((int)$p->leave_time, $registro_end);

                if ($clip_leave > $clip_join) {
                    $grouped[$key]['intervals'][] = [
                        'join_time' => $clip_join,
                        'leave_time' => $clip_leave
                    ];
                }
            }
        }
        foreach ($grouped as $key => $data) {
            $this->log("AGGREGATED | USER: $key | INTERVALS: " . json_encode($data['intervals']));
        }
        return $grouped;
    }

    /**
     * Calcola la durata aggregata e validata sulla finestra registro per ciascun partecipante.
     * @param array $aggregated
     * @return array
     */
    public function calculate_durations($aggregated) {
        $result = [];
        $registro_start = $this->session->start_datetime;
        $registro_end = $this->session->end_datetime;

        foreach ($aggregated as $key => $data) {
            $total_duration = $this->merger->total_for_range(
                $data['intervals'],
                $registro_start,
                $registro_end
            );
            $result[] = (object)[
                'email_key' => $key,
                'name' => $data['name'],
                'user_email' => $data['user_email'],
                'total_duration' => $total_duration
            ];
            $this->log("DURATION | USER: $key | TOTAL: $total_duration seconds");
        }

        return $result;
    }

    /**
     * Workflow completo: trova, aggrega e calcola presenza.
     */
    public function process() {
        $this->log("\n=== PROCESS START: Session {$this->session->id}, Meeting {$this->session->meeting_id} ===");

        $participants = $this->fetch_participants_raw();
        $aggregated = $this->aggregate_participants($participants);
        $result = $this->calculate_durations($aggregated);

        $this->log("=== PROCESS COMPLETE: " . count($result) . " users ===\n");
        return $result;
    }

    private function log($message) {
        file_put_contents($this->logfile, date('Y-m-d H:i:s') . " | " . $message . "\n", FILE_APPEND);
    }
}
