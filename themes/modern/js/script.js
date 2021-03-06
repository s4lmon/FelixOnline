/*
 * Author: J.Kim, P.Kent
 */

$(document).ready(function() {
	/* Image Loading */
	var loadImages = function() {
		$('.article-img-inner, #imgCont img, .article-image-image img').waitForImages({
			each: function() {
				$(this).addClass('loaded').animate({ opacity: 1 }, 400, "linear");
			},
			waitForAll: true
		});
	}

	window.loadImages = loadImages;

	window.loadImages();

	/* Live blogs */
	blogIndicator = $('[data-liveblog]');

	if(blogIndicator.length > 0) {
		LiveBlog.init($(blogIndicator[0]).attr('data-liveblog-url'), $(blogIndicator[0]).attr('data-liveblog-id'));

		$('#loadPosts').text('Load older posts');

		$('#loadPosts').click(function() {
			LiveBlog.fetchMorePosts($(blogIndicator[0]).attr('data-liveblog-id'));
			return false;
		})
	}

	/* Comment Binders - logged in */
	$(document).on("click", '.login-like', function() {
		rateComment(this, 'like');
		return false;
	});
	
	$(document).on("click", '.login-dislike', function() {
		rateComment(this, 'dislike');
		return false;
	});

	function rateComment(cobj, action) {
		var comment = $(cobj).parents('.article-comment').attr('id');
		var token = $('#token-rate-'+comment).val();
		var check = comment+'ratecomment';
		
		data = {};
		data.action = 'rate_comment';
		data.type = action;
		data.comment = comment;
		data.token = token;
		data.check = check;
				
		ajaxHelper(null, 'POST', data, '#likespinner_'+comment+' .loading', ['#comment-'+comment+'-like', '#comment-'+comment+'-dislike'], ['#likespinner_'+comment], null, null, function(data, msg) {
			$('#'+comment).replaceWith(msg.content);
			$('#token-rate-'+comment).val(msg.newtoken);

			init_foundation();
		});

		return false;
	}

	//Reply to comment
	$(document).on("click", '.comment-reply', function() {
		var comment = $(this).parents('.article-comment').attr('id');
		var name = $('#'+comment+'-meta .comment-author-name').first().text().trim();
		var date = $('#'+comment+'-meta .comment-date-date').first().text().trim();
		if ($('#commentReply').length) {
			$('#commentReply').children('#replyLink').html('<b>Replying to:</b> '+name+' at '+date);
			$('#commentReply').children('#replyComment').attr('value', comment);
			$('#commentReply').children('#replyDate').attr('value', date);
		} else {
			var reply = '<div id="commentReply"><span id="replyLink"><b>Replying to:</b> '+name+' at '+date+'</span> <a href="#" id="removeReply"><span class="glyphicons glyphicons-circle-remove" title="Remove reply"></span></a><input type="hidden" id="replyComment" name="replyComment" value="'+comment+'"/><input type="hidden" id="replyDate" name="replyDate" value="'+date+'"/></div>';
			$('#comment').before(reply);
		}
		$('#comment').focus();
		return false;  
	});

	//Post comment
	$(document).on("click", '.comment-form input[type="submit"]', function() {
		var data = {
			"action": "post_comment",
			"token": $('#new-token').val(),
			"check": "new_comment",
			"article": $('.comment-form input[name="article"]').val(),
			"name": $('.comment-form input[name="name"]').val(),
			"email": $('.comment-form input[name="email"]').val(),
			"comment": $('.comment-form textarea[name="comment"]').val(),
			"reply_to": $('.comment-form input[name="replyComment"]').val()
		};

		ajaxHelper(null, 'POST', data, '.comment-form-spin', ['.comment-form'], null, null, '.ajax-comment-error', function(data, response) {
			if(!response.content) {
				$('.ajax-comment-error').text(response.details);
				$('.ajax-comment-error').show();
			} else {
				var cid = $('.comment-form input[name="replyComment"]').val();
				$('#'+cid+' .comment-replies').append(response.content);

				$('#'+response.comment_id)[0].scrollIntoView();
			}

			if(response.clearform) {
				$('.comment-form').trigger("reset");
				$('#commentReply').remove();
			}
		}, '#new-token');

		return false;
	});

	//Remove reply
	$(document).on("click", "#removeReply", function() {
		$(this).parent().remove();
		return false;
	});

	/* Abuse modal */
	$(document).on("click", '.comment-abuse', function() {
		$('#abuseModalBlurbResult').hide();
		$('#abuseModalBlurb').show();
		$('#abuseModalButtons').show();
		$('#abuseModalButtonsResult').hide();
		$('#bad-comment-id').html('');
		var comment = $(this).parents('.article-comment').attr('id');
		$('#bad-comment-id').html(comment);

		$('#abuseModal').foundation('reveal', 'open');

		return false;
	});

	$(document).on("click", '.closeAbusive', function() {
		$('#abuseModal').foundation('reveal', 'close');

		$('#bad-comment-id').html('');

		return false;
	});

	$(document).on("click", '.confirmAbusive', function() {
		abuseComment(this);
		return false;
	});

	function abuseComment(cobj) {
		var comment = $('#bad-comment-id').html();
		var token = $('#token-rate-'+comment).val();
		var check = comment+'ratecomment';

		data = {};
		data.comment = comment;
		data.token = token;
		data.check = check;
		data.action = 'report_abuse';
		
		call = reportAjaxCallback;

		ajaxHelper(null, 'POST', data, '#abuseModalBlurbWait', ['#abuseModalBlurb', '#abuseModalButtons'], null, null, null, call);

		function reportAjaxCallback(data, msg) {
			$('#abuseModalBlurbResult').html(msg.msg);
			$('#abuseModalBlurbResult').show();
			$('#abuseModalBlurb').hide();
			$('#abuseModalButtons').hide();
			$('#abuseModalButtonsResult').show();
			$('#token-rate-'+comment).val(msg.newtoken);
		}

		return false;
	}

	//Poll vote
	$(document).on("click", '.poll-option', function() {
		var pollid = $(this).attr('data-poll');

		var data = {
			"action": "poll_vote",
			"token": $('#poll-token').val(),
			"check": "poll_vote",
			"article": $(this).attr('data-article'),
			"poll": $(this).attr('data-poll'),
			"option": $(this).attr('data-option')
		};

		ajaxHelper(null, 'POST', data, '.poll-spin-'+pollid, ['.poll-form-'+pollid], null, null, null, function(data, response) {
			$('.poll-area-'+pollid).each(function() {
				$(this).html(response.content);	
			});
		}, '#poll-token');

		return false;
	});

	/* PAGINATION */

	//Paginator page - click
	$(document).on("click", 'ul.pagination li a', function() {
		var item = $(this);

		return handlePaginator(item, $("#pag-category").val(), $("#pag-headshot").val(), function(data, json) {
			$('.paginator-bit').html(json.paginator);

			$('#month-viewer').data('final-month', '');
			$('#month-viewer').html('');

			process_dateview(json);

			$('html, body').animate({
				scrollTop: $("#month-viewer").offset().top
			}, 500);
		});
	});

	function process_dateview(json) {
		for(var month in json.articles) {
			// Assess whether to create a new area for articles
			if($('#month-viewer').data('final-month') != month) {
				// Create area
				monthinfo = month.split('-');

				months = {'01': 'January', '02': 'February', '03': 'March', '04': 'April', '05': 'May', '06': 'June', '07': 'July', '08': 'August', '09': 'September', '10': 'October', '11': 'November', '12': 'December'};

				monthname = months[monthinfo[1]] + ' ' + monthinfo[0];

				$('#month-viewer').append('<hr class="month-divider fade '+json.cat+'"><div class="row fade full-width"><div class="small-12 columns"><p class="section-date '+json.cat+'">'+monthname+'</p></div></div><div class="row full-width date-row" data-equalizer="'+month+'" id="'+month+'"></div>');

				$('#month-viewer').data('final-month', month);
			}

			// Remove end tags
			$('#'+month).find('.date-article').removeClass('end');

			// Add articles
			json.articles[month].forEach(function(article) {
				$('#'+month).append('<div class="small-12 large-4 columns fade date-article">'+article+'</div>');
			});

			// Add end tag
			$('#'+month).find('.date-article').last().addClass('end');

			window.loadImages();
		}

		init_foundation();

		setTimeout(function() { init_foundation(); }, 500); // Wait for end of reflow
	}

	//Paginator page - scroll
	$(document).scroll(function() {
		if($('ul.pagination li a.next').visible(false, true)) {
			var item = $('ul.pagination li a.next');

			return handlePaginator(item, $("#pag-category").val(), $("#pag-headshot").val(), function(data, json) {
				$('.paginator-bit').html(json.paginator);

				process_dateview(json);
			});
		}

	});

	// Callback runs at end of pagination ajax
	function handlePaginator(item, categories, headshots, callback) {
		if(!item.attr('data-page') || !item.attr('data-type') || !item.attr('data-key')) {
			return true;
		}

		switch(item.attr('data-type')) {
			case 'category':
				var action = "get_category_page";
				break;
			case 'user':
				var action = "get_user_page";
				break;
			case 'search':
				var action = "get_search_page";
				break;
			case 'topic':
				var action = "get_topic_page";
				break;
			default:
				return true; // not supported yet
				break;
		}

		// Run ajax
		data = {
			"action": action,
			"token": $('#token').val(),
			"check": "pagination",
			"key": item.attr('data-key'),
			"page": item.attr('data-page'),
			"categories": categories,
			"headshots": headshots
		};

		ajaxHelper(null, 'POST', data, '.pagination-spin', ['.pagination'], null, null, null, callback);

		return false;
	}
	
	/* AJAX Helper */

	function ajaxHelper(form, method, data, spinner, hideme, showme, successbox, failbox, callback, token_name, endpoint) {
		method = method || 'POST';
		data = data || {};
		spinner = spinner || null;
		hideme = hideme || {};
		showme = showme || {};
		successbox = successbox || null;
		failbox = failbox || null;
		callback = callback || null;
		token_name = token_name || "#token";
		endpoint = endpoint || "ajax.php";
		
		// hidemes are hidden during the AJAX call and are shown again at the end
		// showme are shown at the beginning of the call and are hidden at the end
		// Ignore spinners here
		function hideStart(hideme, showme, spinner) {
			jQuery.each(hideme, function(index, obj) {
				$(obj).hide();
			});
			
			jQuery.each(showme, function(index, obj) {
				$(obj).show();
			});
			
			if(successbox) {
				$(successbox).show();
			}

			if(failbox) {
				$(failbox).hide();
			}

			if(spinner != null) {
				$(spinner).show();
			}
		}
	
		function hideEnd(hideme, showme, spinner) {
			jQuery.each(hideme, function(index, obj) {
				$(obj).show();
			});
			
			jQuery.each(showme, function(index, obj) {
				$(obj).hide();
			});
			
			if(spinner != null) {
				$(spinner).hide();
			}
		}
		
		function error(error, failbox) {
			if(failbox != null) {
				$(failbox).text(error);
				$(failbox).show();
			} else {
				alert(error);
			}
		}
		
		function handleValidation(fields, form) {
			jQuery.each(fields, function(index, obj) {
				if($("#"+form+" input[name="+obj+"]").length != 0) {
					$("#"+form+" input[name="+obj+"]").addClass('invalidField');
				} else {
					$("#"+form+" #"+obj).addClass('invalidField');
				}
			});
		}
				
		hideStart(hideme, showme, spinner);
		
		$.ajax({
			url: endpoint,
			type: method,
			data: data,
			async:true,
			success: function(msg){
				try {
					var message = msg;
				} catch(err) {
					var message = {};
					message.error = err;
					message.reload = false;
				}
				if(message.error) {
					if(message.validator) {
						handleValidation(message.validator_data, form);
					}
					
					error(message.details, failbox);

					if(message.reload) {
						location.reload();
					}
					hideEnd(hideme, showme, spinner);
					$(token_name).val(message.newtoken);
					return false;
				}
				
				// Set new token
				$(token_name).val(message.newtoken);

				hideEnd(hideme, showme, spinner);
				
				// Run callback if one exists
				if(callback) {
					callback(data, message);
				}

				if(successbox != null) {
					if(data.success != '') {
						$(successbox).text(message.success);
					} else {
						$(successbox).text('Success');
					}
					
					$(successbox).show();
				}
								
				return true;
			},
			error: function(msg){
				try {
					var message = JSON.parse(msg.responseText);
				} catch(err) {
					var message = {};
					message.error = err;
					message.reload = false;
				}

				if(message.validator) {
					handleValidation(message.validator_data, form);
				}

				error(message.details, failbox);

				if(message.reload) {
					location.reload();
				}
				hideEnd(hideme, showme, spinner);
				$(token_name).val(message.newtoken);
				return false;
			}
		});
	}


	$('input').change(function() {
		$(this).removeClass('invalidField');
	});
	$('textarea').change(function() {
		$(this).removeClass('invalidField');
	});

	/* User Profile */

	$(document).on("click", '#editProfileSubmit', function() {
		var data = {};
		data.facebook = $('.profile-facebook').val();
		data.twitter = $('.profile-twitter').val();
		if($('.profile-email').is(':checked')) { data.email = 1 } else { data.email = 0 };
		data.webname = $('.profile-webname').val();
		data.weburl = $('.profile-weburl').val();
		data.bio = $('.profile-bio').val();
		if($('.profile-ldap').is(':checked')) { data.ldap = 1 } else { data.ldap = 0 };
		data.action = 'profile_change';
		data.token = $('#edit_profile_token').val();
		data.check = 'edit_profile';

		ajaxHelper('profileform', 'POST', data, '#profile-spinner', ['#profile-saver'], ['#profile-saver'], null, null, profileAjaxCallback);

		function profileAjaxCallback(data, message) {
			location.reload();
		}

		return false;
	});

	/* Contact Form */
	$("#contactform").submit(function() {
		$("#contactform label.error").hide();
		var name = $('#contactform #name').val();
		var email = $('#contactform #email').val();
		var message = $('#contactform #message').val();
		var token = $('#contactform').find('#token').val();
		var check = 'generic_page';

		data = {};
		data.action = 'contact_us';
		data.name = name;
		data.email = email;
		data.message = message;
		data.token = token;
		data.check = check;
		
		ajaxHelper(
			'contactform',
			'POST',
			data,
			'#contactform #sending',
			['#contactform #submit'],
			null,
			'#sent',
			null,
			function(data, message) {
				$('#contactform').hide();
			}
		);

		return false;
	});

	if(!Modernizr.svg) {
		$('img[src*="svg"]').attr('src', function() {
			return $(this).attr('src').replace('.svg', '.png');
	});
}
});