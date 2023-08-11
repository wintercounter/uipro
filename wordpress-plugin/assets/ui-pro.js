/********************************************
 **** UI-Pro jQuery Plugin
 **** Created by: wintercounter
 **** Version: 1.0
 **** Available at CodeCanyon
 ********************************************/

;(function($) {

	var 		documentWidth,
			documentHeight,
			i;

	// Init things
	$.uiPro = function(settings) {
		params = null;
		params = $.extend({
			
			// Params
			init : 'both',
                        leftMenu : false,
                        rightMenu : false,
			threshold : 40

			
		}, settings);

		
		/***** Some Essential Things *****/
		
		documentWidth = $(document).width();
		documentHeight = $(document).height();
		
		/***** Init Menus *****/
			
		$.uiPro.init();

	}
	
	/************* Inits ************/
	$.uiPro.init = function() {
            
		var leftMenu = false;
		var rightMenu = false;
		var left = right = false;
		
		if(params.init == 'left' || params.init == 'both'){
		    
		    if($.uiPro.createMenu('left')){
			    
			    left = $('#uipro_left');
			    
		    }
    
		}
		
		if(params.init == 'right' || params.init == 'both'){
		    
		    if($.uiPro.createMenu('right')){
			    
			    right = $('#uipro_right');
		    
		    }
    
		}
		
		if(left || right){
			
			$(document).mousemove(function(e){
				
				if(left){
				
					if(e.pageX < params.threshold && left.not(':visible')){
						
						left.addClass('active');
						
					}
					else if(left.is(':visible') && e.pageX > left.width()){
						
						left.removeClass('active');
						
					}
				
				}
				
				if(right){
					
					if(e.pageX > (documentWidth - params.threshold) && right.not(':visible')){
						
						right.addClass('active');
						
					}
					else if(right.is(':visible') && e.pageX < (documentWidth - right.width())){
						
						right.removeClass('active');
						
					}
					
				}
				
			});
		
		}
            
            
        }
        
        /************ PRIVATES *************/
        $.uiPro.createMenu = function(pos){
            
            var menu = $.uiPro.parseMenuItems(pos);
            
            if(menu){
                
                $('body').append('<div id="uipro_' + pos + '" class="uipro">' + menu + '</div>');
                
                return true;
	
            }
            else{
		
                return false;
	
            }
            
        }
        
        $.uiPro.parseMenuItems = function(pos){
		
		setTimeout(function(){
			var c = $('#uipro_' + pos + ' ul li').length;
			$('#uipro_' + pos + ' ul').css({'height': c * 100 + 'px', 'marginTop': (((c * 100) / 2) * -1) + 'px'});
		},100);
		
            if(typeof(params[pos + 'Menu']) == 'object'){
                
                var out = "<ul>";
                var items = params[pos + 'Menu'];
		
                
                for(var item in items){
			
			item = items[item];
			item.target = (typeof item.target == 'undefined') ? '_self' : item.target;
			
			out += '<li><a class="' + item.klass + '" href="' + item.link + '" target="' + item.target + '"><span>' + item.label + '</span></a></li>\n';
			
                }
                
                out += '\n</ul>';
                
                return out;
                
            }
	    else if(typeof(params[pos + 'Menu']) == 'string'){
		
		return $("<div />").append($(params[pos + 'Menu']).clone()).html();
		
	    }
            else{
		
                return false;
	
            }
            
        }
        
} )( jQuery );