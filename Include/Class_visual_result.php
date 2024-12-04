<?php
/**
 *
 *  Survey management system for
 *
 **/

namespace Code\Survey\Inc;
 
class Class_visual_result { 
   
    public function visual_result() {
        
        $survey = array();
        global $wpdb;
        $result_table = $wpdb->prefix . 'survey_result';
        $query = "SELECT sid, answer, COUNT(*) AS answer_count
                  FROM $result_table
                  GROUP BY sid, answer
                  ORDER BY sid, answer_count DESC";
    
        $results = $wpdb->get_results($query);
    
        foreach ($results as $row) {
            $survey[] = array(
                "label" => $row->answer, 
                "y" => intval($row->answer_count)
            );
        }
        $survey_json = json_encode($survey);
        ?>
        <script>
        window.onload = function () {
            var chartData = <?php echo $survey_json; ?>;
    
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light1", 
                title: {
                    text: "Survey Results"
                },
                axisY: {
                    title: "Response Count"
                },
                data: [{        
                    type: "column",  
                    showInLegend: false, 
                    dataPoints: chartData 
                }]
            });
            chart.render();
        }
        </script>    
        <div id="chartContainer" style="height: 300px; width: 100%;"></div>
        <?php
    }
  

}