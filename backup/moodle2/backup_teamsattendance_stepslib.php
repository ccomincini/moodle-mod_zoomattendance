<?php
// This file defines the structure of the data to be backed up for the Teams Meeting Attendance plugin.

class backup_zoomattendance_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure of the backup data.
     *
     * @return backup_nested_element The root element of the structure.
     */
    protected function define_structure() {
        // Define the root element describing the zoomattendance instance.
        $zoomattendance = new backup_nested_element('zoomattendance', ['id'], [
            'name', 'intro', 'introformat', 'meetingurl', 'organizer_email',
            'expected_duration', 'required_attendance',
            'status', 'timecreated', 'timemodified'
        ]);

        // Define data sources.
        $zoomattendance->set_source_table('zoomattendance', ['id' => backup::VAR_ACTIVITYID]);

        // Define attendance records
        $attendance = new backup_nested_element('attendance', ['id'], [
            'userid', 'attendance_duration', 'actual_attendance', 'completion_met'
        ]);

        // Add attendance records as child elements
        $zoomattendance->add_child($attendance);
        $attendance->set_source_table('zoomattendance', ['sessionid' => backup::VAR_PARENTID]);

        // Return the root element wrapped into the standard activity structure.
        return $this->prepare_activity_structure($zoomattendance);
    }
}
