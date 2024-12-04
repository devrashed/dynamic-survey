<?php
/**
 *
 *  Survey management system for
 *
 **/

namespace Code\Survey\Inc;
 
class Class_result_survey { 
   
    public function survey_result (){

        global $wpdb;
        $result_table = $wpdb->prefix . 'survey_result'; // Main table
        $code_survey = $wpdb->prefix . 'code_survey';   // Secondary table
        
        // Fixed query
        $query = "
            SELECT $code_survey.title, $result_table.sid, $result_table.answer, COUNT(*) AS answer_count
            FROM $result_table
            LEFT JOIN $code_survey
            ON $result_table.sid = $code_survey.id
            GROUP BY $result_table.sid, $result_table.answer
            ORDER BY $result_table.sid, answer_count DESC";
    
        
        // Execute query
        $results = $wpdb->get_results($query, ARRAY_A);
        
            echo '<table border="1" style="width: 100%; border-collapse: collapse;">';
            echo '<thead>
                    <tr>
                        <th>Title</th>
                        <th>Answer</th>
                        <th>Count</th>
                    </tr>
                  </thead>';
            echo '<tbody>';
        
            // Loop through results and display
            foreach ($results as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row['title']) . '</td>';
                echo '<td>' . esc_html($row['answer']) . '</td>';
                echo '<td>' . esc_html($row['answer_count']) . '</td>';
                echo '</tr>';
            }
        
            echo '</tbody>';
            echo '</table>';
    } 
    

  

}