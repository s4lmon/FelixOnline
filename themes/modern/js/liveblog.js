window.LiveBlog = (function(LiveBlog, $, SockJS, templates) {
	"use strict";

	var addPost = function(post, animate, reverse) {
		function prefixNumber(number) {
			if(number < 10) {
				number = '0'+number;
			}
			return number;
		}

		var time = new Date(post.timestamp*1000);
		post.time = prefixNumber(time.getHours())+':'+prefixNumber(time.getMinutes());
		var render;
		var postsinnercont = $('.inner-feed');

		if(postsinnercont.data('oldest-post') == null || postsinnercont.data('oldest-post') > post.id) {
			postsinnercont.data('oldest-post') = post.id;
		}

		if(!post.data.type) {
			render = $(template.normal.render(post));
		} else {
			switch(post.data.type) {
				case 'feliximage':
					// TODO: Get image details from AJAX
					post.data.data.picWidth = 0;
					post.data.data.picHeight = 0;
					post.data.data.picTall = '';
					post.data.data.picUrl = '';
					post.data.data.showLink = true;

					if(post.data.data.attattributionLink == '') {
						post.data.data.showLink = false;
					}

					render = $(template.picture.render(post));
					break;
				case 'video':
					// Mustache does not have If support, so we must set some booleans
					if(post.data.data.source == "youtube") {
						post.youtube = true;
						post.vimeo = false;
					} else {
						post.youtube = false;
						post.vimeo = true;
					}
					render = $(template.video.render(post));
					break;   
				case 'quote':
				case 'video':
					render = $(template[post.data.type].render(post));
					break;
				case 'tweet':
					render = $(template.twitter.render(post));
					break;
				default:
					post.data.data.text = micromarkdown.parse(post.data.data.text); // Convert markdown
					render = $(template.normal.render(post));
					break;
			}
		}

		if(animate) {
			if(reverse) {
				postsinnercont.append(render);
			} else {
				postsinnercont.prepend(render);
			}

			render.hide().fadeIn(1000);
		} else {
			if(reverse) {
				postsinnercont.append(render);
			} else {
				postsinnercont.prepend(render);
			}
		}

		return post;
	}

	var getPosts = function(blogId, startAt) {
		var data = {};
		data.action = 'liveblog_archive';
		data.blogId = blogId;
		data.check = 'liveblog-' + blogId + '-token';
		data.token = $('#liveblog-' + blogId + '-token').val();

		if(startAt) {
			data.startAt = startAt;
		}

		$.ajax({
			url: 'ajax.php',
			type: 'post',
			data: data,
			async: true,
			success: function(msg){
				try {
					var message = msg;
				} catch(err) {
					var message = {};
					message.error = err;
					message.reload = false;
				}
				if(message.error) {
					$('#token').val(message.newtoken);

					if(message.reload) {
						location.reload();
					}

					return false;
				}
				
				// Set new token
				$('#token').val(message.newtoken);

				message.posts.forEach(function(post) {
					addPost(post.post, true, true);
				});

				fetchButton.text("Load older posts");
				fetchButton.attr('disabled', false);
		
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
				if(message.error) {
					$('#token').val(message.newtoken);

					if(message.reload) {
						location.reload();
					}
				}

				fetchButton.text("Load older posts");
				fetchButton.attr('disabled', false);

				return false;
			}
		});
	}

	var init = function(url, blogId) {
		var blogId = blogId;
		var socket = null;
		var postsinnercont = $('.inner-feed');

	 // Still to do:
	 // AJAX for images
	 // Key Facts box? maybe
	 // NOTe: need to make article content OPTIONAL

		var template = {
			normal: new Hogan.Template(window.T.post),
			twitter: new Hogan.Template(window.T.posttwitter),
			picture: new Hogan.Template(window.T.postpicture),
			quote: new Hogan.Template(window.T.postquote),
			video: new Hogan.Template(window.T.postvideo)
		}

		var sockettrouble = $('#connection-error');
		var loadposts = $('#loadPosts');
		var postscont = $('.feed');

		function socketRunner() {
			socket = new SockJS(url);

			socket.onopen = function() {
				sockettrouble.fadeOut(1000);
				postscont.removeClass('loading');
				loadposts.css('display', 'block');
			};

			socket.onclose = function() {
				sockettrouble.fadeIn(1000);
			};

			socket.onmessage = function(data) {
				var data = JSON.parse(data.data);
				if(data.type) {
					switch(data.type) {
						case 'post':
							// data.posts = array of all posts
							// need to determine whether post is already displayed
							if($('[data-post-id="' + data.post.id + '"]').length() > 0);
								addPost(data.post, true);
							}
							break;
						case 'delete':
							$('[data-post-id="' + data.delete + '"]').remove();

							if (postsinnercont.data('oldest-post') == data.delete) { // If oldest post deleted
								oldestPost++; // Reflect that the oldest post is now newer
							}
							break;
						default:
							// Unimplemented data type
							break;
					}
				}
			};
		}

		function checkAlive(){
			if(!socket || socket.readyState == 3) {
				socketRunner();
			}

			if(socket && socket.readyState == 1) {
			}
		}

		getPosts(blogId);

		setInterval(checkAlive, 1000);
	}

	var fetchMorePosts = function(blogId) {
		var fetchButton = $('#loadPosts');
		fetchButton.text("Loading...");
		fetchButton.attr('disabled', true);

		getPosts(blogId, $('.inner-feed').data('oldest-post'));
	}

	LiveBlog.init = init;
	LiveBlog.addPost = addPost;
	LiveBlog.getPosts = getPosts;
	LiveBlog.fetchMorePosts = fetchMorePosts;
	return LiveBlog;
})(window.LiveBlog || {}, window.jQuery, window.SockJS, window.T);
