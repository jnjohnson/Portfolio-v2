var slider = {
	instances: [],

	init: function(opts) {
		if (typeof opts == 'undefined')
			opts = {};
		if (typeof opts.selector == 'undefined')
			opts.selector = '.slider';
		if (typeof opts.drag == 'undefined')
			opts.drag = 2;
		if (typeof opts.min_speed == 'undefined')
			opts.min_speed = 1;
		if (typeof opts.slide_spacing == 'undefined')
			opts.slide_spacing = 0;
		if (typeof opts.move_to_nearest_mode == 'undefined')
			opts.move_to_nearest_mode = 'center';
		if (typeof opts.infinite_scroll == 'undefined')
			opts.infinite_scroll = true;
		if (typeof opts.after_create == 'undefined')
			opts.after_create = function(slider_instance) {};
		if (typeof opts.index_update == 'undefined')
			opts.index_update = function(slider_instance, index) {};
		if (typeof opts.show_prev_next == 'undefined')
			opts.show_prev_next = true;
		if (typeof opts.show_buttons == 'undefined')
			opts.show_buttons = true;
		if (typeof opts.zoom_button == 'undefined')
			opts.zoom_button = false;
		if (typeof opts.fullscreen_button == 'undefined')
			opts.fullscreen_button = false;
			
		jQuery(opts.selector).each(function() {		
			slider.instances.push(new slider.slider_instance(jQuery(this), opts));
		});	
	},

	slider_instance: function(jq, opts) {
		if (jq.hasClass('slider-init'))
			return;
		
		var me = this;

		var slides = [];
		var jq_width = 0;
		var container = jq.find('ul');
		var jq_win = jQuery(window);
		var jq_btns = false;
		var left_position = 0;
		var tmr_impulse = 0;
		var tmr_nearest = 0;
		var max_position = 0;
		var max_item_position = 0;
		var extra_before = [];
		var extra_before_width = 0;
		var extra_after = [];
		var extra_after_width = 0;

		me.get_jq = function() {
			return jq;
		};

		me.prev = function() {
			var cur = this.get_current_index();
			var prev_index = cur - 1;
			if (prev_index < 0)
				prev_index = slides.length - 1;

			if (opts.infinite_scroll)
				me.move_to_index(prev_index, -1);
			else
				me.move_to_index(prev_index, prev_index < cur ? -1 : 1)
		};

		me.next = function() {
			var cur = this.get_current_index();
			var next_index = cur + 1;
			if (next_index >= slides.length)
				next_index = 0;
			if (opts.infinite_scroll)
				me.move_to_index(next_index, 1);
			else
				me.move_to_index(next_index, next_index < cur ? -1 : 1)
		};

		/*
			direction - 1 - closest position to right
						-1 - closest position to left
		*/
		me.move_to_index = function(new_index, direction, instant) {
			if (slides.length < 2)
				return;
			if (typeof instant == 'undefined')
				instant = false;

			var cur_index = this.get_current_index();	
			if (cur_index == new_index && !instant) {
				return;
			}	

			var distance_from_left = new_index < cur_index ? (cur_index - new_index) : (slides.length + cur_index - new_index);
			var distance_from_right = new_index > cur_index ? (new_index - cur_index) : (slides.length + new_index - cur_index);
			
			var total_length = 0;
			if (direction == 1) {
				var i = cur_index;
				for(var n=0; n<distance_from_right; n++) {
					total_length += slides[i].width() + opts.slide_spacing;
					i++;
					if (i >= slides.length) {
						i = 0;
					}
				}
			} else {
				var i = cur_index;
				for(var n=0; n<distance_from_left; n++) {
					total_length -= slides[i].width() + opts.slide_spacing;
					i--;
					if (i < 0) {
						i = slides.length - 1;
					}
				}
			}

			move_to_nearest_animation(left_position - total_length, instant);

			me.update_index(new_index);
		}

		me.get_slides = function() {
			return slides;
		};

		me.update_index = function(new_index) {
			if (typeof new_index == 'undefined') {
				new_index = me.get_current_index();
			}
			if (opts.show_buttons && jq_btns) {
				jq_btns.find('.active').removeClass('active');
				jq_btns.find('a:nth-child(' + (new_index+1) + ')').addClass('active');
			}

			opts.index_update(me, new_index);
		}
		me.impulse = function(amt) {
			impulse(amt);
		}

		me.get_current_index = function() {
			var min_index = 0;

			if (opts.move_to_nearest_mode == 'center') {
				var center_pos = jq_width/2 - left_position;
				var min_distance = -1;
				for(var i in slides) {
					var slide_pos = slides[i].position().left + slides[i].width()/2;
					var dist = Math.abs(center_pos - slide_pos);
					if (min_distance == -1 || dist < min_distance) {
						min_distance = dist;
						min_index = i;
					}
				}
				if (extra_before.length) {
					for(var i in extra_before) {
						var slide_pos = extra_before[i].position().left + extra_before[i].width()/2;
						var dist = Math.abs(center_pos - slide_pos);
						if (dist < min_distance) {
							min_index = (slides.length - (i % slides.length));
							min_distance = dist;
						}
					}
				}
				if (extra_after.length) {
					for(var i in extra_after) {
						var slide_pos = extra_after[i].position().left + extra_after[i].width()/2;
						var dist = Math.abs(center_pos - slide_pos);
						if (dist < min_distance) {
							min_index = i % slides.length;
							min_distance = dist;
						}
					}
				}
			} else if (opts.move_to_nearest_mode == 'left') {
				var left_pos = -left_position;
				var min_distance = -1;
				for(var i in slides) {
					var slide_pos = slides[i].position().left;
					var dist = Math.abs(left_pos - slide_pos);
					if (min_distance == -1 || dist < min_distance) {
						min_distance = dist;
						min_index = i;
					}
				}
				if (extra_before.length) {
					for(var i in extra_before) {
						var slide_pos = extra_before[i].position().left;
						var dist = Math.abs(left_pos - slide_pos);
						if (dist < min_distance) {
							min_index = (slides.length - (i % slides.length));
							min_distance = dist;
						}
					}
				}
				if (extra_after.length) {
					for(var i in extra_after) {
						var slide_pos = extra_after[i].position().left;
						var dist = Math.abs(left_pos - slide_pos);
						if (dist < min_distance) {
							min_index = i % slides.length;
							min_distance = dist;
						}
					}
				}
			}

			return ~~min_index;
		};
		function create_zoom_button(slide) {
			var btn_zoom = jQuery('<a href="#" class="btn-zoom"></a>');
			btn_zoom.click(function(ev) {
				var src = slide.css('background-image');
				src = src.replace('url(','').replace(')','').replace(/\"/g, "").replace(/\'/g, "");

				new image_zoom(jq, src);

				ev.stopPropagation();
				ev.preventDefault();
				return false;
			});					

			return btn_zoom;
		}

		function create_fullscreen_button() {
			var is_fullscreen = false;
			var btn = jQuery('<a href="#" class="btn-fullscreen"></a>');
			btn.click(function() {
				is_fullscreen = !is_fullscreen;

				if (is_fullscreen) {
					jq.addClass('fullscreen');
					var index = me.get_current_index();
					jq.css({
						position: 'fixed',
						top: 0,
						left: 0,
						right: 0,
						bottom: 0,
						height: 'auto',
						width: 'auto'
					});
					jq.find('.container').css('height', '100%');
					jq.find('ul').css('height', '100%');
					jq.find('li').each(function() {
						jQuery(this).css({width: '100%'});
					});
					jq_width = 0;
					jq_win.resize();
					me.move_to_index(index, 1, true);
					impulse(0.5);
				} else {
					jq.removeClass('fullscreen');
					var index = me.get_current_index();
					jq.css({
						position: '',
						top: '',
						left: '',
						right: '',
						bottom: '',
						height: '',
						width: ''
					});
					jq.find('.container').css('height', '');
					jq.find('ul').css('height', '100%');
					jq.find('li').each(function() {
						jQuery(this).css({width: ''});
					});
					jq_width = 0;
					jq_win.resize();
					me.move_to_index(index, 1, true);
					impulse(0.5);
				}
				return false;
			});

			jq.append(btn);
		}
		function init() {
			jq.addClass('slider-init').css('position', 'relative');
			container.css({
				position: 'absolute',
				top: 0,
				left: 0,
				transform: 'translateX(0px)',
				width: '100%',
				height: '100%',
				display: 'block',
				'list-style': 'none'
			});
			container.wrap('<div class="container" style="position:relative;overflow:hidden;width:100%;" />');

			container.find('li').each(function() {
				var slide = jQuery(this);
				slide.css({
					position: 'absolute',
					'list-style':'none',
					display: 'block',
					height: '100%',
					'background-size':'cover',
					'background-repeat':'no-repeat',
					'background-position':'center'
				});
				slides.push(slide);

				if (opts.zoom_button) {
					slide.append(create_zoom_button(slide));
				}
			});

			if (opts.fullscreen_button) {
				create_fullscreen_button();
			}
			
			calculate();

			position();

			width_listener();

			touch_listener();

			adjust_left_position();

			move_to_nearest(true, true);

			check_infinite_slides();

			create_navigation_buttons();

			opts.after_create(me);
		}
		function create_navigation_buttons() {
			if (opts.show_prev_next && slides.length > 1) {
				var btn_prev = jQuery('<a href="#" class="btn-prev"></a>');
				btn_prev.click(function() {
					me.prev();
					return false;
				});

				var btn_next = jQuery('<a href="#" class="btn-next"></a>');
				btn_next.click(function() {
					me.next();
					return false;
				});
				jq.append(btn_prev, btn_next);
			}
			if (opts.show_buttons && slides.length > 1) {
				function create_button(index) {
					var btn = jQuery('<a href="#"></a>');
					btn.click(function() {
						me.move_to_index(index, index > me.get_current_index() ? 1 : -1);
						return false;
					});
					if (index == 0)
						btn.addClass('active');
					return btn;
				}

				jq_btns = jQuery('<div class="buttons" />');
				for(var i=0; i<slides.length; i++) {
					jq_btns.append(create_button(i));
				}
				jq.append(jq_btns);
			}
		}
		function check_infinite_slides() {
			if (opts.infinite_scroll && slides.length) {
				extra_before_width = 0;
				for(var i in extra_before) {
					var w =  extra_before[i].width() + opts.slide_spacing;
					extra_before[i].css({left: -extra_before_width - w});
					extra_before_width += w;
				}
				while(extra_before_width < jq_width*2) {
					var next_index = slides.length - 1 - extra_before.length;
					while(next_index < 0)
						next_index += slides.length;
					
					var new_slide = slides[next_index].clone(true,true);
					new_slide.addClass('extra' + extra_before.length);
					container.prepend(new_slide);
					var w = new_slide.width() + opts.slide_spacing;
					new_slide.css({left: -extra_before_width -w});
					extra_before_width += w;
					extra_before.push(new_slide);
				}

				extra_after_width = 0;
				for(var i in extra_after) {
					extra_after[i].css({left:max_position + extra_after_width});
					var w =  extra_after[i].width() + opts.slide_spacing
					extra_after_width += w;
				}
				while(extra_after_width < jq_width*2) {
					var next_index = extra_after.length;
					while(next_index >= slides.length)
						next_index -= slides.length;
					
					var new_slide = slides[next_index].clone(true,true);
					new_slide.addClass('extra' + extra_after.length);
					container.append(new_slide);
					new_slide.css({left:max_position + extra_after_width});
					var w = new_slide.width() + opts.slide_spacing;
					extra_after_width += w;
					extra_after.push(new_slide);
				}
			}
		}

		function move_container(left) {
			
			if (!opts.infinite_scroll) {
				if (left > 0) {
					left = 0;	
				} else {
					if (typeof opts.limit_max_position == 'undefined') {
						if (left < -max_item_position)
							left = -max_item_position;
					} else {
						var n = opts.limit_max_position();
						if (left < -max_item_position + n)
							left = -max_item_position + n;
					}
				}
			}
			container.css({
				transform: 'translateX(' + ~~left + 'px)'
			});
			if (typeof opts.x_update != 'undefined')
				opts.x_update(~~left, max_position);
		}
		function move_to_nearest_animation(new_position, instant) {
			if (instant) {
				left_position = new_position;
				move_container(left_position);
				me.update_index();
			} else {
				var iterations = 20;
				var move_delta = (new_position - left_position) / iterations;
				if (move_delta < 1 && move_delta > 0) {
					move_delta = 1;
				} else if (move_delta > -1 && move_delta < 0) {
					move_delta = -1;
				}
				var i = 0;
				clearInterval(tmr_nearest);
				tmr_nearest = setInterval(function() {
					if (i < iterations) {
						left_position += move_delta;
						adjust_left_position();
						i++;
						move_container(left_position);
					} else {
						me.update_index();
						clearInterval(tmr_nearest);
					}
				}, 16);
			}
		}
		function move_to_nearest(instant, first_index) {
			if (typeof instant == 'undefined')
				instant = false;
			if (typeof first_index == 'undefined')
				first_index = false;

			if (opts.move_to_nearest_mode == 'center') {
				var center_pos = jq_width/2 - left_position;
				var min_distance = -1;
				var min_slide_pos = 0;
				for(var i in slides) {
					var slide_pos = slides[i].position().left + slides[i].width()/2;
					var dist = Math.abs(center_pos - slide_pos);
					if (first_index) {
						min_slide_pos = slide_pos;
						break;
					} else if (min_distance == -1 || dist < min_distance) {
						min_distance = dist;
						min_slide_pos = slide_pos;
					}
				}
				if (!first_index) {
					if (extra_before.length) {
						for(var i in extra_before) {
							var slide_pos = extra_before[i].position().left + extra_before[i].width()/2;
							var dist = Math.abs(center_pos - slide_pos);
							if (dist < min_distance) {
								min_slide_pos = slide_pos;
								min_distance = dist;
							}
						}
					}
					if (extra_after.length) {
						for(var i in extra_after) {
							var slide_pos = extra_after[i].position().left + extra_after[i].width()/2;
							var dist = Math.abs(center_pos - slide_pos);
							if (dist < min_distance) {
								min_slide_pos = slide_pos;
								min_distance = dist;
							}
						}
					}
				}

				move_to_nearest_animation(-min_slide_pos + jq_width/2, instant);
			} else if (opts.move_to_nearest_mode == 'left') {
				var left_pos = -left_position;
				var min_distance = -1;
				var min_slide_pos = 0;
				for(var i in slides) {
					var slide_pos = slides[i].position().left;
					var dist = Math.abs(left_pos - slide_pos);
					if (first_index) {
						min_slide_pos = slide_pos
						break;
					} else if (min_distance == -1 || dist < min_distance) {
						min_distance = dist;
						min_slide_pos = slide_pos;
					}
				}
				if (!first_index) {
					if (extra_before.length) {
						for(var i in extra_before) {
							var slide_pos = extra_before[i].position().left;
							var dist = Math.abs(left_pos - slide_pos);
							if (dist < min_distance) {
								min_slide_pos = slide_pos;
								min_distance = dist;
							}
						}
					}
					if (extra_after.length) {
						for(var i in extra_after) {
							var slide_pos = extra_after[i].position().left;
							var dist = Math.abs(left_pos - slide_pos);
							if (dist < min_distance) {
								min_slide_pos = slide_pos;
								min_distance = dist;
							}
						}
					}
				}

				move_to_nearest_animation(-min_slide_pos, instant);
			}
		}
		function calculate() {
			jq_width = jq_win.width();
		}
		function position() {
			var left = 0;
			for(var i in slides) {
				slides[i].css({left: left});

				max_item_position = left;
				left += slides[i].width() + opts.slide_spacing;
			}

			max_position = left;
		}
		function width_listener() {
			jq_win.resize(function() {
				var last_width = jq_width;
				calculate();

				if (last_width != jq_width) {
					position();
					move_to_nearest(true);
					check_infinite_slides();
				}
			});
		}
		function adjust_left_position() {
			if (opts.infinite_scroll && slides.length) {
				if (left_position > 0) {
					left_position -= max_position;
				} else if (left_position < -max_position) {
					left_position += max_position;
				}
			}
		}
		function impulse(speed) {
			if (speed > 4)
				speed = 4;
			else if (speed < -4)
				speed = -4;

			speed *= (1000 / 32);

			var drag = speed > 0 ? opts.drag : -opts.drag;
			stop_impulse();
			tmr_impulse = setInterval(function() {
				if ((drag > 0 && speed < opts.min_speed) || (drag < 0 && speed > -opts.min_speed)) {
					stop_impulse();
					move_to_nearest();
					me.update_index();
				} else {
					if (isNaN(speed) || isNaN(drag)) {
						speed = 0.1;
						drag = 0.1;
					}
					speed -= drag;
					left_position += speed;
					adjust_left_position();
					move_container(left_position);
				}

			}, 16);
		}
		function stop_impulse() {
			clearInterval(tmr_impulse);
		}
		function touch_listener() {
			var start_x = 0;
			var start_time = 0;
			var last_delta = 0;
			track_movement(jq.children('.container'),
				function(x, y) {
					start_x = x;
					last_delta = 0;
					start_time = Date.now();
					stop_impulse();
					clearInterval(tmr_nearest);
				},
				function (x_delta, y_delta, x, y) {
					last_delta = x - start_x;
					move_container(left_position + last_delta);
				},
				function() {
					left_position += last_delta;
					var elapsed = Date.now() - start_time;
					if (elapsed < 500) {
						var speed = last_delta / elapsed;
						impulse(speed);
					} else {
						adjust_left_position();
						move_to_nearest();
					}
				},
				true, true
			);
		}

		init();

		me.update_index(0);

		return me;
	}
}
function wait_for_dimensions(jq, callback, iterations) {
	if (typeof iterations == 'undefined')
		iterations = 0;
		
	var item = jq.get(0);
	if (typeof item.naturalWidth != 'undefined' && item.naturalWidth > 0) {
		callback(item.naturalWidth, item.naturalHeight, iterations);	
	} else {		
		var me = this;
		//slowly increase the time between each call
		setTimeout(function() {
			wait_for_dimensions(jq, callback, ++iterations);
		}, iterations + 1);
	}
}
function image_zoom(container, src) {
	var min_zoom = 1.5;
	var max_zoom = 2.5;
	var zoom = jQuery('<div class="image-zoom" style="position: absolute;z-index: 10000;top: 0;left: 0;right: 0;bottom: 0;overflow: hidden;background:black;"/>');
	var img = jQuery('<img src="' + src + '" style="position:absolute;top:0;left:0;">');
	var btn_close = jQuery('<div class="image-zoom-close" style="position:absolute;z-index:10001;top:0;right:0;cursor:pointer;"><span>X</span></div>');
	var running = true;
	
	zoom.append(img, btn_close);
	container.append(zoom);

	btn_close.click(function() {
		if (running) {
			running = false;
			zoom.remove();
		}
	});

	wait_for_dimensions(img, function(width, height) {
		var container_width = zoom.width();
		var container_height = zoom.height();

		var max_width = container_width * max_zoom;
		var max_height = container_height * max_zoom;
		var min_width = container_width * min_zoom;
		var min_height = container_height * min_zoom;

		if (width > max_width) {
			height = (max_width/width)*height;
			width = max_width;
		} else if (width < min_width) {
			height = (min_width/width)*height;
			width = min_width;
		}
		if (height > max_height) {
			width  = (max_height/height)*width;
			height = max_height;
		} else if (height < min_height) {
			width  = (min_height/height)*width;
			height = min_height;
		}

		var top = (container_height - height) / 2;
		var left = (container_width - width) / 2;
		var min_left = -width + container_width;
		var min_top = -height + container_height;
		var updated_left = 0;
		var updated_top = 0;

		img.css({
			width: width,
			height: height,
			top: top,
			left: left
		});

		track_movement(zoom, function() {

		}, function(x_delta, y_delta) {
			updated_top = top + y_delta;
			updated_left = left + x_delta;

			if (updated_top > 0)
				updated_top = 0;
			else if (updated_top < min_top)
				updated_top = min_top;
			if (updated_left > 0)
				updated_left = 0;
			else if (updated_left < min_left)
				updated_left = min_left;

			img.css({
				top: updated_top,
				left: updated_left
			});
		}, function() {
			top = updated_top;
			left = updated_left;
		}, true);
	});
}

function track_movement(jq, start_callback, move_callback, release_callback, track_mouse, ignore_y) {
 	var is_down = false;
 	var start_x = 0;
 	var start_y = 0;

 	if (typeof ignore_y == 'undefined')
 		ignore_y = false;
 	
 	if (typeof track_mouse == 'undefined')
		 track_mouse = false;
		 
	if (track_mouse)
		 jq.mouseleave(function() {
			 if (is_down) {
				 is_down = false;
				 setTimeout(function() {
					 jq.removeClass('noclick');
				 }, 125);
				 release_callback();
			 }
		 });
 	
 	if (track_mouse)
	jq.on('mousedown', function(ev) {
		is_down = true;
 		start_x = ev.clientX;
 		start_y = ev.clientY;
 		start_callback(start_x, start_y);
 		return false;
	});	
	
	if (track_mouse)
	jq.on('mouseup', function(ev) {
		if (is_down) {
			is_down = false;
			setTimeout(function() {
				jq.removeClass('noclick');
			}, 125);
			release_callback();
		}
	});	
	
	if (track_mouse)
	jq.on('mousemove', function(ev) {
		if (is_down) {
         	var x_delta = ev.clientX - start_x;
			var y_delta = ev.clientY - start_y;
			move_callback(x_delta, y_delta, ev.clientX, ev.clientY);
			jq.addClass('noclick');
			return false;			
		}		
	});
	
	jq.on('touchstart', function(ev) {
		var touches = ev.originalEvent.touches;
		if (touches.length >= 1) {
			start_x = touches[0].clientX;
			start_y = touches[0].clientY;
			is_down = true;
			start_callback(start_x, start_y);
		}
	});
	jq.on('touchend', function(ev) {
		if (is_down) {
			is_down = false;
			release_callback();
		}
	});
	jq.on('touchmove', function(ev) {
		var touches = ev.originalEvent.touches;
		if (touches.length >= 1) {
			var x_delta = touches[0].clientX - start_x;
			var y_delta = touches[0].clientY - start_y;
			if (ignore_y && (
					Math.abs(y_delta) > Math.abs(x_delta)
				)) {
				return true;
			}
			move_callback(x_delta, y_delta, touches[0].clientX, touches[0].clientY);
			ev.stopImmediatePropagation();
			ev.preventDefault();
			
			return false;
		}
	});
}


var FormRules = {
	//form has fld-form-rules
	//fields have .fld-validate-me and data-validate-type
	ruleSets: [],
	
	init: function(){
		var me = this;
		jQuery('form.fld-form-rules').each(function(index, value){
			jQuery(this).attr('data-validation-id', index);
			me.ruleSets[index] = {
				form: jQuery(this),
				fields: [],
			};
			me.ruleSets[index].form.find('.fld-validate-me:not(.customselect)').each(function(fieldIndex, fieldValue){
				var field = jQuery(this);
				field.attr('data-validation-id', fieldIndex);

				var type = field.data('validate-type');

				if (typeof type == "undefined" || type == "") {
					var field_type = field.attr('type');
					if (field.is('select')) {
						type = 'dropdown';
					} else if (field_type == 'email') {
						type = 'email';
					} else if (field_type == 'checkbox') {
						type = 'checkbox';
					} else {
						type = 'text';
					}
				}


				var msg = '';

				if (type == 'email') {
					msg = '*Oops! That&rsquo;s not a valid email address.';
				} else if (type == 'text') {
					msg = '*Please fill out this field.';
				} else if (type == 'checkbox' || type == 'checklist') {
					msg = '*This is a required field.';
				} else if (type == 'dropdown') {
					msg = '*Please select an item in the list.';
				} else if (type == 'credit_card') {
					msg = '*Oops! That&rsquo;s not a valid credit card number. Please enter the number only, no spaces or dashes.';
				}

				var error_text = false;
				if (msg.length > 0) {
					error_text = jQuery('<span class="error-text" style="display:none">' + msg + '</span>');
					if (type == 'checklist' && !jQuery(this).hasClass('selection-boxes')) {
						jQuery(this).append(error_text);
					} else if (type == 'checkbox' || type == 'dropdown') {
						//jQuery(this).parent().after(error_text);
						jQuery(this).after(error_text);
					} else {
						jQuery(this).after(error_text);
					}
				}

				me.ruleSets[index].fields[fieldIndex] = {
					field: field,
					type: type, //text, dropdown, email, credit_card, checkbox, checklist
					error_text: error_text
				};
				field.blur(function(){
					me.validateField(me.ruleSets[index].fields[fieldIndex], false);
				});
				field.change(function(){
					me.validateField(me.ruleSets[index].fields[fieldIndex], false);
				});
				field.keyup(function(){
					me.validateField(me.ruleSets[index].fields[fieldIndex], false);
				});
			});
		});
	},
	
	validate: function(formJq) {
		var me = this;
		var ruleSet = formJq.data('validation-id');
		var valid = true;
		for(var i = 0; i < me.ruleSets[ruleSet].fields.length; i++){
			var data = me.ruleSets[ruleSet].fields[i];
			var result = me.validateField(data, valid);
			if(!result){
				valid = false;
			}
		}
		
		return valid;
	},
	
	validateField: function(data, bFocus){
		var field = data.field;
		var field_val = "";

		if (typeof data.type == 'undefined') {
			data.type = 'text';
		}

		if (field.data('validate-if-visible')) {
			if (!field.is(':visible')) {
				if (data.error_text) {
					data.error_text.hide();
				}
				return true;
			}
		}

		
		if (data.type == 'text' || data.type == 'dropdown' || data.type == 'email' || data.type == 'credit_card') {
			field_val = field.val();
			
			if (field.data('default_text') == field_val) {
				field_val = "";
			}
			
		} else if (data.type == 'checkbox') {
			if (field.prop('checked'))
				field_val = field.val();
			else
				field_val = "";			
		} else if (data.type == 'checklist') {
			var list = field.find('input');
			var values = [];
			for(var i=0; i<list.length; i++) {
				var chk = jQuery(list[i]);
				if (chk.prop('checked'))
     				values.push(chk.attr('value'));
			}
		}
				
		var valid = false;
		if (data.error_text) {
			data.error_text.hide();
		}
		
		if (data.type == 'dropdown') {
			if (field_val == "") {
				field.addClass('error');
				if(field.parent().children('.customselect').length > 0)
					field.parent().children('.customselect').addClass('error');
				valid = false;
				if (bFocus)
					field.focus();
			} else {
				field.removeClass('error');
				if(field.parent().children('.customselect').length > 0)
					field.parent().children('.customselect').removeClass('error');
				valid = true;
			}		
		} else if (data.type == 'text') {
			if ((field_val == "") ||
				(typeof data.minlength != 'undefined' && field_val.length < data.minlength)) {
				field.addClass('error');	
				valid = false;
				if (bFocus)
					field.focus();
			} else {
				field.removeClass('error');	
				valid = true;
			}
		} else if (data.type == 'credit_card') {
			if (field_val == "" || !this.is_credit_card(field_val)) {
				field.addClass('error');	
				valid = false;
				if (bFocus)
					field.focus();
			} else {
				field.removeClass('error');	
				valid = true;
			}
		} else if (data.type == 'email') {
			valid = true;
			if (field_val == "") {
				valid = false;
			} else {
				var exp = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,16})?$/;
				var re = new RegExp(exp);
				var match = re.exec(field_val);
				if (match == null)
					valid = false;
			}
			
			if (valid) {
				field.removeClass('error');	
			} else {
				field.addClass('error');	
				if (bFocus)
					field.focus();
			}			
		} else if(data.type == 'checkbox') {
			var fld = field.parent('label');
			if (field_val == "") {
				fld.addClass('error');
				valid = false;
				if (bFocus)
					field.focus();
			} else {
				fld.removeClass('error');
				valid = true;
			}
		} else if (data.type == 'checklist') {
			if (values.length == 0) {
				field.addClass('error');
				valid = false;
				if (bFocus) {
					var pos = field.find('input').parent();
					jQuery('body,html').scrollTop(pos.offset().top - 140);
				}
			} else {
				field.removeClass('error');
				valid = true;
			}
		}

		if (!valid && data.error_text) {
			data.error_text.css('display', 'block');
		}

		field.trigger('fld-error-response');
		return valid;
	},
	
	is_credit_card: function( number ) {
	    var sum = 0;
	    var mul = 1;
	 
	    for (var i = 0; i < number.length; i++) {
	       var digit = number.substring(number.length - i - 1, number.length - i);
	       var tproduct = parseInt(digit ,10) * mul;
	       if (tproduct >= 10)
	            sum += (tproduct % 10) + 1;
	       else
	            sum += tproduct;
	 
	       mul = 3 - mul;
	    }
	 
	    if ((sum % 10) == 0)
	        return true;
	    else
	        return false;
	}
};

