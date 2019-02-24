define(['questAPI'], function(Quest){

    var API = new Quest();
    var isTouch = API.getGlobal().isTouch;

    API.addQuestionsSet('iatEval',{
        type: 'selectOne',
        numericValues:true, 
        style:'multiButtons',
        answers: ['Not at all', 'Slightly', 'Moderately', 'Very', 'Extremely']
    });

    API.addQuestionsSet('basicQ',{
        decline: true,
        required : true,
        autoSubmit:true,
        numericValues:true
    });

    API.addQuestionsSet('text',{
        inherit: 'basicQ',
        type: 'text',
        noSubmit:false
    });


    API.addSequence([
        {
            header: 'Debriefing',
            questions:[
                {
                    type:'info',
                    name: 'iatresults',
                    description: ['' +
			'<p>The sorting test you just took is called the Implicit Association Test (IAT). You categorized good and bad words with images of Fat People and Thin People.</p>' +
			'<div class="jumbotron jumbotron-dark">' +
			  '<h2>Here is your result:</h2>' +
			  '<p><%= global.weightiat.feedback %></p>' +
			'</div>' +

		    '<p>Your result is described as an "Automatic preference for Fat People over Thin People" if you were faster responding when <i>Fat People</i> and <i>Good</i> are assigned to the same response key than when <i>Thin People</i> and <i>Good</i> were classified with the same key. Your score is described as an "Automatic preference for Thin People over Fat People" if the opposite occurred.</p>' +
		    '<p>Your automatic preference may be described as "slight", "moderate", "strong", or "no preference". This indicates the <i>strength</i> of yourautomatic preference.</p>' +
		'<p>The IAT requires a certain number of correct responses in order to get results. If you made too many errors while completing the test you will get the feedback that there were too many errors to determine a result.</p>' +
	    '<p><b>Note that your IAT result is based only on the categorization task and not on the questions that you answered.</b></p>'+
		'<hr>'].join('\n')
                },
                {
                    type:'info',
                    description:'<h4>Click "Submit" to submit your answers and receive more information.</h4></p>'
                }
            ]
        }
    ]);

    return API.script;
});
