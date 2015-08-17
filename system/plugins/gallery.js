// Yellow imagegallery

var initPhotoSwipeFromDOM = function() {
	
	// Parse gallery items from DOM
	var parseElements = function(el) {
		var thumbElements = el.childNodes,
		numNodes = thumbElements.length,
		items = [],
		el,
		childElements,
		size,
		item;
		
		for(var i = 0; i < numNodes; i++) {
			el = thumbElements[i];
			if(el.nodeType !== 1) {
				continue;
			}
			childElements = el.children;
			size = el.getAttribute('data-size').split('x');
			item = {
				src: el.getAttribute('href'),
				w: parseInt(size[0], 10),
				h: parseInt(size[1], 10),
			};
			if(childElements.length > 0) {
				item.msrc = childElements[0].getAttribute('src'); // thumbnail
				if(childElements.length > 1) {
					item.title = childElements[1].innerHTML; // caption
				}
			}
			item.el = el;
			items.push(item);
		}
		return items;
	};

	// Parse gallery and picture index from URL
	var parseHash = function() {
		var hash = window.location.hash.substring(1),
		params = {};
		
		if(hash.length < 5) {
			return params;
		}
		var vars = hash.split('&');
		for (var i = 0; i < vars.length; i++) {
			if(!vars[i]) {
				continue;
			}
			var pair = vars[i].split('=');
			if(pair.length < 2) {
				continue;
			}
			params[pair[0]] = pair[1];
		}
		if(params.gid) {
			params.gid = parseInt(params.gid, 10);
		}
		return params;
	};
	
	// Create gallery template if necessary
	var createTemplate = function(selector)
	{
		var template = document.querySelectorAll(selector)[0];
		if(!template)
		{
			var elementDiv = document.createElement('div');
			elementDiv.className = selector.substr(1);
			elementDiv.setAttribute('tabindex', '-1');
			elementDiv.innerHTML =
				'<div class="pswp__bg"></div>'+
				'<div class="pswp__scroll-wrap">'+
				'<div class="pswp__container">'+
				'<div class="pswp__item"></div>'+
				'<div class="pswp__item"></div>'+
				'<div class="pswp__item"></div>'+
				'</div>'+
				'<div class="pswp__ui pswp__ui--hidden">'+
				'<div class="pswp__top-bar">'+
				'<div class="pswp__counter"></div>'+
				'<button class="pswp__button pswp__button--close" title="Close (Esc)"></button>'+
				'<button class="pswp__button pswp__button--share" title="Share"></button>'+
				'<button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>'+
				'<button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>'+
				'<div class="pswp__preloader">'+
				'<div class="pswp__preloader__icn">'+
				'<div class="pswp__preloader__cut">'+
				'<div class="pswp__preloader__donut"></div>'+
				'</div>'+
				'</div>'+
				'</div>'+
				'</div>'+
				'<div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">'+
				'<div class="pswp__share-tooltip"></div>'+
				'</div>'+
				'<button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>'+
				'<button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>'+
				'<div class="pswp__caption">'+
				'<div class="pswp__caption__center"></div>'+
				'</div>'+
				'</div>'+
				'</div>';
			template = document.body.appendChild(elementDiv);
		}
		return template;
	};
	
	// Handle when user clicks on gallery
	var onClickGallery = function(e) {
		e = e || window.event;
		e.preventDefault ? e.preventDefault() : e.returnValue = false;
		var clickedElement = e.target || e.srcElement;
		while(clickedElement) {
			if(clickedElement.tagName === 'A') break;
			clickedElement = clickedElement.parentNode;
		}
		if(!clickedElement) {
			return;
		}
		var clickedGallery = clickedElement.parentNode;
		var childNodes = clickedElement.parentNode.childNodes,
		numChildNodes = childNodes.length,
		nodeIndex = 0,
		index;
		
		for (var i = 0; i < numChildNodes; i++) {
			if(childNodes[i].nodeType !== 1) {
				continue;
			}
			if(childNodes[i] === clickedElement) {
				index = nodeIndex;
				break;
			}
			nodeIndex++;
		}
		if(index >= 0) {
			openPhotoSwipe( index, clickedGallery );
		}
		return false;
	};
	
	// Open gallery
	var openPhotoSwipe = function(index, galleryElements, disableAnimation, fromURL) {
		var template = createTemplate('.pswp'),
		items = parseElements(galleryElements),
		gallery,
		options;
		
		options = {
			mainClass: 'pswp--minimal--dark',	//custom options
			fullscreenEl: false,
			shareEl: false,
			tapToClose: true,
			tapToToggleControls: false,
			
			galleryUID: galleryElements.getAttribute('data-pswp-uid'),
			
			getThumbBoundsFn: function(index) {
				var thumbnail = items[index].el.children[0],
				pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
				rect = thumbnail.getBoundingClientRect();
				return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
			}
		};
		if(fromURL) {
			if(options.galleryPIDs) {
				for(var j = 0; j < items.length; j++) {
					if(items[j].pid == index) {
						options.index = j;
						break;
					}
				}
			} else {
				options.index = parseInt(index, 10) - 1;
			}
		} else {
			options.index = parseInt(index, 10);
		}
		if(isNaN(options.index)) {
			return;
		}
		if(disableAnimation) {
			options.showAnimationDuration = 0;
		}
		gallery = new PhotoSwipe( template, PhotoSwipeUI_Default, items, options);
		gallery.init();
	};
	
	// check gallery elements and bind events
	var galleryElements = document.querySelectorAll( '.photoswipe' );
	for(var i = 0, l = galleryElements.length; i < l; i++) {
		galleryElements[i].setAttribute('data-pswp-uid', i+1);
		galleryElements[i].onclick = onClickGallery;
	}
	
	// check if URL contains gid and pid
	if(galleryElements.length)
	{
		var hashData = parseHash();
		if(hashData.pid && hashData.gid) {
			openPhotoSwipe( hashData.pid,  galleryElements[ hashData.gid - 1 ], true, true );
		}
	}
};

if(window.addEventListener){
	window.addEventListener('load', initPhotoSwipeFromDOM, false);
} else {
	window.attachEvent('onload', initPhotoSwipeFromDOM);
}