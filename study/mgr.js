define(['managerAPI'], function(Manager) {

	function runStudy(isTouch)
	{
		var API = new Manager();

		API.setName('mgr');
		API.addSettings('skip',true);
		API.addSettings('skin','demo');
		API.addSettings('DEBUG', {level: 'error'});
    
		API.addGlobal({
		//YBYB: change when copying back to the correct folder
		//  baseURL: '/implicit/user/education/weight/demo.weight.0003/images/'
			baseURL: '/implicit/user/demo.us/demo.weight.0003/images/',
			isTouch:isTouch, 
	    posWords : API.shuffle([
            'Love', 'Cheer', 'Friend', 'Pleasure', 
            'Adore', 'Cheerful', 'Friendship', 'Joyful', 
            'Smiling','Cherish', 'Excellent', 'Glad', 
            'Joyous', 'Spectacular', 'Appealing', 'Delight', 
            'Excitement', 'Laughing', 'Attractive','Delightful', 
            'Fabulous', 'Glorious', 'Pleasing', 'Beautiful', 
            'Fantastic', 'Happy', 'Lovely', 'Terrific', 
            'Celebrate', 'Enjoy', 'Magnificent', 'Triumph']), 
        negWords : API.shuffle([
            'Abuse', 'Grief', 'Poison', 'Sadness', 
            'Pain', 'Despise', 'Failure', 'Nasty', 
            'Angry', 'Detest', 'Horrible', 'Negative', 
            'Ugly', 'Dirty', 'Gross', 'Evil', 
            'Rotten','Annoy', 'Disaster', 'Horrific',  
            'Scorn', 'Awful', 'Disgust', 'Hate', 
            'Humiliate', 'Selfish', 'Tragic', 'Bothersome', 
            'Hatred', 'Hurtful', 'Sickening', 'Yucky'])
		});
		API.save({isTouch:isTouch});
		
		if (isTouch)
		{
			var injectedStyle = [
				'[piq-page] {background-color: #fff; border: 1px solid transparent; border-radius: 4px; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05); margin-bottom: 20px; border-color: #bce8f1;}',
				'[piq-page] > ol {margin: 15px;}',
				'[piq-page] > .btn-group {margin: 0px 15px 15px 15px;}',
                '.container {padding:5px;}',
                '[pi-quest]::before, [pi-quest]::after {content: " ";display: table;}',
				'[pi-quest]::after {clear: both;}',
				'[pi-quest] h3 { border-bottom: 1px solid transparent; border-top-left-radius: 3px; border-top-right-radius: 3px; padding: 10px 15px; color: inherit; font-size: 2em; margin-bottom: 20px; margin-top: 0;background-color: #d9edf7;border-color: #bce8f1;color: #31708f;}',
				'[pi-quest] .form-group > label {font-size:1.2em; font-weight:normal;}',
				
				'[pi-quest] .btn-toolbar {margin:15px;float:none !important; text-align:center;position:relative;}',
				'[pi-quest] [ng-click="decline($event)"] {position:absolute;right:0;bottom:0}',
				'[pi-quest] [ng-click="submit()"] {width:30%;line-height: 1.3333333;border-radius: 6px;}',
				// larger screens
				'@media (min-width: 480px) {',
					' [pi-quest] [ng-click="submit()"] {width:30%;padding: 10px 16px;font-size: 1.6em;}',
				'}',
				// phones and smaller screens
				'@media (max-width: 480px) {',
					' [pi-quest] [ng-click="submit()"] {padding: 8px 13px;font-size: 1.2em;}',
					' [pi-quest] [ng-click="decline($event)"] {font-size: 0.9em;padding:3px 6px;}',
				'}'
    		].join('');

    		API.addSettings('injectStyle', injectedStyle);
		}
	

		API.addTasksSet({
			instructions: [{
				type: 'message',
				buttonText: 'Continue'
			}],

			realstart: [{
				inherit: 'instructions',
				name: 'realstart',
				templateUrl: 'realstart.jst',
				title: 'Consent',
				piTemplate: true,
				header: 'Welcome'
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
				name: 'weightiat',
				version: '0.3',
				scriptUrl: 'weightiat.js'
			}],

			demographics: [{
				type: 'quest',
				name: 'demographics',
				scriptUrl: 'demographics.js'
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
				piTemplate: 'debrief',
				isTouch:isTouch,
				last: true,
				demo:true,
				header: 'Last Page',
				pre:function(){
					var head = document.getElementsByTagName('head')[0];
					    var script = document.createElement('script');
					    script.type = 'text/javascript';
					    script.src = "https://apis.google.com/js/platform.js";
					    head.appendChild(script);					    
				}
			}]
		});

		API.addSequence([
			{inherit: 'realstart'},
			{
				mixer:'random', // randomize sequence of variables
				data:[
					{inherit: 'explicits'},
					{inherit: 'demographics'},
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
	}
	
	return runStudy;
});









