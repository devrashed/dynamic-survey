<?php
/**
 *
 *  Survey management system for
 *
 **/

 namespace Code\Survey\Inc;
 
 class Class_viewall_survey {  

   
    public function __construct()
    {
     add_action('wp_ajax_delete_survey', [$this,'delete_survey']);
     add_action('wp_ajax_get_survey_details', [$this,'get_survey_details']);
     add_action('wp_ajax_update_survey', [$this,'update_survey']);
    }    

    public function viewall_survey(){
   ?>
    <table id="surveyTable" class="display" style="width:100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Details</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}code_survey");
        foreach ($results as $row) {
            echo "<tr>
                <td>{$row->id}</td>
                <td>{$row->title}</td>
                <td>{$row->details}</td>
                <td>{$row->startdate}</td>
                <td>{$row->enddate}</td>
                <td>
                <a class='edit_survey' href='#ex1' rel='modal:open' data-id='{$row->id}'>Edit</a>
                <a href='#' class='del_servey' data-id='{$row->id}'>Delete</a>
            </tr>";
        }
        ?>
    </tbody>
</table>

    <!-- Modal Form -->
        <div id="ex1" class="modal">
            <form id="editSurveyForm">
                <table>
                    <tr>
                        <td><label for="title">Title</label></td>
                        <td><input type="text" id="title" name="title" /></td>
                    </tr>
                    <tr>
                        <td><label for="details">Details</label></td>
                        <td><textarea id="details" name="details"></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="answer_1">Questions-1</label></td>
                        <td><input type="text" id="answer_1" name="answer_1" /></td>
                    </tr>
                    <tr>
                        <td><label for="answer_2">Questions-2</label></td>
                        <td><input type="text" id="answer_2" name="answer_2" /></td>
                    </tr>
                    <tr>
                        <td><label for="answer_3">Questions-3</label></td>
                        <td><input type="text" id="answer_3" name="answer_3" /></td>
                    </tr>
                    <tr>
                        <td><label for="answer_4">Questions-4</label></td>
                        <td><input type="text" id="answer_4" name="answer_4" /></td>
                    </tr>
                    <tr>
                        <td><label for="startdate">Start Date</label></td>
                        <td><input type="text" id="startdate" name="startdate" /></td>
                    </tr>
                    <tr>
                        <td><label for="enddate">End Date</label></td>
                        <td><input type="text" id="enddate" name="enddate" /></td>
                    </tr>
                    <tr>
                        <td><input type="hidden" id="survey_id" name="survey_id" /></td>
                        <td><button type="button" id="saveSurvey">Save</button></td>
                    </tr>
                </table>
            </form>
        </div>



 <?php
    } 

    /* =====  Delete Script ======== */

    public function delete_survey() {
      //check_ajax_referer('survey-wp-nonce', 'nonce');
      
      global $wpdb;
      $id = intval($_POST['id']);
      $result = $wpdb->delete($wpdb->prefix . 'code_survey', ['id' => $id]);
      if ($result) {
         wp_send_json_success('Survey deleted successfully.');
      } else {
         wp_send_json_error('Failed to delete survey.');
    }

   }

    /* =====  Edit Script ======== */
    
   public function get_survey_details() {
       // Verify nonce
       if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'survey-wp-nonce')) {
           wp_send_json_error(['message' => 'Invalid nonce']);
       }
   
       global $wpdb;
       $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
       
       if ($id) {
           $result = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}code_survey WHERE id = $id");
           if ($result) {
               wp_send_json_success([
                   'id' => $result->id,
                   'title' => $result->title,
                   'details' => $result->details,
                   'question_1' => $result->question_1,
                   'question_2' => $result->question_2,
                   'question_3' => $result->question_3,
                   'question_4' => $result->question_4,
                   'startdate' => $result->startdate,
                   'enddate' => $result->enddate,
               ]);
           } else {
               wp_send_json_error(['message' => 'Survey not found']);
           }
       } else {
           wp_send_json_error(['message' => 'Invalid ID']);
       }
   }
    

    /* =====  Update Script ======== */
   
  public function update_survey() {

    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'survey-wp-nonce')) {
        wp_send_json_error(['message' => 'Invalid nonce']);
    }

    global $wpdb;
    $id = isset($_POST['survey_id']) ? intval($_POST['survey_id']) : 0;
    $title = isset($_POST['title']) ? sanitize_text_field($_POST['title']) : '';
    $details = isset($_POST['details']) ? sanitize_textarea_field($_POST['details']) : '';
    
    $question_1 = isset($_POST['answer_1'])? sanitize_text_field($_POST['answer_1']) : '';
    $question_2 = isset($_POST['answer_2'])? sanitize_text_field($_POST['answer_2']) : '';
    $question_3 = isset($_POST['answer_3'])? sanitize_text_field($_POST['answer_3']) : '';
    $question_4 = isset($_POST['answer_4'])? sanitize_text_field($_POST['answer_4']) : '';

    $startdate = isset($_POST['startdate']) ? sanitize_text_field($_POST['startdate']) : '';
    $enddate = isset($_POST['enddate']) ? sanitize_text_field($_POST['enddate']) : '';

    if ($id && $title && $details && $startdate && $enddate) {
        $wpdb->update(
            "{$wpdb->prefix}code_survey",
            [
                'title' => $title,
                'details' => $details,
                'question_1' => $question_1,
                'question_2' => $question_2,
                'question_3' => $question_3,
                'question_4' => $question_4,
                'startdate' => $startdate,
                'enddate' => $enddate
            ],
            ['id' => $id]
        );

        wp_send_json_success(['message' => 'Survey updated successfully']);
    } else {
        wp_send_json_error(['message' => 'Missing required data']);
    }
}



 }