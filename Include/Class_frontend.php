<?php
/**
 *
 *  Survey management system for
 *
 **/

 namespace Code\Survey\Inc;
 
 class Class_frontend {

   public function __construct() {
       add_shortcode('dynamic_survey', [$this,'dynamic_survey_shortcode']);
       add_action('wp_ajax_submit_survey', [$this,'submit_survey']);
       add_action('wp_ajax_nopriv_submit_survey', [$this, 'submit_survey']);
   }

     public function dynamic_survey_shortcode($atts) {
        global $wpdb;
        $attributes = shortcode_atts(
            array(
                'id' => '0', // Default id value
            ),
            $atts
        );
    
        $id = $attributes['id'];
        $survey_table = $wpdb->prefix . 'code_survey';
        $survey = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $survey_table WHERE id = %d", $id)
        );

         if ( is_user_logged_in() ) {
         ?>
        <h3 id="vote"></h3>

        <form id="survey-form">
            <h2><?php echo esc_html($survey->title);?></h2>
            <p><?php echo esc_html($survey->details);?></p>

            <input type="radio" id="answer_1" name="surveypool" value="<?php echo esc_attr($survey->question_1); ?>" />
            <label for="answer_1"><?php echo esc_html($survey->question_1); ?></label><br />

            <input type="radio" id="answer_2" name="surveypool" value="<?php echo esc_attr($survey->question_2); ?>" />
            <label for="answer_2"><?php echo esc_html($survey->question_2); ?></label><br />

            <input type="radio" id="answer_3" name="surveypool" value="<?php echo esc_attr($survey->question_3); ?>" />
            <label for="answer_3"><?php echo esc_html($survey->question_3); ?></label><br />

            <input type="radio" id="answer_4" name="surveypool" value="<?php echo esc_attr($survey->question_4); ?>" />
            <label for="answer_4"><?php echo esc_html($survey->question_4); ?></label><br />
    
            <button type="button" id="generate_button" data-survey-id="<?php echo esc_attr($survey->id); ?>">Submit</button>
        
        </form>
         <?php 
           } else {
              echo "Please login & place your vote";
           } 
         ?>

     <?php
     }

   public function submit_survey() {
       global $wpdb;
   
       $table_name = $wpdb->prefix . 'survey_result';
       $answer = sanitize_text_field($_POST['answer']);
       $survey_id = intval($_POST['survey_id']);
       $user_id = intval($_POST['user_id']);
   
       $checkid = $wpdb->get_var($wpdb->prepare(
           "SELECT COUNT(*) FROM $table_name WHERE sid = %d AND uid = %d",
           $survey_id,
           $user_id
       ));
   
       if ($checkid) {
           wp_send_json_error('You have already submitted this survey.');
       }
   
       $wpdb->insert($table_name, [
           'answer' => $answer,
           'sid' => $survey_id,
           'uid' => $user_id,
           'created_at' => current_time('mysql'),
       ]);
   
       wp_send_json_success('Survey response submitted successfully!');
   }
}
  