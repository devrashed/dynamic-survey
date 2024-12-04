<?php
/**
 *
 *  Survey management system for
 *
 **/

 namespace Code\Survey\Inc;
 
 class Class_addnew_survey {  

    public function __construct()
    {
      add_action('wp_ajax_survey_question', [$this,'survey_question']);
    }    
    public function create_survey(){
  ?>   

   <div id="wrapper">    

       <div class="row">  
           
         <table style="width: 300px;margin: left 50px;"> 
          <tr> 
              <td> Title:</td>
              <td> <input type="text" name="title" id="title"></td>
          </tr> 
          <tr> 
              <td> Description:</td>
              <td> <textarea name="details" id="details"> </textarea></td>
          </tr> 
          <tr> 
              <td>Answer-1:</td>
              <td><input type="text" name="answer_1" id="answer_1"></td>
          </tr> 
          <tr> 
              <td>Answer-2:</td>
              <td><input type="text" name="answer_2" id="answer_2"></td>
          </tr> 
          <tr> 
              <td>Answer-3:</td>
              <td><input type="text" name="answer_3" id="answer_3"></td>
          </tr> 
          <tr> 
              <td>Answer-4:</td>
              <td><input type="text" name="answer_4" id="answer_4"></td>
          </tr>
          <tr> 
              <td>End Date:</td>
              <td><input type="Date" name="endate" id="endate"></td>
          </tr>
          <tr> 
              <td></td>
              <td><button type="button" id="btnsubmit" name="btnsubmit">Submit</button></td>
          </tr>

        </table>

       </div>
               
   </div>

  <?php
   }

   

   
   public function survey_question() {
      check_ajax_referer('survey-wp-nonce', 'nonce');
  
      $title = sanitize_text_field($_POST['title']);
      $details = sanitize_textarea_field($_POST['details']);
      $answer_1 = sanitize_text_field($_POST['answer1']);
      $answer_2 = sanitize_text_field($_POST['answer2']);
      $answer_3 = sanitize_text_field($_POST['answer3']);
      $answer_4 = sanitize_text_field($_POST['answer4']);
      $endate = sanitize_text_field($_POST['endate']);
      

      if (empty($title)) {
          wp_send_json_error('Title is required.');
      }
  
      global $wpdb;
      $table_name = $wpdb->prefix . 'code_survey';
  
      $result = $wpdb->insert(
          $table_name,
          array(
              'title' => $title,
              'details' => $details,
              'question_1' => $answer_1,
              'question_2' => $answer_2,
              'question_3' => $answer_3,
              'question_4' => $answer_4,
              'startdate' => current_time('mysql'),
              'enddate' => $endate,
              'created_at' => current_time('mysql'),
          ),
          array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
      );
  
      if ($result) {
          wp_send_json_success('Survey question created successfully!');
      } else {
          wp_send_json_error('Failed to create survey question.');
      }
  }
  
 }