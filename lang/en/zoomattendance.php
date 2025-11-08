<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * English strings for zoomattendance
 *
 * @package    mod_zoomattendance
 * @copyright  2025 Invisiblefarm srl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin information
$string['pluginname'] = 'Zoom meeting attendance';
$string['pluginadministration'] = 'Zoom meeting attendance administration';
$string['modulename'] = 'Zoom meeting attendance';
$string['modulenameplural'] = 'Zoom meeting attendance';

// Settings strings
$string['settingsheader'] = 'Zoom attendance settings';
$string['settingsheader_desc'] = 'Configure Microsoft Zoom API settings for attendance tracking';
$string['tenantid'] = 'Tenant ID';
$string['tenantid_desc'] = 'Microsoft Azure tenant ID for API authentication';
$string['apiendpoint'] = 'API endpoint';
$string['apiendpoint_desc'] = 'Microsoft Graph API endpoint URL';
$string['apiversion'] = 'API version';
$string['apiversion_desc'] = 'Microsoft Graph API version to use';

// Basic strings
$string['description'] = 'Description';
$string['activityname'] = 'Activity name';
$string['meetingdetails'] = 'Meeting details';
$string['completionsettings'] = 'Completion settings';
$string['minutes'] = 'minutes';

// Meeting configuration
$string['meetingurl'] = 'Zoom meeting URL';
$string['meetingurl_help'] = 'Select the Zoom meeting to track attendance for.';
$string['organizer_email'] = 'Meeting organizer email';
$string['organizer_email_help'] = 'Email address of the person who organized the Zoom meeting. Required to retrieve attendance reports.';
$string['meeting_start_time'] = 'Meeting start time';
$string['meeting_start_time_help'] = 'The start time of the meeting session to filter attendance reports.';
$string['meeting_end_time'] = 'Meeting end time';
$string['meeting_end_time_help'] = 'The end time of the meeting session to filter attendance reports.';
$string['expected_duration'] = 'Expected duration';
$string['expected_duration_help'] = 'The expected duration of the meeting in minutes. Calculated automatically from start and end times.';
$string['required_attendance'] = 'Required attendance (%)';
$string['required_attendance_help'] = 'The minimum attendance percentage required for completion. Students must participate for at least this percentage of the expected meeting duration.';

// Completion
$string['completionattendance'] = 'Student must meet attendance requirement';
$string['completionattendance_help'] = 'If enabled, students must achieve the minimum attendance percentage to complete this activity.';
$string['completionattendance_desc'] = 'Student must achieve the required attendance percentage';

// View page
$string['attendance_register'] = 'Attendance roster';
$string['close_register'] = 'Close attendance roster';
$string['reopen_register'] = 'Reopen attendance roster';
$string['fetch_attendance'] = 'Fetch attendance data';
$string['fetch_warning'] = 'This will retrieve the latest attendance data from Microsoft Zoom. The process may take a few moments.';
$string['last_fetch_time'] = 'Last updated: {$a}';
$string['exporttocsv'] = 'Export to CSV';
$string['exporttoxlsx'] = 'Export to Excel';

// Table headers
$string['cognome'] = 'Last name';
$string['nome'] = 'First name';
$string['idnumber'] = 'ID number';
$string['role'] = 'Role';
$string['tempo_totale'] = 'Total time';
$string['attendance_percentage'] = 'Attendance %';
$string['soglia_raggiunta'] = 'Threshold met';
$string['assignment_type'] = 'Assignment type';
$string['teams_user'] = 'Zoom user';
$string['zoom_participant_name'] = 'Zoom user ID';
$string['attendance_duration'] = 'Attendance duration';
$string['suggested_match'] = 'Suggested match';
$string['assign_user'] = 'Assign user';
$string['actions'] = 'Actions';

// Assignment types
$string['manual'] = 'Manual';
$string['automatic'] = 'Automatic';
$string['manually_assigned_tooltip'] = 'This user has been manually assigned by an administrator';
$string['automatically_assigned_tooltip'] = 'This user has been automatically associated based on email address';

