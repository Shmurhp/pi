define(['managerAPI'], function(Manager) {
    var API = new Manager();

    API.setName('mgr');
    API.addSettings('skip',true);
    API.addSettings('skin','demo');
    API.addSettings('DEBUG', {level: 'info'});

    API.addGlobal({
        weightiat:{},
        //YBYB: change when copying back to the correct folder
        baseURL: './study/images/',
        posWords : API.shuffle([
	    'Love', 'Cheer', 'Friend', 'Pleasure',
            'Adore', 'Cheerful', 'Friendship', 'Joyful',
            'Smiling','Cherish', 'Excellent', 'Glad',
            'Joyous', 'Spectacular', 'Appealing', 'Delight',
            'Excitement', 'Laughing', 'Attractive','Delightful',
            'Fabulous', 'Glorious', 'Pleasing', 'Beautiful',
            'Fantastic', 'Happy', 'Lovely', 'Terrific',
            'Celebrate', 'Enjoy', 'Magnificent', 'Triumph'
        ]), 
        negWords : API.shuffle([
	    'Abuse', 'Grief', 'Poison', 'Sadness',
            'Pain', 'Despise', 'Failure', 'Nasty',
            'Angry', 'Detest', 'Horrible', 'Negative',
            'Ugly', 'Dirty', 'Gross', 'Evil',
            'Rotten','Annoy', 'Disaster', 'Horrific',
            'Scorn', 'Awful', 'Disgust', 'Hate',
            'Humiliate', 'Selfish', 'Tragic', 'Bothersome',
            'Hatred', 'Hurtful', 'Sickening', 'Yucky'
        ])
    });

    API.addTasksSet({
        instructions: [{
            type: 'message',
            buttonText: 'Continue'
        }],

        intropage: [{
            inherit: 'instructions',
            name: 'intropage',
            templateUrl: 'intropage.jst',
            title: 'Consent Page',
            piTemplate: true,
            header: 'Welcome'
        }],

        realstart: [{
            inherit: 'instructions',
            name: 'realstart',
            templateUrl: 'realstart.jst',
            title: 'Consent',
            piTemplate: true,
            header: 'Welcome'
        }],

        group_id: [{
            type: 'quest',
            name: 'group_id',
            scriptUrl: 'group_id.js'
        }],

        traits: [{
            type: 'quest',
            name: 'traits',
            scriptUrl: 'traits.js'
        }],

        instiat_weight: [{
            inherit: 'instructions',
            name: 'instiat',
            templateUrl: 'instiat_weight.jst',
            title: 'IAT Instructions',
            piTemplate: true,
            header: 'Implicit Association Test'
        }],

        explicits: [{
            type: 'quest',
            name: 'explicits',
            scriptUrl: 'explicits.js'
        }],

        weightiat: [{
            type: 'pip',
            version:0.3,
            baseUrl: '//cdn.jsdelivr.net/gh/minnojs/minno-time@0.3.40/dist/js',
            name: 'weightiat',
            scriptUrl: 'weightiat.js'
        }],

        demographics: [{
            type: 'quest',
            name: 'demographics',
            scriptUrl:'demographics.js'
        }],


        debriefing: [{
            type: 'quest',
            name: 'debriefing',
            scriptUrl: 'debriefing.js'
        }],

        lastpage: [{
            type: 'message',
            name: 'lastpage',
            templateUrl: 'lastpage.jst',
            title: 'End',
            piTemplate: true,
            buttonHide: true,
            last:true,
            header: 'You have completed the study'
        }]
    });

    API.addSequence([
        {inherit: 'intropage'},
        {inherit: 'realstart'},
        {inherit: 'group_id'},
        {inherit: 'traits'},
        {
            mixer:'random',
            data:[
                {inherit: 'explicits'},
                {inherit: 'demographics'},

                // force the instructions to preceed the iat
                {
                    mixer: 'wrapper',
                    data: [
                        {inherit: 'instiat_weight'},
                        {inherit: 'weightiat'}
                    ]
                }
            ]
        },

        {inherit: 'debriefing'},
        {inherit: 'lastpage'}
    ]);

    return API.script;
});
