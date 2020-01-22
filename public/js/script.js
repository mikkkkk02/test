$(document).ready(function() {
	app.init();
});

var app = {

	init: function() {
		var setup = this.setup;

		$('#main').removeClass('hidden');

		switch(pageID) {
			default: break;
		}

		setup.vendors();
	},

	setup: {

		vendors: function() {

			/* Init HTMLTextarea */
			trumbowygInit();

			/* Initialize Select2 */
			selectInit();

			/* Initialize pickers */
			datepickerInit();
			// timepickerInit();

			/* Initialize Google Calendar Logout */
			gapiLogoutInit();
			

			/* Initialize form ajax */
			$('form.ajax').each(function() {
				var form = $(this);

				app.form.init({
					form: form,
					url: form.attr('action'),
					redirect: form.data('redirect'),
				});
			});

			function trumbowygInit() {
				var richtexts = $('.trumbowyg');

				if(richtexts.length) {
					$.trumbowyg.svgPath = '/image/trumbowyg.svg';

					$(richtexts).each(function(e, a) {
						var content = $(a).text();

						$(a).trumbowyg({
							btns: [
								['undo', 'redo'],
								['formatting'],
								['strong', 'em', 'del'],
								['superscript', 'subscript'],
								['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
								['unorderedList', 'orderedList'],
								['horizontalRule']
							],
						});

						$(a).trumbowyg('html', content);
					});
				}
			}

			function selectInit() {
				var select = $('.select2');

				if(select.length)
					select.select2({ multiple: true });	
			}

			function datepickerInit() {
				var datepicker = $('.datepicker');

				if(datepicker.length)
					datepicker.datepicker({ autoclose: true });				
			}

			function timepickerInit() {
				var timepicker = $('.timepicker-input');

				if(timepicker.length)
					timepicker.timepicker({ defaultValue: null });
			}

			function gapiLogoutInit() {
				
				$('#gapiLogoutBtn').on('click', function() {
					gapi.auth2.getAuthInstance().signOut().then(function () {

					});
					gapi.auth2.getAuthInstance().disconnect();
				});
			}
		},
	},

	calendar: {

		init: function() { //console.log('Initializing Google Calendar...');

			/* Default Values */
			const discoveryUrl = "https://www.googleapis.com/discovery/v1/apis/calendar/v3/rest";
			const scopes = "https://www.googleapis.com/auth/calendar";
			const syncBtn = $('#eventSyncBtn');
			let events = JSON.parse(eventsJSON);
			let existingEvents = [];

			gapi.load('client:auth2', initClient);

			function bindEvents() {
				syncBtn.click(function(e) {
					e.preventDefault();

					/* Create events or sign-in */
					if(gapi.auth2.getAuthInstance().isSignedIn.get()) {
						syncEvents();
					} else {
						gapi.auth2.getAuthInstance().signIn();
						gapi.auth2.getAuthInstance().isSignedIn.listen(syncEvents());
					}
				});
			}

			function initClient() { //console.log('Client: ' + cal_client + "\n" + 'Key: ' + cal_api + "\n");
				gapi.client.init({
					'clientId': cal_client,
					'apiKey': cal_api,
					'discoveryDocs': [discoveryUrl],
					'scope': scopes
				}).then(function() {

					/* Fetch existing events */
					gapi.client.calendar.events.list({
						'calendarId': 'primary',
						'showDeleted': false,
						'singleEvents': true
					}).then(function(response) {
						existingEvents = response.result.items;
					});

					bindEvents();
				});
			}

			function syncEvents() { //console.log(events);
				let errors = [];

				syncBtn.prop('disabled', true);

				/* Sync events that user is attending */
				for(var i = 0, len = events.length; i < len; i++) { console.log(events[i]);
					let arrayData = fetchEventArray(events[i]);

					if(checkDuplicate(events[i].id)) {
						/* Update existing event */
						gapi.client.calendar.events.patch(arrayData)
						.then(function(response) { //console.log(response);
							syncBtn.prop('disabled', false);
						}).catch(function(error) { //console.log(error);
							syncBtn.prop('disabled', false);
						});
					} else {
						/* Create new event */
						gapi.client.calendar.events.insert(arrayData)
						.then(function(response) { //console.log(response);
							syncBtn.prop('disabled', false);
							
						}).catch(function(error) { //console.log(error);
							switch (error.status) {
								/* Handles duplicated events that was deleted on google calendar */
								case 409:
										errors.push(arrayData);
									break;
							}
							syncBtn.prop('disabled', false);
							showDuplicateEvents(errors, i, len);
						});
					}
				}
			}

			function showDuplicateEvents(errors, index = 0, length = 0) {

				/* Check if any error exist */
				if (errors.length > 0 && index >= length) {
					/* Set variables */
					const modal = $('#alert-modal');

					let details = '';

					/* Set Description */
					details += '<p>Unable to sync the following events because they have been deleted in your Google Calendar.</p>'

					/* Set details */
					errors.forEach(function(error) {

						/* Clean date */
						let startDate = moment(error.start.dateTime).format('MMM D, YYYY');
						let endDate = moment(error.end.dateTime).format('MMM D, YYYY');

						let date = startDate + ' - ' + endDate;

						if (startDate == endDate) {
							date = startDate;
						}

						/* Assemble event details */
						details += '<li> ' + date + ' - ' + error.summary + '</li>';
					});

					/* Set error title and assembled details */
					modal.find('.alert-title').html('Duplicate Events');
					modal.find('.alert-body').html(details);

					/* Show Modal */
					modal.modal('show');

				}

			}

			function checkDuplicate(id) { //console.log(existingEvents);
				/* Check for duplicates, update if so */
				for(var i = 0, len = existingEvents.length; i < len; i++) { //console.log(existingEvents[i]);
					if(existingEvents[i].id == id)
						return true;
				}

				return false;
			}

			function fetchEventArray(e, isUpdate = false) {
				let array = {
					'calendarId': 'primary',
					'id': e.id,
					'eventId': e.id,
					'start': {
						'dateTime': e.googleStart,
						'timeZone': 'Asia/Manila'
					},
					'end': {
						'dateTime': e.googleEnd,
						'timeZone': 'Asia/Manila'
					},
					'summary': e.title
				};


				/* Add ID depending if its for update */
				if(isUpdate) {
					// array['eventId'] = e.id;
				} else {
					// array['Id'] = e.id;
				}


				return array;
			}
		},
	},

	form: {

		/* Default Values */
		modal: '#alert-modal',
		modalContent: null,
		modalTitle: null,
		modalBody: null,

		redirect: null,

		button: 'button[type="submit"]',
		loader: null,		
		isLoading: false,

		successValue: 1,
		failValue: 0,
		loaderClass: 'show',

		onClick: null,

		onStart: null,
		onEnd: null,

		onSuccess: null,
		onFail: null,

		clearForm: false,

		init: function(settings) {

			var $form = settings.form,
				url = settings.url,
				_settings = [],
				_inputs = [];

			/* !important */
			_settings.url = url;
			_settings.vars = [];			
			_settings.inputs = getInputs($form);

			/* Set components */
			_settings.button = ('button' in settings) ? $form.find(settings.button) : $form.find(this.button);
			_settings.loader = ('loader' in settings) ? $form.find(settings.loader) : $form.find(this.loader);
			_settings.isLoading = this.isLoading;

			/* Set alert */
			_settings.modal = ('modal' in settings) ? $(settings.modal) : $(this.modal);
			_settings.modalContent = _settings.modal.find('.alert');			
			_settings.modalTitle = _settings.modal.find('.alert-title');
			_settings.modalBody = _settings.modal.find('.alert-body');

			_settings.redirect = ('redirect' in settings) ? settings.redirect : this.redirect;

			/* Set values */
			_settings.successValue = ('successValue' in settings) ? settings.successValue : this.successValue;
			_settings.failValue = ('failValue' in settings) ? settings.failValue : this.failValue;
			_settings.loaderClass = ('loaderClass' in settings) ? settings.loaderClass : this.loaderClass;

			/* Set functions */
			_settings.onClick = ('onClick' in settings) ? settings.onClick : this.onClick;
			_settings.onStart = ('onStart' in settings) ? settings.onStart : this.onStart;
			_settings.onEnd = ('onEnd' in settings) ? settings.onEnd : this.onEnd;
			_settings.onSuccess = ('onSuccess' in settings) ? settings.onSuccess : this.onSuccess;
			_settings.onFail = ('onFail' in settings) ? settings.onFail : this.onFail;

			/* Set options */
			_settings.clearForm = ('clearForm' in settings) ? settings.clearForm : this.clearForm;

			$form.settings = _settings;


			/* Bind form submit event */
			submitHandler();

			/* Bind all needed events */
			bindEvents();
			bindCallableFunctions();

			return $form;

			function bindEvents() {
				var $this = this;

				$form.settings.button.on('click', function(e) {
					e.preventDefault();

					if($form.settings.onClick) { $form.settings.onClick($(this)); }

					$form.submit();
				});
			}

			function bindCallableFunctions() {
				var $this = this;

				/* Set callable functions */
				$form.on = function(name, value) {
					switch(name) {
						case 'onClick':$form.settings.onClick = value; break;
						case 'onStart':$form.settings.onStart = value; break;
						case 'onEnd':$form.settings.onStart = value; break;
						case 'onSuccess':$form.settings.onClick = value; break;		
						case 'onFail':$form.settings.onStart = value; break;									
						default: break;
					}
				}

				/* Store/Replace form settings */
				$form.set = function(name, value) {
					return $form.settings[name] = value;
				}

				/* Store/Replace form post variables */
				$form.addPostVars = function(name, value) {
					return $form.settings.vars.push({name: name, value: value });
				}
			}

			function submitHandler() {
				var $this = this;

				$form.validate({
					submitHandler: function() {

						if(!$form.settings.isLoading) {

							if($form.settings.onStart) { $form.settings.onStart(); }

							disable();

							/* Get all post vars */
							var vars = $form.serializeArray();
							// console.log(vars);
							/* Add the settings.vars on the post vars */
							$.post($form.settings.url, vars.concat($form.settings.vars), function(data) {
								// console.log(data);
								switch(data.response) {
									case $form.settings.failValue:

										setError(data);

										enable();

										if($form.settings.onFail) { $form.settings.onFail(data); }

									break;
									case $form.settings.successValue:

										/* Set success message */
										setMessage(data.title, data.message);


										if($form.settings.redirect && data.redirectURL) { 

											$('#alert-modal').on('hidden.bs.modal close.bs.alert', function () {
												window.location.href = data.redirectURL;
											});
										}

										if($form.settings.clearForm) { clearForm(); }
										if($form.settings.onSuccess) { $form.settings.onSuccess(data); }

										enable();

									break;
								}

								if($form.settings.onEnd) { $form.settings.onEnd(data); }

							}, 'json').fail(function(data) {
								setError(data);
							});
						}
					},
				});

				function setError(data) {

					var errors = '<p>Kindly check below what seems to be the problem...<p>';

					for(let field in data.responseJSON) {

						/* Check if there are multiple error */
						if(isArray(data.responseJSON[field])) {

							for(let subfield in data.responseJSON[field]) {
								errors += '<span>*</span> ' + data.responseJSON[field][subfield] + '<br>';
							}

						} else {
							errors = data.responseJSON[field];
						}
					}

					/* Set error message */
					setMessage('', errors, true);
				

					enable();

					if($form.settings.onFail) { $form.settings.onFail(data); }						
				}

				function setMessage(title, message, error = false) {
					var classes = 'alert-success',
						title = '<img src="/image/alerts/success.png" class="alert-image"> ' + title;

					/* Check if there is an error*/
					if(error) {

						classes = 'alert-danger';
						title = '<i class="icon fa fa-ban"></i> Oops! Something went wrong!';
					}


					/* Set the modal */
					$form.settings.modalContent.addClass(classes);
					$form.settings.modal.modal('show');

					$form.settings.modalTitle.html(title);
					$form.settings.modalBody.html(message);			
				}

				function enable() {
					$form.settings.button.attr('disabled', false);

					$form.settings.isLoading = false;		
				}

				function disable() {
					$form.settings.button.attr('disabled', true);
					$form.settings.modalContent.removeClass('alert-danger alert-success');
					// $form.settings.loader.removeClass($form.settings.loaderClass);

					$form.settings.isLoading = true;
				}

				function clearForm() {
					$form.trigger('reset');
				}

				function isArray(obj) {
				    return Object.prototype.toString.call(obj) === '[object Array]';
				}
			}

			function getInputs(form) {
				var inputs = [];

				$(form).find('input, textarea, select').each(function() {
				    inputs.push({ name: $(this).attr('name'), type: '' });
				});

				return inputs;
			}
		},
	},
};