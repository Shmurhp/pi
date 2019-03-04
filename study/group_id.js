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
        header: 'Group ID Assignment',
        numbered: false
    });

    /**
	* Question prototypes
	*/
    API.addQuestionsSet('basicQ',{
        decline: true,
        required : true,
        errorMsg: {
            required: 'Please enter your assigned Group ID value'
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
    API.addQuestionsSet('groupID',{
        inherit: 'text',
        name: 'groupID',
        stem: 'What group ID number were you assigned?'
    });

    if (isTouch) API.addSequence([
        {
            inherit: 'basicPage',
            questions: [
                {inherit:'groupID'}
            ]
        }
    ]);

    if (!isTouch) API.addSequence([
        {
            inherit: 'basicPage',
            questions: [{inherit: 'groupID'}]
        }
    ]);

    return API.script;
});
