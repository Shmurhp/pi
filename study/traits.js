define(['questAPI'], function(Quest){

    var API = new Quest();
    var isTouch = API.getGlobal().isTouch;

    /**
	* Page prototype
	*/
    API.addPagesSet('basicPage',{
        noSubmit:false, //Change to true if you don't want to show the submit button.
	v1style: 2,
        decline: false,
        autoFocus:true, 
        header: 'Traits',
        numbered: false
    });

    /**
	* Question prototypes
	*/
    API.addQuestionsSet('basicQ',{
        decline: true,
        required : true,
        errorMsg: {
            required: 'What traits do you believe a mentee should possess?'
        },
        autoSubmit:true,
        numericValues:true
    });

    API.addQuestionsSet('text',{
        inherit: 'basicQ',
        type: 'text',
        noSubmit:false
    });


    /**
	* Actual questions
	*/
    API.addQuestionsSet('traits',{
        inherit: 'text',
        name: 'traits',
        stem: 'What traits do you believe a mentee should possess?'
    });

    if (isTouch) API.addSequence([
        {
            inherit: 'basicPage',
            questions: [
                {inherit:'traits'}
            ]
        }
    ]);

    if (!isTouch) API.addSequence([
        {
            inherit: 'basicPage',
            questions: [{inherit: 'traits'}]
        }
    ]);

    return API.script;
});