// Unassigned management
$string['unassigned_records'] = 'Unassigned records';
$string['manage_unassigned'] = 'Manage unassigned records';
$string['manage_manual_assignments'] = 'Manage manual assignments';
$string['no_unassigned'] = 'All attendance records have been assigned to users.';
$string['unassigned_users_alert'] = 'There are {$a} unassigned attendance records that need manual review.';

// Performance strings
$string['total_records'] = 'Total records';
$string['performance_level'] = 'Performance level';
$string['recommended_page_size'] = 'Recommended page size';
$string['available_users'] = 'Available users';
$string['for_assignment'] = 'for assignment';
$string['estimated_time'] = 'Estimated time';
$string['for_suggestions'] = 'for suggestions';
$string['filter_by'] = 'Filter by';
$string['filter_all'] = 'All records';
$string['all_records'] = 'All records';
$string['filter_name_suggestions'] = 'Name-based suggestions';
$string['filter_email_suggestions'] = 'Email-based suggestions';
$string['with_suggestions'] = 'With suggestions';
$string['without_suggestions'] = 'Without suggestions';
$string['filter_long_duration'] = 'Long duration sessions';
$string['records_per_page'] = 'Records per page';
$string['advanced_users'] = 'Advanced users only';
$string['refresh'] = 'Refresh';
$string['apply_selected'] = 'Apply selected';
$string['bulk_assignment_progress'] = 'Bulk assignment progress';
$string['loading_initial_data'] = 'Loading initial data';
$string['loading'] = 'Loading';
$string['applying'] = 'Applying';
$string['page'] = 'Page';
$string['of'] = 'of';
$string['previous'] = 'Previous';
$string['next'] = 'Next';
$string['no_records_found'] = 'No records found';

// Performance levels
$string['performance_excellent'] = 'Excellent performance expected';
$string['performance_good'] = 'Good performance expected';
$string['performance_moderate'] = 'Moderate performance - consider using filters';
$string['performance_challenging'] = 'Large dataset - use pagination and filters for better performance';

// Suggestions system
$string['suggestions_found'] = '{$a} automatic matching suggestions found based on names';
$string['suggestions_summary'] = 'Found {$a->total} total suggestions: {$a->name_matches} based on name similarity, {$a->email_matches} based on email patterns';
$string['name_match_suggestion'] = 'Suggested match based on name similarity';
$string['email_match_suggestion'] = 'Suggested match based on email pattern';
$string['no_suggestion'] = 'No automatic suggestion';
$string['apply_suggestion'] = 'Apply this suggestion';
$string['apply_selected_suggestions'] = 'Apply selected suggestions';
$string['bulk_assignments_applied'] = '{$a} assignments have been applied successfully.';
$string['no_assignments_applied'] = 'No assignments were applied.';

// Color legend
$string['color_legend'] = 'Color legend';
$string['name_based_matches'] = 'Name-based suggestions';
$string['email_based_matches'] = 'Email-based suggestions';
$string['suggested_matches'] = 'Suggested matches';
$string['no_matches'] = 'No automatic matches';
$string['name_suggestions_count'] = 'Name-based suggestions';
$string['email_suggestions_count'] = 'Email-based suggestions';

// User assignment
$string['select_user'] = 'Select user...';
$string['assign'] = 'Assign';
$string['user_assigned'] = 'User has been assigned successfully.';
$string['user_assignment_failed'] = 'User assignment failed. Please try again.';

// JavaScript messages
$string['select_user_first'] = 'Please select a user first.';
$string['confirm_assignment'] = 'Are you sure you want to assign this record to {user}?';
$string['select_suggestions_first'] = 'Please select at least one suggestion to apply.';
$string['confirm_bulk_assignment'] = 'Are you sure you want to apply {count} selected suggestions?';

