

    
/* =========== Survey result ========= */


jQuery(document).ready(function ($) {
    jQuery('#generate_button').on('click', function () {
        //alert('working'); // Debugging step

        const correctanswer = jQuery('input[name="surveypool"]:checked').val();
        const surveyId = jQuery(this).data('survey-id');

        if (!correctanswer) {
            alert('Please select your answer.');
            return;
        }

        jQuery.ajax({
            url: ajax_ob.ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'submit_survey',
                answer: correctanswer,
                survey_id: surveyId,
                user_id: ajax_ob.user_id,
            },
            success: function (response) {
                if (response.success) {
                    alert(response.data);
                    jQuery('#vote').text("Your vote is already done"); 
                } else {
                    alert(response.data);
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            },
        });
    });
});


