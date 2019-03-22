var $j = jQuery;
    var filter_cat= '';
    var filter_type= '';
    var filter_brand= '';
    var tempScrollTop = null;
$j(document).ready(function() {
    loadProducts();
    var menu = $j('#product-category-menu');
    menu.find('li > a').on('click', function(){
        var ul = $j(this).closest('ul');
        var type = ul.data('type');
        tempScrollTop = $(window).scrollTop();
        $j(this).toggleClass('underline');
        $j(this).parent('li').siblings().children('a').removeClass('underline');
        if(type == 'cat'){
          if(filter_cat == $j(this).text()){
            filter_cat = '';
          }else{
            filter_cat = $j(this).text();
          }
        }
        if(type == 'type'){
          if(filter_type == $j(this).text()){
            filter_type = '';
          }else{
            filter_type = $j(this).text();
          }
        }
        if(type == 'brand'){
          if(filter_brand == $j(this).text()){
            filter_brand = '';
          }else{
            filter_brand = $j(this).text();
          }
        }

        if(filter_type != '' || filter_cat !=''){
          $j('#menu-bar2').css('display', 'inline');
        }
        var filters = {
          filter_cat : filter_cat,
          filter_type : filter_type,
          filter_brand : filter_brand
         }
        if($j(this).hasClass('underline')){
          
          loadProducts(filters, 1,6 ,true);
        }else{
          loadProducts(filters);
        }
         $j('a.page.button').find('a').on('click', function(e){
                  e.preventDefault();
                  alert('ok');
                });
         $(window).scrollTop(tempScrollTop);
         return false;
      });


});

var loadProducts = function (filters=false, page=1, number= 6, brands=false, action='my_ajax_request') {
    $.ajax({                        
        type: "POST",                 
        url: "../wp-admin/admin-ajax.php",                     
        data: {filters:filters , page: page, number: number, action:action, brands:brands}, 
        dataType: "json",
        beforeSend: function(){
           $j('#search-text').html('BUSCANDO PRODUCTOS ...'); 
           $j('#search_result_container').find('.row').html("");  
        },
        success: function(data)             
        {
              $j('#search_result_container').find('.row').html(data.html).show("slow");  
              $j('#pagination-container').html(data.pagination);  
              $j('#search-text').html(data.filters); 
              $j('#brand-filter').html(data.brandstring);
              $j('#brand-filter li a').on('click', function(e){
              var ul = $j(this).closest('ul');
              var type = ul.data('type');
              $j(this).toggleClass('underline');
              var filter_brand= '';
                if(type == 'cat'){
                  if(filter_cat == $j(this).text()){
                    filter_cat = '';
                  }else{
                    filter_cat = $j(this).text();
                  }
                }
                if(type == 'type'){
                  if(filter_type == $j(this).text()){
                    filter_type = '';
                  }else{
                    filter_type = $j(this).text();
                  }
                }
                if(type == 'brand'){
                  if(filter_brand == $j(this).text()){
                    filter_brand = '';
                  }else{
                    filter_brand = $j(this).text();
                  }
                }

                if(filter_type != '' || filter_cat !=''){
                  $j('#menu-bar2').css('display', 'inline');
                }
                   console.log('filtro categoria', filter_cat);
                   console.log('filtro tipo', filter_type);
                   console.log('filtro marca', filter_brand);
                     var filters = {
                      filter_cat : filter_cat,
                      filter_type : filter_type,
                      filter_brand : filter_brand
                     }
                
                if($j(this).hasClass('underline')){
                  
                  loadProducts(filters, 1,6 ,true);
                }else{
                  loadProducts(filters);
                }
                $j('a.page.button').find('a').on('click', function(e){
                  e.preventDefault();
                  alert('ok');
                });
       
        return false;
              });

     
        },
        complete(){
           $j('#search-text').html('RESULTADO DE LA BÚSQUEDA'); 
        },
        always(){
          $(window).scrollTop(tempScrollTop);
        }
    });
    return false;
};