// Error messages
$string['meetingurl_required'] = 'Zoom meeting URL is required.';
$string['invalid_meetingurl'] = 'Please enter a valid Zoom meeting URL.';
$string['organizer_email_required'] = 'Meeting organizer email is required.';
$string['invalid_email'] = 'Please enter a valid email address.';
$string['meeting_start_time_required'] = 'Meeting start time is required.';
$string['meeting_end_time_required'] = 'Meeting end time is required.';
$string['end_time_after_start'] = 'End time must be after start time.';
$string['invalid_meeting_duration'] = 'Invalid meeting duration.';
$string['required_attendance_error'] = 'Required attendance must be between 0 and 100 percent.';

// Help strings
$string['required_attendance_help'] = 'Enter the minimum attendance percentage required for students to complete this activity. Value must be between 0 and 100.';
$string['expected_duration_help'] = 'This field shows the expected meeting duration in minutes, automatically calculated from the start and end times set above.';
$string['meetingurl_help'] = 'Select the Zoom meeting from available meetings in this course. If no meetings are available, you must first create a Zoom meeting activity.';
$string['organizer_email_help'] = 'Enter the email address of the person who organized the Zoom meeting. This email is used to authenticate with the Microsoft Zoom API and retrieve attendance reports.';
$string['meeting_start_time_help'] = 'Set the start time for this meeting session. This will be used to filter attendance reports to include only participants in this time range.';
$string['meeting_end_time_help'] = 'Set the end time for this meeting session. This will be used to filter attendance reports to include only participants in this time range.';
$string['completionattendance_help'] = 'If enabled, students will need to achieve the minimum attendance percentage specified above to mark this activity as completed.';

// API and system messages
$string['missingapicredentials'] = 'Microsoft Graph API credentials are missing. Please configure the auth_oidc plugin.';
$string['missingtenantid'] = 'Tenant ID is missing. Please configure it in plugin settings.';
$string['invalidaccesstoken'] = 'Failed to obtain a valid access token from Microsoft Graph API.';
$string['sessionnotfound'] = 'Zoom attendance session not found.';
$string['invalidattendanceformat'] = 'Invalid attendance data format received from Microsoft Zoom API.';
$string['attendancefetchfailed'] = 'Failed to fetch attendance data from Microsoft Zoom.';
$string['fetch_attendance_success'] = 'Attendance data has been successfully retrieved from Microsoft Zoom.';

// Completion descriptions
$string['completionattendance_desc'] = 'Student must achieve the required attendance percentage';

// Capabilities
$string['zoomattendance:view'] = 'View Zoom attendance reports';
$string['zoomattendance:manageattendance'] = 'Manage Zoom attendance data';
$string['zoomattendance:addinstance'] = 'Add Zoom attendance activity';

// Reset automatic assignments
$string['automatic_assignments_info'] = '{$a} records associated based on suggestions.';
$string['reset_automatic_assignments'] = 'Reset all suggestion-based assignments';
$string['confirm_reset_automatic'] = 'Are you sure you want to reset all suggestion-based associations? All reset associations will need to be made manually again.';
$string['automatic_assignments_reset'] = '{$a} automatic assignments reset.';

$string['manual_assignments_info'] = '{$a} manual assignments found.';
$string['reset_manual_assignments'] = 'Reset manual assignments';
$string['confirm_reset_manual_assignments'] = 'Are you sure you want to reset all manual assignments?';

$string['potential_suggestions_info'] = 'There are {$a} manual associations that match current automatic suggestions';
$string['reset_suggestion_assignments'] = 'Reset suggestion-based associations';
$string['confirm_reset_suggestions'] = 'Reset associations that match automatic suggestions?';
$string['suggestion_assignments_reset'] = 'Reset {$a} suggestion-based associations';

