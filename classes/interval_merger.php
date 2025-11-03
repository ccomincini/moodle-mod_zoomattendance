<?php
namespace mod_zoomattendance;

defined('MOODLE_INTERNAL') || die();

/**
 * Merge di intervalli e clipping su finestra registro.
 * Gestisce sovrappositioni di login da più dispositivi.
 *
 * @package    mod_zoomattendance
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class interval_merger {
    
    /**
     * Calcola i secondi totali di presenza non sovrapposta, clippata al range.
     * @param array $records array di ['join_time'=>int, 'leave_time'=>int]
     * @param int $rangestart timestamp inizio registro
     * @param int $rangeend timestamp fine registro
     * @return int secondi totali
     */
    public function total_for_range(array $records, int $rangestart, int $rangeend): int {
        if ($rangestart >= $rangeend || empty($records)) {
            return 0;
        }
        $merged = $this->merge($records);
        return $this->clip_and_sum($merged, $rangestart, $rangeend);
    }

    /**
     * Merge di intervalli sovrapposti/adiacenti.
     * @param array $records array di ['join_time'=>int,'leave_time'=>int]
     * @return array intervalli merged [['start'=>int,'end'=>int], ...]
     */
    public function merge(array $records): array {
        $normalized = [];
        
        // Normalizza e valida i record
        foreach ($records as $r) {
            if (!isset($r['join_time'], $r['leave_time'])) {
                continue;
            }
            $start = (int)$r['join_time'];
            $end = (int)$r['leave_time'];
            
            if ($start >= $end) {
                continue;
            }
            
            $normalized[] = ['start' => $start, 'end' => $end];
        }
        
        if (empty($normalized)) {
            return [];
        }
        
        // Ordina per start time
        usort($normalized, static function($a, $b) {
            return $a['start'] <=> $b['start'];
        });

        // Merge degli intervalli
        $merged = [];
        $current = $normalized[0];
        
        for ($i = 1, $count = count($normalized); $i < $count; $i++) {
            $next = $normalized[$i];
            
            // Se si sovrappongono o sono adiacenti, fai merge
            if ($next['start'] <= $current['end']) {
                $current['end'] = max($current['end'], $next['end']);
            } else {
                // Gap: salva l'intervallo corrente e inizia uno nuovo
                $merged[] = $current;
                $current = $next;
            }
        }
        
        // Salva l'ultimo intervallo
        $merged[] = $current;
        
        return $merged;
    }

    /**
     * Somma la sola intersezione degli intervalli col range dato.
     * @param array $merged intervalli non sovrapposti [['start'=>int,'end'=>int], ...]
     * @param int $rangestart
     * @param int $rangeend
     * @return int secondi
     */
    public function clip_and_sum(array $merged, int $rangestart, int $rangeend): int {
        $total = 0;
        
        foreach ($merged as $interval) {
            // Clip l'intervallo al range
            $clipped_start = max($interval['start'], $rangestart);
            $clipped_end = min($interval['end'], $rangeend);
            
            // Se c'è sovrapposizione, aggiungi alla somma
            if ($clipped_start < $clipped_end) {
                $total += ($clipped_end - $clipped_start);
            }
        }
        
        return $total;
    }
}