var FormValidationErrorResponse = {
	//for error response after input field, just add .error:after css with the message
	errorBoxes: [],
	
	init: function(){
		var me = this;
		jQuery('form.fld-error-response').each(function(index, value){
			var form = jQuery(this);
			me.errorBoxes[index] = jQuery('#'+form.data('error-response-box')).length == 0 ? null : jQuery('#'+form.data('error-response-box'));
			
			form.find('.fld-validate-me').each(function(fieldIndex, fieldValue){
				var field = jQuery(this);
				field.on('fld-error-response', function(){
					if(field.hasClass('error')){
						if(me.errorBoxes[index] != null){
							if(me.errorBoxes[index].find('.'+field.attr('name')).length == 0){
								me.errorBoxes[index].append('<div class="'+field.attr('name')+' error-response">'+field.data('error-response-text')+'</div>');
							}
						}
						
					} else{
						if(me.errorBoxes[index] != null){
							me.errorBoxes[index].find('.'+field.attr('name')).remove();
						}
					}
				});
			});
		});
	},
}

function getPageScroll() {
	return document.body.scrollTop || jQuery(window).scrollTop();
}

function wait_for_visible(jq, callback, delay_callback) {
	if (jq.length == 0)
		return;

	if (typeof delay_callback == 'undefined')
		delay_callback = 10;

	var jq_win = jQuery(window);

	var chk_timer = setInterval(function() {
		if (jq.is(':visible')) {
			var t = getPageScroll();
			var h = jq_win.height();
			var item_pos = jq.offset().top;
			if (item_pos > t && item_pos < t + h) {
				clearInterval(chk_timer);
				setTimeout(function() {
					callback(jq);
				}, delay_callback);
			}
		}
	}, 250);
}