//Privacy
$string['privacy:metadata'] = 'The Zoom meeting attendance plugin stores attendance data retrieved from Microsoft Zoom.';
$string['privacy:metadata:zoomattendance_data'] = 'Attendance records for Zoom meetings';
$string['privacy:metadata:zoomattendance_data:userid'] = 'The user ID';
$string['privacy:metadata:zoomattendance_data:attendance_duration'] = 'Duration of attendance in the meeting';
$string['privacy:metadata:zoomattendance_data:actual_attendance'] = 'Actual attendance percentage';
$string['privacy:metadata:zoomattendance_data:completion_met'] = 'Whether completion criteria were met';
// Meeting ID strings
$string['meeting_id'] = 'Zoom meeting';
$string['meeting_id_help'] = 'Select the Zoom meeting to track attendance for. If no meetings are available, you must first create a Zoom meeting activity in this course.';

$string['attendancefetched'] = '{} attendance records fetched from Zoom';
$string['meeting_id_required'] = 'Zoom meeting selection is required.';


$string['total_records'] = 'Total records';
$string['automatic_assignments'] = 'Automatically assigned';
$string['manual_assignments'] = 'Manually assigned';
$string['filter_manual'] = 'Manual assignments';
$string['unassigned_records'] = 'Unassigned records';
$string['fetch_zoom_data'] = 'Fetch Zoom data';
$string['manage_unassigned'] = 'Manage unassigned records';
$string['reset_assignments'] = 'Reset assignments';
$string['export_csv'] = 'Export CSV';
$string['export_excel'] = 'Export Excel';
$string['filter_all'] = 'All';
$string['filter_unassigned'] = 'Unassigned';
$string['filter_assigned'] = ' Automatically assigned';
$string['type_manual'] = 'Manual';
$string['type_automatic'] = 'Automatic';
$string['type_unassigned'] = 'Unassigned';
$string['manually_assigned_tooltip'] = 'This user has been manually assigned by an administrator';
$string['automatically_assigned_tooltip'] = 'This user was automatically assigned based on email address';
$string['actions'] = 'Actions';
$string['cognome'] = 'Last name';
$string['nome'] = 'First name';
$string['idnumber'] = 'Identification number';
$string['teams_user'] = 'Zoom user';
$string['assignment_type'] = 'Assignment type';

$string['zoom_participant_name'] = 'Zoom Participant';

$string['fetch_success'] = '{$a} meeting attendance sessions were downloaded from Zoom. These will be compared to the log settings to verify attendance within the specified date range.';
$string['loading'] = 'Loading...';


$string['participant_name'] = 'Participant Name';
$string['attendance_duration'] = 'Attendance Duration';
$string['suggested_match'] = 'Suggested Match';
$string['actions'] = 'Actions';
$string['apply_suggestion'] = 'Apply Suggestion';
$string['assign'] = 'Assign';
$string['select_user'] = 'Select User';
$string['apply_selected'] = 'Apply Selected';
$string['applying'] = 'Applying...';
$string['previous'] = 'Previous';
$string['next'] = 'Next';
$string['page'] = 'Page';
$string['of'] = 'of';
$string['total_records'] = 'Total Records';
$string['no_records_found'] = 'No records found';
$string['no_suggestion'] = 'No suggestion available';

$string['duration_participation'] = 'Duration of participation';
$string['attendance_percentage'] = '% attendance';
$string['minimum_threshold'] = 'Exceeding minimum<br>attendance threshold';

$string['filter_table_by_type'] = 'Filter table by user matching type';

$string['attendance_register_title'] = 'Attendance roster for Zoom meeting: <em>\'{$a->meeting_name}\'</em>. Meeting Duration: <em>from {$a->start_date}, to {$a->end_date} for {$a->duration}</em>; minimal attendance to get completion: <em>{$a->threshold}%</em>';

$string['back_to_register'] = 'Back to attendance roster';
$string['total_unique_users'] = 'Total unique users';

$string['reset_assignments'] = 'Reset assignments';
$string['reset_confirm'] = 'Are you sure you want to reset all manual assignments?';
$string['reset_success'] = 'All manual assignments have been reset.';

$string['fetch_success_message'] = 'Zoom data imported successfully!';
$string['fetch_error_message'] = 'Error during import';
$string['fetch_network_error'] = 'Network error';
