
jQuery(document).ready(function($) {
    jQuery('#btnsubmit').on('click', function() {
        var button = jQuery(this);

        var title = jQuery('#title').val();
        var details = jQuery('#details').val();
        var answer1 = jQuery('#answer_1').val();
        var answer2 = jQuery('#answer_2').val();
        var answer3 = jQuery('#answer_3').val();
        var answer4 = jQuery('#answer_4').val();
        var endate = jQuery('#endate').val();

        console.log(title);
        console.log(details);
        console.log(answer1);
        console.log(answer2);
        console.log(answer3);
        console.log(answer4);
        console.log(endate);
        
        if (title) {
            jQuery.ajax({
                url: ajax_ob.ajax_url,
                method: 'POST',
                dataType: 'JSON',
                data: {
                    action: 'survey_question',
                    title: title,
                    details: details,
                    answer1: answer1,
                    answer2: answer2,
                    answer3: answer3,
                    answer4: answer4,
                    endate: endate,
                    nonce: ajax_ob.nonce
                },
                beforeSend: function(xhr) {
                    console.log('button', button);
                    button.append('<span class="loading-spinner"></span>');
                },
                success: function(response) {
                    if (response.success) {
                        jQuery('#title, #details, #answer_1, #answer_2, #answer_3, #answer_4, #endate').val('');

                        jQuery.toast({
                            text: "Congratulations! Your blog title successfully generated.",
                            heading: 'success', 
                            icon: 'success', 
                            showHideTransition: 'fade', 
                            allowToastClose: true, 
                            hideAfter: 3000, 
                            stack: 15, 
                            position: { left : 'auto', right : 100, top : 153, bottom : 'auto' },
                            textAlign: 'left',
                            loader: true, 
                            loaderBg: '#9EC600',
                            class: 'aitite-toast', 
                        });    


                    } else {
                        jQuery.toast({ text: 'An error: ' + response.data, 
                            heading: 'Failed', 
                            icon: 'error',
                            showHideTransition: 'fade',
                            allowToastClose: true, 
                            hideAfter: 3000, 
                            stack: 5, 
                            position: 'top-right', 
                           });

                    }
                },
                complete: function() {
                    button.find('.loading-spinner').remove();
                },
                error: function(error) {

                    jQuery.toast({ text: 'An error occurred.', 
                        heading: 'Failed', 
                        icon: 'error',
                        showHideTransition: 'fade',
                        allowToastClose: true, 
                        hideAfter: 3000, 
                        stack: 5, 
                        position: 'top-right', 
                       });

                }
            });
        } else {
            jQuery.toast({ text: 'Please enter a title', 
                heading: 'Failed', 
                icon: 'error',
                showHideTransition: 'fade',
                allowToastClose: true, 
                hideAfter: 3000, 
                stack: 5, 
                position: 'top-right', 
               });
        }
    });
});


     /* ===== DataTable ====== */

    jQuery(document).ready(function($) {
        jQuery('#surveyTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "columnDefs": [
                { "orderable": false, "targets": -1 } // Disable ordering on the "Actions" column
            ]
        });
    });    



    /* ============================ 
              Delete Script 
       ============================ */

    // Handle delete button
    jQuery('.del_servey').on('click', function() {
        var surveyid = jQuery(this).data('id');
        //console.log(surveyid);
        if (confirm('Are you sure you want to delete this survey?')) {
            jQuery.ajax({
                url: ajax_ob.ajax_url,
                type: 'POST',
                dataType: 'JSON', 
                data: {
                    action: 'delete_survey',
                    id: surveyid,
                    //nonce: ajax_ob.nonce
                },
                success: function(response) {
                    if (response.success) {
                        //alert('Survey deleted successfully!');
                        jQuery.toast({
                            text: "Survey deleted successfully!",
                            heading: 'success', 
                            icon: 'success', 
                            showHideTransition: 'fade', 
                            allowToastClose: true, 
                            hideAfter: 3000, 
                            stack: 15, 
                            position: { left : 'auto', right : 100, top : 153, bottom : 'auto' },
                            textAlign: 'left',
                            loader: true, 
                            loaderBg: '#9EC600',
                            class: 'aitite-toast', 
                        }); 
                        location.reload();
                    } else {
                        //alert('Error: ' + response.data);
                        jQuery.toast({ text: 'An error: ' + response.data, 
                        heading: 'Failed', 
                        icon: 'error',
                        showHideTransition: 'fade',
                        allowToastClose: true, 
                        hideAfter: 3000, 
                        stack: 5, 
                        position: 'top-right', 
                       });
                    }
                }
            });
        }
    });



   /* ==============================  
              Fetch Data 
      ========================= */
   
    jQuery(document).ready(function($) {
        // Edit button click event
        jQuery('#surveyTable').on('click', '.edit_survey', function() {
            var surveyId = jQuery(this).data('id'); 
            console.log(surveyId);

            jQuery.ajax({
                url: ajax_ob.ajax_url,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'get_survey_details',
                    id: surveyId,
                    nonce: ajax_ob.nonce
                },
                success: function(response) {
                    if (response.success) {

                        jQuery('#survey_id').val(response.data.id);
                        jQuery('#title').val(response.data.title);
                        jQuery('#details').val(response.data.details);
                        jQuery('#answer_1').val(response.data.question_1);
                        jQuery('#answer_2').val(response.data.question_2);
                        jQuery('#answer_3').val(response.data.question_3);
                        jQuery('#answer_4').val(response.data.question_4);    
                        jQuery('#startdate').val(response.data.startdate);
                        jQuery('#enddate').val(response.data.enddate);

                    } else {
                        alert('Error fetching survey data.');
                    }
                },
            });
        });
    });


      
      /* ======================== 
                  Update 
        ========================= */

      jQuery(document).ready(function ($) {
        jQuery('#saveSurvey').on('click', function () {
            var formData = jQuery('#editSurveyForm').serialize();
    
            // AJAX request
            jQuery.ajax({
                url: ajax_ob.ajax_url,
                method: 'POST',
                dataType: 'json',
                data: formData + '&action=update_survey&nonce=' + ajax_ob.nonce,
                success: function (response) {
                    if (response.success) {
                        // Show success toast
                        $.toast({
                            text: "Survey updated successfully!",
                            heading: 'Success',
                            icon: 'success',
                            showHideTransition: 'fade',
                            allowToastClose: true,
                            hideAfter: 3000,
                            stack: 5,
                            position: 'top-right',
                            loader: true,
                            loaderBg: '#9EC600',
                            class: 'custom-toast',
                        });
                        // Optional reload
                        location.reload();
                    } else {
                        // Show error toast
                        jQuery.toast({
                            text: response.message || 'Error updating survey.',
                            heading: 'Failed',
                            icon: 'error',
                            showHideTransition: 'fade',
                            allowToastClose: true,
                            hideAfter: 3000,
                            stack: 5,
                            position: 'top-right',
                        });
                    }
                },
                error: function () {
                    // Show generic error toast
                    jQuery.toast({
                        text: 'An error occurred while updating the survey.',
                        heading: 'Failed',
                        icon: 'error',
                        showHideTransition: 'fade',
                        allowToastClose: true,
                        hideAfter: 3000,
                        stack: 5,
                        position: 'top-right',
                    });
                },
            });
        });
    });
    
    
 
    
            /* ============================
                Survey result 
            =========================== */


    jQuery(document).ready(function ($) {
        jQuery('#generate_button').on('click', function () {
            
            const selectedAnswer = jQuery('input[name="surveypool"]:checked').val();
            const surveyId = jQuery(this).data('survey-id'); // Correct attribute selection

            if (!selectedAnswer) {
                alert('Please select an option.');
                return;
            }

            jQuery.ajax({
                url: ajax_ob.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'submit_survey',
                    answer: selectedAnswer,
                    survey_id: surveyId,
                    user_id: ajax_ob.user_id,
                },
                success: function (response) {
                    if (response.success) {
                        alert(response.data);
                        $('#survey-form').remove(); // Optionally hide form after submission
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