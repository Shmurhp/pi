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
			'<p>The sorting test you just took is called the Implicit Association Test (IAT). You categorized good and bad words with images of Fat People and Thin People.</p>'].join('\n')
                },
                {
                    type:'info',
                    description:'<h4>Click "Submit" to submit your answers and receive your results.</h4></p>'
                }
            ]
        }
    ]);

    return API.script;
});