(function() {
    var lastTime = 0;
    var vendors = ['ms', 'moz', 'webkit', 'o'];
    for(var x = 0; x < vendors.length && !window.requestAnimationFrame; ++x) {
        window.requestAnimationFrame = window[vendors[x]+'RequestAnimationFrame'];
        window.cancelAnimationFrame = window[vendors[x]+'CancelAnimationFrame'] 
                                   || window[vendors[x]+'CancelRequestAnimationFrame'];
    }
 
    if (!window.requestAnimationFrame)
        window.requestAnimationFrame = function(callback, element) {
            var currTime = new Date().getTime();
            var timeToCall = Math.max(0, 16 - (currTime - lastTime));
            var id = window.setTimeout(function() { callback(currTime + timeToCall); }, 
              timeToCall);
            lastTime = currTime + timeToCall;
            return id;
        };
 
    if (!window.cancelAnimationFrame)
        window.cancelAnimationFrame = function(id) {
            clearTimeout(id);
        };
}());

(function() {
// Reasonable defaults
var PIXEL_STEP  = 10;
var LINE_HEIGHT = 40;
var PAGE_HEIGHT = 800;

window.normalizeWheel = function(/*object*/ event) /*object*/ {

  var sX = 0, sY = 0,       // spinX, spinY
      pX = 0, pY = 0;       // pixelX, pixelY

  // Legacy
  if ('detail'      in event) { sY = event.detail; }
  if ('wheelDelta'  in event) { sY = -event.wheelDelta / 120; }
  if ('wheelDeltaY' in event) { sY = -event.wheelDeltaY / 120; }
  if ('wheelDeltaX' in event) { sX = -event.wheelDeltaX / 120; }

  // side scrolling on FF with DOMMouseScroll
  if ( 'axis' in event && event.axis === event.HORIZONTAL_AXIS ) {
    sX = sY;
    sY = 0;
  }

  pX = sX * PIXEL_STEP;
  pY = sY * PIXEL_STEP;

  if ('deltaY' in event) { pY = event.deltaY; }
  if ('deltaX' in event) { pX = event.deltaX; }

  if ((pX || pY) && event.deltaMode) {
    if (event.deltaMode == 1) {          // delta in LINE units
      pX *= LINE_HEIGHT;
      pY *= LINE_HEIGHT;
    } else {                             // delta in PAGE units
      pX *= PAGE_HEIGHT;
      pY *= PAGE_HEIGHT;
    }
  }

  // Fall-back if spin cannot be determined
  if (pX && !sX) { sX = (pX < 1) ? -1 : 1; }
  if (pY && !sY) { sY = (pY < 1) ? -1 : 1; }

  return { spinX  : sX,
           spinY  : sY,
           pixelX : pX,
           pixelY : pY };
};
})();