<?php
// This file defines the restore task for the Teams Meeting Attendance plugin.

require_once($CFG->dirroot . '/mod/zoomattendance/backup/moodle2/restore_zoomattendance_stepslib.php');

class restore_zoomattendance_activity_task extends restore_activity_task {

    /**
     * Define the settings for the restore task.
     */
    protected function define_my_settings() {
        // No specific settings for this activity.
    }

    /**
     * Define the steps for the restore task.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_zoomattendance_activity_structure_step('zoomattendance_structure', 'zoomattendance.xml'));
    }

    /**
     * Define the contents to be processed by the restore task.
     *
     * @return array The contents to process.
     */
    public static function define_decode_contents() {
        return [];
    }

    /**
     * Define the links to be decoded by the restore task.
     *
     * @return array The links to decode.
     */
    public static function define_decode_rules() {
        return [];
    }
}
