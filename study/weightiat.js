define(['pipAPI','/minno-tasks/IAT/iat6.js'], function(APIConstructor, iatExtension){
	var API = new APIConstructor();
    var global = API.getGlobal();
    
    return iatExtension({
        category1 : {
            name : 'Fat people', //Will appear in the data.
            title : {
                media : {word : 'Fat people'}, //Name of the category presented in the task.
                css : {color:'#31940F','font-size':'1.8em'}, //Style of the category title.
                height : 4 //Used to position the "Or" in the combined block.
            }, 
            stimulusMedia : [ //Stimuli content as PIP's media objects               
				{image: 'fatwoman1.png'},
                {image: 'fatwoman2.png'},
                {image: 'fatwoman3.png'},
				{image: 'fatwoman4.png'},
				{image: 'fatwoman5.png'},
                {image: 'fatwoman6.png'}     
    	    ],
    		//Stimulus css (style)
    		stimulusCss : {color:'#31940F','font-size':'2.3em'}
        },    
        category2 :    {
            name : 'Thin people', //Will appear in the data.
            title : {
                media : {word : 'Thin people'}, //Name of the category presented in the task.
                css : {color:'#31940F','font-size':'1.8em'}, //Style of the category title.
                height : 4 //Used to position the "Or" in the combined block.
            }, 
            stimulusMedia : [ //Stimuli content as PIP's media objects               
				{image: 'thinwoman1.png'},
                {image: 'thinwoman2.png'},
				{image: 'thinwoman3.png'},
				{image: 'thinwoman4.png'},
				{image: 'thinwoman5.png'},
                {image: 'thinwoman6.png'}  
            ],
    		//Stimulus css (style)
    		stimulusCss : {color:'#31940F','font-size':'2.3em'}
        },
		attribute1 :
		{
			name : 'Bad words',
			title : {
				media : {word : 'Bad words'},
				css : {color:'#0000FF','font-size':'1.8em'},
				height : 4 //Used to position the "Or" in the combined block.
			},
			stimulusMedia : [ //Stimuli content as PIP's media objects
				{word: global.negWords[0]},
				{word: global.negWords[1]},
				{word: global.negWords[2]},
				{word: global.negWords[3]},
				{word: global.negWords[4]},
				{word: global.negWords[5]},
				{word: global.negWords[6]},
				{word: global.negWords[7]}
			],
			//Stimulus css
			stimulusCss : {color:'#0000FF','font-size':'2.3em'}
		},
		attribute2 :
		{
			name : 'Good words',
			title : {
				media : {word : 'Good words'},
				css : {color:'#0000FF','font-size':'1.8em'},
				height : 4 //Used to position the "Or" in the combined block.
			},
			stimulusMedia : [ //Stimuli content as PIP's media objects
				{word: global.posWords[0]},
				{word: global.posWords[1]},
				{word: global.posWords[2]},
				{word: global.posWords[3]},
				{word: global.posWords[4]},
				{word: global.posWords[5]},
				{word: global.posWords[6]},
				{word: global.posWords[7]}
			],
			//Stimulus css
			stimulusCss : {color:'#0000FF','font-size':'2.3em'}
		},
        base_url : {//Where are your images at?
            image : global.baseURL
        },
		isTouch : global.isTouch
    });
});

