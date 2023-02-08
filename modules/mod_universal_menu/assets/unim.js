// Universal Menu v1.10.1, module mod_universal_menu
// Copyright (C) 2014 Thelet Pirsum. All rights reserved.

;function universalMenu(params,jQload) {
	/* Function's body, don't change */
	var loadAttempts = 0, initAttempts = 0;
	if (typeof jQuery === 'undefined' && typeof jQload !== 'undefined') {
		document.write('<'+'script src="./modules/mod_universal_menu/assets/jquery.min.js" type="text/javascript"><'+'/script>');
	}
	var unimLoad = function() {
		loadAttempts++;
		
		// Is jQuery loaded?
		if (typeof jQuery === 'undefined') {
			if (loadAttempts < 500) setTimeout(unimLoad, 5);
			return false;
		}	
		if (typeof jQuery.noConflict !== 'undefined') {
			var $ = jQuery.noConflict();
		}
		(function($, window, document) {
			var unimInit = function() {
				initAttempts++;

				// Is jQuery ready?
				if (!$.isReady) {
					if (initAttempts < 500) setTimeout(unimInit, 5);
					return false;
				}
				console.log('Loading "'+params.unipath+'" (attempts: '+loadAttempts+'+'+initAttempts+', jQuery: '+$.fn.jquery+')');
				
				// Text direction
				var dir = "ltr";
				$("script").each(function(key,value) {
					dir = value.src.replace(/^[^\#]+\#?/,'');
					if (dir=="ltr" || dir=="rtl") return false;
				});
				$("html").attr("dir", dir);

				// CSS-path to icon (show / hide menu)
				var iconPath = params.unipath ? '.unim-icon.' + params.unipath : '.unim-icon';

				// CSS-path to menu items
				var menuPath = params.unipath ? 'ul.' + params.unipath : 'ul.unim';

				// Enables mobile menu icon
				var mobileMenuIcon = params.menuicon;
				
				// Expand the menu on load or collapse it
				var expandSubMenus = params.expand;
				
				// Auto-stretch horizontal menu items
				var autoStretch  = params.stretch;

				// Animation effect type and duration
				var animEffect = params.effect;
				var animDuration = params.duration;
				// No animation effects, globally disable all animations
				if (animEffect != 'slide' && animEffect != 'fade') { 
					$.fx.off = true; animDuration = 0;
				}

				// Disable parent menu link?
				var disablePLink = params.dplink;

				// Toggle on mouse hover?
				var toggleOnHover = params.tohover;

				// Mobile menu layout and viewport size
				var mobileMenu = params.mobimenu;
				var mobileSize = params.mobisize;
				var mobileState = (!mobileMenuIcon || expandSubMenus);

				// Init main accordion vars
				var all_submenus = $(menuPath + ' li ul');
				var menu_path = $(menuPath); var icon_path = $(iconPath);
				var auto_close, parent_menu, grandparent_menus, children_menu, parent_menu_opened, children_menu_opened;

				// Expand/collapse sub-menus on load
				if (expandSubMenus) {
					all_submenus.show();
				}
				else {
					all_submenus.hide();
					// Hide sub-menus when the user clicks outside the menus' area
					$(document).click(function(e) { 
						if ($(e.target).parents().index(menu_path) == -1) {
							if (all_submenus.is(":visible")) {
								all_submenus.closeMenu(animEffect, animDuration).delay(animDuration).parent().removeClass('unimsub opened');
							}
						}        
					});
				}

				// Enables responsive menu
				if (mobileMenu) {
					// Init mobile menu state
					if (mobileSize >= 2147483647) {
						menu_path.hide();
						mobileState = false;
					}
					else {
						// Init mobile menu icon and toggle a menu on load
						toggleMenu();
					}
					updateMenuIconState();
					
					// Update menu on window resize
					$(window)
						.resize(function() {
							toggleMenu();
						});
				}

				// Auto-space menu items to fill container
				if (autoStretch) {
					var initialMenuWidth = 0;
					autoStretchMenu();
					$(window)
						.resize(function() {
							// Update menu on window resize
							autoStretchMenu(true);
							// Update menu when animation/transition finishes
							setTimeout(function() {autoStretchMenu()}, 1000);
						})
						.load(function() {
							// Update menu on window load
							autoStretchMenu();
							// Update menu when animation/transition finishes
							setTimeout(function() {autoStretchMenu()}, 1000);
						});
				}

				// Toggle on click on arrow
				$(menuPath + ' li a > i')
					.click(function(e) {
						toggleMenuOnClick(e, $(this).parent().parent(), true);
					});

				// Toggle on click on item
				$(menuPath + ' li a')
					.click(function(e) {
						toggleMenuOnClick(e, $(this).parent(), disablePLink);
					});

				function toggleMenuOnClick(e, parent_menu, parent_link) {
					e.stopPropagation();
					children_menu = parent_menu.children('ul');
					// Open if item contain sub-menu(s)
					if (children_menu.length > 0) {
						if (children_menu.css('display') == 'none') {
							e.preventDefault();
							grandparent_menus = parent_menu.parents('ul');
							// Close all sub-menu(s) and open clicked sub-menu
							all_submenus.not(grandparent_menus).closeMenu(animEffect, animDuration).
								parent().removeClass('unimsub opened');
							children_menu.openMenu(animEffect, animDuration);
							parent_menu.addClass('unimsub opened');
						}
						else {
							// Bootstrap's jumping sub-menus bug fix
							if (parent_link || children_menu.height() <= 1) {
								e.preventDefault();
							}
							if (parent_link && parent_menu.hasClass('unimsub')) {
								children_menu.closeMenu(animEffect, animDuration);
								parent_menu.delay(animDuration).removeClass('unimsub opened');
							}
						}
					}			
				}

				// Toggle on hover
				if (toggleOnHover) {
				$(menuPath + ' > li.parent')
					// Opens level1 sub-menu on hover
					.hover(function() {
						// Check if desktop viewport width
						if (isDesktopMenuType()) {
							parent_menu = $(this);
							children_menu = parent_menu.children('ul');
							// Try to open sub-menu
							if (children_menu.length > 0 && !parent_menu.hasClass('unimsub')) {
								// Finish previous animation before starting a new
								children_menu.finish();
								children_menu.openMenu(animEffect, animDuration);
								parent_menu.addClass('unimsub opened');
							}
						}
					}, 
					// Auto closing opened level1 sub-menu
					function() {
						if (isDesktopMenuType() && parent_menu != null && parent_menu.hasClass('unimsub')) {
							if (children_menu != null) {
								// Close children menus
								children_menu.closeMenu(animEffect, animDuration);
							}
							parent_menu.delay(animDuration).removeClass('unimsub opened');
						}
					});
				$(menuPath + ' > li.parent ul li.parent')
					// Opens level2+ sub-menus on hover
					.hover(function() {
						// Check if viewport width is Desktop-sized
						if (isDesktopMenuType()) {
							parent_menu_opened = $(this);
							children_menu_opened = parent_menu_opened.find('ul');
							// Try to open sub-menus
							if (children_menu_opened.length > 0) {
								// Finish previous animation before starting a new
								children_menu_opened.finish();
								children_menu_opened.openMenu(animEffect, animDuration);
								parent_menu_opened.addClass('unimsub opened');
							}
						}
					}, 
					// Auto closing opened level2+ sub-menus
					function() {
						if (isDesktopMenuType() && parent_menu_opened != null && parent_menu_opened.hasClass('unimsub')) {
							if (children_menu_opened != null) {
								children_menu_opened.closeMenu(animEffect, animDuration);
							}
							parent_menu_opened.delay(animDuration).removeClass('unimsub opened');
						}
					});
				}
				
				// Show/hide mobile menu icon
				icon_path
					.click(function(e) {
						e.preventDefault();
						// Update mobile menu state
						if (!isDesktopMenuType()) {
							mobileState = (menu_path.css('display') == 'none');
						}
						// Update menu icon class
						updateMenuIconState();
						// Toggle mobile menu
						menu_path.toggle(animDuration);
					});

				// Updates menu icon state class
				function updateMenuIconState() {
					if (!mobileState) {
						icon_path.addClass('collapsed');
						icon_path.removeClass('expanded');
					}
					else {
						icon_path.addClass('expanded');
						icon_path.removeClass('collapsed');
					}
				}
				
				// Checks menu type by viewport width
				function isDesktopMenuType() {
					return (!mobileMenu || window.innerWidth > mobileSize || 
						document.documentElement.clientWidth > mobileSize);
				}
					
				// Responsive menu functionality
				function toggleMenu() {
					trigger = (menu_path.css('display') != 'none');
					// Detect menu type by viewport width
					if (isDesktopMenuType()) {
						// Desktop: hide menu icon
						icon_path.css("display", "none");
						icon_path.removeClass('mobile');
						// Display menu's items if trigger is false
						if (!trigger) {
							menu_path.show(animDuration);
						}
						menu_path.removeClass('mobile');
					}
					else {
						// Mobile: show menu icon
						icon_path.css("display", "block");
						icon_path.addClass('mobile');
						// Hide menu's items if mobileState is false
						if (!menu_path.hasClass('mobile')) {
							if (!mobileState) {
								menu_path.hide(animDuration);
							}
							menu_path.addClass('mobile');
						}
						// Update menu icon class
						updateMenuIconState();
					}
				}

				// Recalculates item paddings
				function itemPaddingsRecalc(menuItemChildren, menuCount, totalShift) {
					var itemShiftRandom, itemShiftDelta, itemPaddingRight=[], itemPaddingLeft=[];
					// Calculate average item shift
					var itemFracPart = totalShift % menuCount;
					var itemShift = (totalShift - itemFracPart) / menuCount;
					var itemShiftHalf = itemShift >> 1;

					// Calculate the new item paddings
					menuItemChildren.each(function( index ) {
						// Add padding shift to original paddings
						itemPaddingRight[index] = parseFloat($(this).css("padding-right")) + itemShiftHalf;
						itemPaddingLeft[index] = parseFloat($(this).css("padding-left")) + itemShiftHalf;
						totalShift -= itemShift;

						// Padding shift compensations
						itemShiftDelta = itemShift - (itemShiftHalf << 1);
						if (itemShiftDelta == 0 && itemFracPart != 0) {
							itemShiftRandom = Math.floor(Math.random() * menuCount);
							if (itemShiftRandom <= Math.abs(itemFracPart)) {
								itemShiftDelta = (itemFracPart > 0 ? 1 : -1);
								totalShift -= itemShiftDelta;
							}
						}
						if (itemShiftDelta != 0) {
							if (Math.random() < .5) itemPaddingRight[index] += itemShiftDelta;
							else itemPaddingLeft[index] += itemShiftDelta;
						}

						// Check paddings
						if (itemPaddingRight[index] < 0) {
							totalShift += itemPaddingRight[index];
							itemPaddingRight[index] = 0;
						}
						if (itemPaddingLeft[index] < 0) {
							totalShift += itemPaddingLeft[index];
							itemPaddingLeft[index] = 0;
						}
					});					
					
					// After-corrections and FireFox fix (-1px)
					/*console.log('Shift on exit:',totalShift);*/
					var i = 0; if (totalShift > 1) totalShift = 1; totalShift--;
					
					// Explanation: a=length, b=is just a place holder
					var createArray = function(a,b) {b=[];while(a--) {b[a]=0}return b};					
					var shuffled = createArray(menuCount);
					while (i < menuCount && totalShift < 1) {
						do {
							itemShiftRandom = Math.floor(Math.random() * menuCount);
						} while (shuffled[itemShiftRandom] > 0);
						//var item = menuItemChildren[itemShiftRandom];
						if (Math.random() < .5) {
							itemPaddingRight[itemShiftRandom]--;
							if (itemPaddingRight[itemShiftRandom] > 0) {
								shuffled[itemShiftRandom]++;
								totalShift++;
							}
						}
						else{
							itemPaddingLeft[itemShiftRandom]--;
							if (itemPaddingLeft[itemShiftRandom] > 0) {
								shuffled[itemShiftRandom]++;
								totalShift++;
							}
						}
						i++;
					}
					/*console.log(shuffled);*/

					// Set the new item paddings
					menuItemChildren.each(function( index ) {
						$(this).finish().css({
							'padding-right': Math.floor(itemPaddingRight[index])+'px', 
							'padding-left': Math.floor(itemPaddingLeft[index])+'px'
						});
					});
					return totalShift;
				}					

				// Auto-stretches menu items across a menu's container
				function autoStretchMenu(auto) {
					// Disable stretching in mobile mode
					if (!isDesktopMenuType()) return false;
					var containerWidth = menu_path.width();
					if (containerWidth <= 1) return false;
					
					// Get menu items list and count them
					var menuItems = menu_path.children("li");
					var menuCount = menuItems.length;
					// Calculate actual menu width
					var actualMenuWidth = 0;
					menuItems.each(function() {
						actualMenuWidth += $(this).outerWidth(true);
					});
					if (!initialMenuWidth) initialMenuWidth = actualMenuWidth;

					// Compare actual menu width and menu's container width
					if (containerWidth != actualMenuWidth) {
						if (autoStretch == 1 && containerWidth < initialMenuWidth) return false; // Stretch only
						if (autoStretch == 2 && containerWidth > initialMenuWidth) return false; // Squeeze only
						
						// Added support for MegaMenus (DIV elements)
						var menuItemChildren = $(":first", menuItems);
						// Define item paddings shifts
						var totalShift = containerWidth - actualMenuWidth;
						
						//if (typeof auto==='undefined') auto = false;
						// Recalculate new item paddings
						return itemPaddingsRecalc(menuItemChildren, menuCount, totalShift);
					}
				}
			};
			/* Extend jQuiery <1.9 */
			if (!$.fn.finish) {
				jQuery.fn.extend({
					finish: function() {
						return $(this).stop(true,true);
					}
				});
			};
			/* "Open Menu" plug-in */
			if (!$.fn.openMenu) {
				jQuery.fn.extend({
					openMenu: function(animEffect, animDuration) {
						switch (animEffect) {
							case 'slide':
								return $(this).slideDown(animDuration);
								break;
							case 'fade':
								return $(this).fadeOut(animDuration);
								break;
						}
						return $(this);
					}
				});
			}			
			/* "Close Menu" plug-in */
			if (!$.fn.closeMenu) {
				jQuery.fn.extend({
					closeMenu: function(animEffect, animDuration) {
						switch (animEffect) {
							case 'slide':
								return $(this).slideUp(animDuration);
								break;
							case 'fade':
								return $(this).fadeIn(animDuration);
								break;
						}
						return $(this);
					}
				});
			}
			/* Initialise menu */
			setTimeout(unimInit, 5);
		}(window.jQuery, window, document));
	}
	setTimeout(unimLoad, 5);
}
