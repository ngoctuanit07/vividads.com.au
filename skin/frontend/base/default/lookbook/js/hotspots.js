jQuery.extend({
        setHotspots : function(slide, hotspots) {
                if (!hotspots) return;
                var i=0;
                hotspots.each(function() {
                   if (!document.getElementById(hotspots[i].id)) {
                       slide.append('<div class="hotspot" id="'+hotspots[i].id+'" style="left:'+hotspots[i].left+'px; top:'+hotspots[i].top+'px; width:'+hotspots[i].width+'px; height:'+hotspots[i].height+'px;">'+hotspots[i].text+'</div>');
                       var infoblock = slide.find('#'+hotspots[i].id +' .product-info');
                       var imgwidth = slide.width();
                       var infowidth = infoblock.width();             
                       var hspt_width_hf = parseInt(hotspots[i].width/2);
                       var leftposition = hotspots[i].left+hspt_width_hf+7;                
                       infoblock.find('.info-icon').css('left',hspt_width_hf+'px');     
                       if (((leftposition + infowidth + 10)> imgwidth) && (leftposition>(imgwidth-leftposition))) 
                       {
                          if ( jQuery.browser.msie && jQuery.browser.version=='8.0') {
                              if (leftposition-5<infowidth) {
                              infoblock.css('width', leftposition-20 +'px');
                              infowidth = infoblock.width();      
                              }
                              infoblock.css('left', hspt_width_hf-7-infowidth-2*parseInt(infoblock.css('padding-left'))+'px');
                          }
                          else
                          {
                              infoblock.css('left', '');
                              infoblock.css('right', hspt_width_hf+7+'px');               
                          } 
                          
                          if (leftposition-5<infowidth) {
                              infoblock.css('width', leftposition-20 +'px');
                              infowidth = infoblock.width();
                          }      
                        }
                        else
                        {
                             infoblock.css('left', hspt_width_hf+7 + 'px');
                             if ((imgwidth-leftposition-5)<infowidth) {
                                  infoblock.css('width', imgwidth-leftposition-20 +'px');
                                  infowidth = infoblock.width();      
                              }  
                        }
                        var imgheight = slide.height();
                        var infoheight = infoblock.height(); 
                        var hspt_height_hf = parseInt(hotspots[i].height/2);
                        var topposition = hotspots[i].top+hspt_height_hf; 
                        if (((topposition + infoheight + 30)> imgheight) && (topposition>(imgheight-topposition))) 
                        {
                          if ( jQuery.browser.msie && jQuery.browser.version=='8.0') {
                              if (topposition-5<infoheight) {
                               infoblock.css('height', topposition-10 +'px');
                               infoheight = infoblock.height();      
                              }
                              infoblock.css('top', hspt_height_hf-infoheight-2*parseInt(infoblock.css('padding-top'))+'px');
                          }
                          else
                          {
                              infoblock.css('top', '');
                              infoblock.css('bottom', hspt_height_hf+'px');                 
                          }
                          if (topposition-5<infoheight) {
                              infoblock.css('height', topposition-10 +'px');
                              infoheight = infoblock.height();      
                          }
                        }
                        else
                        {
                             infoblock.css('top', hspt_height_hf + 'px');
                             if ((imgheight-topposition-5)<infoheight) {
                                  infoblock.css('height', imgheight-topposition-10 +'px');
                                  infoheight = infoblock.height();      
                             }
                        }          
                        i++;
                  }             
              });
        }
});
  
             
