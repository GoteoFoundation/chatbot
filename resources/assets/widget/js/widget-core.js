module.exports = {
	load: function(domain, language, topic, loadFonts) {

		// Accessibility: Handle event for click with keyboard (enter key)
		handleClickKeyboard = function (func) {
			return function (e) {
				if (e.type != 'keypress' || e.keyCode == 13) func($(this));
			};
		};

		/** GLOBAL VARS **/

		var questionsStack = [];
		var lastRequestedQuestionId = 0;
		var widgetObj, widgetIconObj, widgetContainerObj, widgetLoadingObj, widgetErrorObj, widgetQuestionsObj, widgetBackButtons;

		/** CSS FONTS LOADING **/

		if(loadFonts) {
			$('head').append('<link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">');
		}

		/** CSS STYLESHEET LOADING **/

		$('head').append('<link type="text/css" href="' + domain + '/widget/widget.css" rel="stylesheet">');

		/** LOADING OF UI FROM LANGUAGE PARAMETERS **/

		var apiLoadCallURL = domain + '/api/load/' + language;

		$.get(apiLoadCallURL)
			.done(function(response) {
				$('body').append(templateShell(response));
				widgetObj = $('#goteo-help-widget');
				widgetIconObj = widgetObj.find('a.widget-icon').first();
				widgetContainerObj = widgetObj.find('div.widget-container').first();
				widgetLoadingObj = widgetObj.find('div.widget-loading').first();
				widgetErrorObj = widgetObj.find('div.widget-error').first();
				widgetQuestionsObj = widgetObj.find('div.widget-questions').first();
				widgetBackButtons = widgetObj.find('a.widget-back, a.widget-restart');
				initFixedUIEvents();
				widgetObj.show();

				$(window).on('resize', function() {
					if(widgetObj.hasClass('open')) {
						setBodyScroll(true);

						setTimeout(function() {
							resetSliderWidths();
						}, 100);
					}
				});
			})
			.fail(function() {
				console.log("Error loading help widget")
			});

		/** FUNCTIONALITY FUNCTIONS **/

		var initFixedUIEvents = function() {
			widgetIconObj.bind('click keypress', handleClickKeyboard(function(thisObj) {
					widgetObj.removeClass('icon');
					setTimeout(function() {
						widgetObj.addClass('open');
						setBodyScroll(true);
					}, 150);
			}));

			widgetContainerObj.find('a.widget-close').bind('click keypress', handleClickKeyboard(function(thisObj) {
					setBodyScroll(false);
					widgetObj.removeClass('open');
					setTimeout(function() {
						widgetObj.addClass('icon');
					}, 150);
			}));

			widgetContainerObj.find('a.widget-back').bind('click keypress', handleClickKeyboard(function(thisObj) {
					if(questionsStack.length > 1) {
						questionsStack.pop();
						moveToQuestion(questionsStack[questionsStack.length - 1]);
					}
			}));

			widgetContainerObj.find('a.widget-restart').bind('click keypress', handleClickKeyboard(function(thisObj) {
					if(questionsStack.length > 1) {
						questionsStack = [questionsStack[0]];
						moveToQuestion(questionsStack[0]);
					}
			}));

			widgetContainerObj.find('button.widget-start').bind('click keypress', handleClickKeyboard(function(thisObj) {
					widgetObj.addClass('loading');
					widgetLoadingObj.attr('aria-hidden', 'false');
					widgetObj.find('div.widget-welcome').attr('aria-hidden', 'true').hide();
					widgetQuestionsObj.attr('aria-hidden', 'false').show();
					loadQuestion(0);
			}));

			widgetErrorObj.find('button.widget-retry').bind('click keypress', handleClickKeyboard(function(thisObj) {
					widgetObj.removeClass('error').addClass('questions loading');
					widgetErrorObj.attr('aria-hidden', 'true');
					widgetLoadingObj.attr('aria-hidden', 'false');
					loadQuestion(lastRequestedQuestionId);
			}));
		};

		// Reset questions slider width
		var resetSliderWidths = function() {
			var goteoWidgetQuestionObjList = widgetQuestionsObj.find('div.widget-question');
			var containerWidth = widgetContainerObj.outerWidth();

			widgetQuestionsObj.width(containerWidth * goteoWidgetQuestionObjList.length);
			goteoWidgetQuestionObjList.each(function() {
				$(this).outerWidth(containerWidth);
			});
		};

		// Move slider to specified question and delete childs
		var moveToQuestion = function(questionId) {
			var questionObj = widgetContainerObj.find('div.widget-question[data-id=' + questionId + ']');

			if(questionObj.length) {
				var questionObjPosition = questionObj.position();

				widgetQuestionsObj.css('left', (questionObjPosition.left * (-1)) + 'px');
				widgetContainerObj.find('div.widget-question').attr('aria-hidden', 'true');
				questionObj.attr('aria-hidden', 'false');

				if(questionsStack.length > 1) {
					widgetBackButtons.show().attr('aria-hidden', 'false');
				} else {
					widgetBackButtons.hide().attr('aria-hidden', 'true');
				}

				setTimeout(function() {
					var questionObjSibling = questionObj.next();

					while(questionObjSibling.length) {
						questionObjSibling.remove();
						questionObjSibling = questionObj.next();
					}
				}, 200);
			}
		};

		// Check if the a  has been already loaded
		// Returns:
		//	false if not loaded
		//  index (integer) if loaded
		var isAlreadyLoadedQuestion = function(questionId) {
			for(var i = 0; i < questionsStack.length; i++) {
				if(questionsStack[i] == questionId) {
					return i;
				}
			}

			return false;
		}

		// Load a question
		var loadQuestion = function(questionId) {
			var index = isAlreadyLoadedQuestion(questionId);

			if(index !== false) {
				questionsStack = questionsStack.slice(0, (index + 1));
				widgetObj.removeClass('loading').addClass('questions');
				widgetLoadingObj.attr('aria-hidden', 'true');
				moveToQuestion(questionId);
			}
			else {
				var apiQuestionCallURL = domain + '/api/question/' + language + '/' + topic;

				if(questionId != 0) {
					apiQuestionCallURL += '/' + questionId;
				}

				lastRequestedQuestionId = questionId;

				$.get(apiQuestionCallURL)
					.done(function(response) {
						questionsStack.push(response.id);
						widgetQuestionsObj.append(templateQuestion(response));
						questionObj = widgetQuestionsObj.find('.widget-question[data-id=' + response.id + ']').first();
						resetSliderWidths();
						widgetObj.removeClass('loading').addClass('questions');
						widgetLoadingObj.attr('aria-hidden', 'true');
						moveToQuestion(response.id);

						questionObj.find('ul.widget-answers li a[data-question-link]').bind('click keypress', handleClickKeyboard(function(thisObj) {
							widgetObj.addClass('loading');
							widgetLoadingObj.attr('aria-hidden', 'false');
							loadQuestion(thisObj.data('question-link'));
						}));
					})
					.fail(function() {
						showError();
					});
			}
		};

		// Show error
		var showError = function() {
			widgetObj.removeClass('loading questions').addClass('error');
			widgetErrorObj.attr('aria-hidden', 'false');
			widgetLoadingObj.attr('aria-hidden', 'true');
			widgetQuestionsObj.attr('aria-hidden', 'true');
		};

		// Check the need of blocking the main page scroll on mobile
		var setBodyScroll = function(add) {
			var winWidth = $(window).width();

			if((add && (winWidth < 768))) {
				$('body').css('overflow', 'hidden');
			}
			else if(!add) {
				$('body').css('overflow', '');
			}
		};

		// Escape double quotes in template value strings
		var escapeQuotes = function(value) {
			if((typeof value === 'string') && value) {
				return value.replace(/"/g, '&quot;');
			}
		}

		/** TEMPLATE GENERATION FUNCTIONS **/

		// Main shell template
		var templateShell = function (languageData) {
			var html = '';

			html += '<div id="goteo-help-widget" class="icon" style="display: none;">';
			html += '  <a class="widget-icon" tabindex="0" title="' + escapeQuotes(languageData.icon_tooltip) + '">';
			html += '    <span>' + languageData.icon_tooltip + '</span>';
			html += '  </a>';
			html += '  <div class="widget-container" role="dialog" aria-labelledby="goteo-help-widget-title">';
			html += '    <div class="widget-header">';
			html += '      <h5 id="goteo-help-widget-title">' + languageData.widget_title + '</h5>';
			html += '      <a class="widget-back" tabindex="0" title="' + escapeQuotes(languageData.go_back_icon) + '" aria-hidden="true"><span>' + languageData.go_back_icon + '</span></a>';
			html += '      <a class="widget-restart" tabindex="0" title="' + escapeQuotes(languageData.reset_icon) + '" aria-hidden="true"><span>' + languageData.reset_icon + '</span></a>';
			html += '      <a class="widget-close" tabindex="0" title="' + escapeQuotes(languageData.close_icon) + '"><span>' + languageData.close_icon + '</span></a>';
			html += '    </div>';
			html += '    <div class="widget-questions" aria-hidden="true"></div>';
			html += '    <div class="widget-welcome">';
			html += '      <p>' + languageData.widget_welcome + '</p>';
			html += '      <button class="widget-start">' + languageData.start_icon + '</button>';
			html += '    </div>';
			html += '    <div class="widget-error" aria-hidden="true">';
			html += '      <div class="widget-error-content">';
			html += '        <h6>' + languageData.error_title + '</h6>';
			html += '        <p>' + languageData.error_message + '</p>';
			html += '        <button class="widget-retry">' + languageData.error_retry + '</button>';
			html += '      </div>';
			html += '    </div>';
			html += '    <div class="widget-loading" aria-hidden="true">';
			html += '      <p>' + languageData.loading + '</p>';
			html += '    </div>';
			html += '    <div class="widget-footer">';
			html += '      <a class="widget-faq" href="' + escapeQuotes(languageData.faq_url) + '" target="_blank" title="' + escapeQuotes(languageData.faq_title) + '"><span>' + languageData.faq_title + '</span></a>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			return html;
		};

		// Question template
		var templateQuestion = function (questionData) {
			var html = answersHtml = '';

			for (var i = 0; i < questionData.answers.length; i++) {
				if(questionData.answers[i].type === 'question') {
					answersHtml += '<li><a tabindex="0" data-question-link="' + escapeQuotes(questionData.answers[i].value.toString()) + '">' + questionData.answers[i].answer + '</a></li>';
				}
				else {
					answersHtml += '<li><a href="' + escapeQuotes(questionData.answers[i].value) + '" target="_blank">' + questionData.answers[i].answer + '</a></li>';
				}
			}

			html += '<div class="widget-question" data-id="' + escapeQuotes(questionData.id.toString()) + '">';
			html += '  <h6>' + questionData.question + '</h6>';
			html += '  <ul class="widget-answers">';
			html += answersHtml;
			html += '  </ul>';
			html += '</div>';

			return html;
		};
	}
};
