$(function() {
    
    var tabs = {};
    
    var controller = '';
    var subcategories = {};
    

    $(document).ready(function(){
        var category = $('#category').text();
        category = category.split('-');
        controller = category[0];
        
        $('#post-ad-form #subcategory-select option[data-category-id]').each(function(){
            if($(this).attr('data-category-id') in subcategories) {
                subcategories[$(this).attr('data-category-id')][$(this).val()] = $(this).text();
            } else {
                subcategories[$(this).attr('data-category-id')] = new Array();
                subcategories[$(this).attr('data-category-id')][$(this).val()] = $(this).text();
            }
            $(this).remove();
        });
    });
    
    $('#sitemap-link').click(function(){
        $('#sitemap-window-blur').css('height', $(document).height() + 'px');
        $('#sitemap-window-blur').css('width', $(document).width() + 'px');
        $('#sitemap-window-blur').show();
        $('#sitemap-window').show();
    });
    
    $('#sitemap-close').click(function(){
        $('#sitemap-window').hide();
        $('#sitemap-window-blur').hide();
    })
    
    $('#category-select').change(function(){
        var category = $(this).val();
        $('#post-ad-form #subcategory-select option[data-category-id]').each(function(){
            $(this).remove();
        });
        
        
        for( var i in subcategories[category]) {
            $('#post-ad-form #subcategory-select').append('<option value="' + i + '" data-category-id="' + category + '">' + subcategories[category][i] + '</option>');
        }
    });
    
    $('#submenu a').each(function(){
        var id = $(this).attr('id');
        var title = $(this).text();
        tabs[id] = title;
    });

    $('#post-ad-submit').click(function(e){
        e.preventDefault();
        
        $('#post-ad-error').hide();
        var form_data = $('#post-ad-form').serializeArray();
        
        var form = {};
        for( var i in form_data ) {
            if(form_data[i].value == '') {
                $('#post-ad-error').text('All fields must be filled').show('pulsate', {times: 2});
                form.Add(form_data[i].name, form_data[i].value);
                e.preventDefault();
                return;
            }
            if(form_data[i].name == 'ads_price' && isNaN(form_data[i].value)) {
                $('#post-ad-error').text('Price must be a number').show('pulsate', {times: 2});
                e.preventDefault();
                return;
            }
        }
        
        $('#post-ad-form').submit();
    });

    $('.item .image-container').click(function(){
        var pos = $(this).parent('.item').offset();
        var images = [];
        $('img', this).each(function(){
            images.push($(this).attr('src'));
        });
        $('#photo-viewer #large-position').html('<img alt="" src="' + images[0] + '" />');
        var i = 0;
        $('#photo-viewer .small-position').each(function(){
            $(this).html('<img src="' + images[i] + '" alt=""/>');
            i++;
        });
        
        $('#photo-viewer').css({'top' : pos.top - 350 + 'px', 'left': pos.left + 156 + 'px'}).show();
        $('#photo-viewer-close').show();
    });
    
    $('#photo-viewer .small-container').mouseover(function(){
        if($('img', this).attr('src') != '') {
            $('#large-position').html('<img alt="" src="' + $('img', this).attr('src') + '" />');
        }
    });
    
    $('#photo-viewer-close').click(function(){
        $('#photo-viewer').hide();
        $('#photo-viewer-close').hide();
    });

    $('#post-ad-form').submit(function(e){
        $('#post-ad-submit').html('<img alt="" src="/images/ajax-load.gif" alt="" />');
    });
    
    
    $('iframe[name="post_target"]').bind('load', function(){
        $('#post-ad-submit').html('Post this ad');
    });

    $('.item .read-more-link').click(function(e){
        e.preventDefault();
        var description = $(this).parent('.item').find('.description').text();
        var pos = $(this).parent('.item').offset();
        $('#read-more').text(description).css({'left': (pos.left + 170) + 'px', 'top': (pos.top - 70) + 'px'}).show();
        $('#read-more-close').css({'left': (pos.left + 855) + 'px', 'top': (pos.top - 20) + 'px'}).show();
    });
    
    $('#read-more-close').click(function(e){
        e.preventDefault();
        $('#read-more').hide();
        $('#read-more-close').hide();
    });

    $('#post-ad-form input, #post-ad-form textarea, #post-ad-form select').bind('keyup change mouseup blur click', function(){
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        var item = '#post-ad-window-blur .item-list .item';
        var form_data = $('#post-ad-form').serializeArray();
        
        var images = [
            $('#post-ad-form input[type=file][name="ads_image1"]').get(0),
            $('#post-ad-form input[type=file][name="ads_image2"]').get(0),
            $('#post-ad-form input[type=file][name="ads_image3"]').get(0),
            $('#post-ad-form input[type=file][name="ads_image4"]').get(0),
            $('#post-ad-form input[type=file][name="ads_image5"]').get(0)
        ];
        
        var image = '';
        for( var i in images ) {
            if(images[i].files[0] != undefined) {
                if(images[i].files[0].getAsDataURL() != '') {
                    try{
                        image = images[i].files[0].getAsDataURL();
                    }catch(e){
                        image = '';
                    }
                }
            }
        }
        
        var form = {};
        for( var i in form_data ) {
            form[form_data[i].name] = form_data[i].value;
        }
        
        var date = new Date();
        var day = date.getDate();
        var month = months[date.getMonth()];
        var year = date.getFullYear();
        date = month + ' ' + day + ', ' + year;
        
        var price_arr = form['ads_price'].split('');
        var price = '';
        var counter = 0;
        if(parseInt(form['ads_price'])) {
            for(var j = price_arr.length - 1; j >= 0; j--) {
                if(counter % 3 == 0 && counter > 0) {
                    price = ',' + price;
                }
                price = price_arr[j] + price;
                counter++;
            }
        }
        if(price != '') {
            price = '$' + price;
        }
        
        
        if(image != '') {
            $(item + ' .image-position').html('<img alt="No Image" src="' + image + '" />').css('background', '#000');
        } else {
            $(item + ' .image-position').css({'background-image': "url('/images/default.png')", "background-position": "6px 1px"}).html('&nbsp;&nbsp;No Image');
        }
        if(form['ads_type'] == '') {
            if(form['ads_title'] != '') {
                $(item + ' .title').text(form['ads_title']);
            } else {
                $(item + ' .title').text('TITLE');
            }
        } else {
            $(item + ' .title').text($('#post-ad-form [name="ads_type"] option[value="' + form['ads_type'] + '"]').text() + ' - ' + form['ads_title']);
        }
        $(item + ' .description').html('<strong>DESCRIPTION:</strong> ' + form['ads_description']);
        $(item + ' .info tr:last').html('<td>' + date + '</td><td>' + $('#post-ad-form [name="ads_donations_type_id"] [value="' + form['ads_donations_type_id'] + '"]').text() + '</td><td>' + price + '</td>');
        
        
    });

    /*
    $('#post-ad-submit').click(function(ev){
        ev.preventDefault();
        var images = [
            $('#post-ad-form input[type=file][name="ads_image1"]').get(0),
            $('#post-ad-form input[type=file][name="ads_image2"]').get(0),
            $('#post-ad-form input[type=file][name="ads_image3"]').get(0),
            $('#post-ad-form input[type=file][name="ads_image4"]').get(0),
            $('#post-ad-form input[type=file][name="ads_image5"]').get(0),
            $('#post-ad-form input[type=file][name="ads_image6"]').get(0)
        ];

        alert(images[0].files[0]);
        
        var post_data = $('#post-ad-form').serialize();
        
        $.ajax({
            type: 'POST',
            url: '/index/post-ad',
            data: post_data
        
        });
    });
    */

    $('#post-ad-link').click(function(){
        if($('#post-ad-form input[name=ads_user]').val() != '') {

            $('#post-ad-window-blur').css('height', $(document).height() + 'px');
            $('#post-ad-window-blur').css('width', $(document).width() + 'px');
            $('#post-ad-window-blur').show();
            $('#post-ad-form').show();
            $('#post-ad-window').fadeIn();
            $('#post-ad-window-blur .item-list').fadeIn();
            $('#post-ad-form input:first').focus();
        } else {
            $('#post-ad-window-blur').css('height', $(document).height() + 'px');
            $('#post-ad-window-blur').css('width', $(document).width() + 'px');
            $('#post-ad-window-blur').show();
            $('#post-ad-window').fadeIn();
            $('#post-ad-window-blur .item-list').hide();
            $('#post-ad-message').show();
        }
    });
    
    $('#post-ad-close').click(function(){
        $('#post-ad-window').hide();
        $('#post-ad-window-blur').hide();
        return false;
    });

    $('#logout-link').click(function(ev){
        ev.preventDefault();
        
        $.ajax({url: '/index/logout', 
            success: function(){
                $('#logout-window-blur').css('height', $(document).height() + 'px');
                $('#logout-window-blur').css('width', $(document).width() + 'px');
                $('#logout-window-blur').show();
                $('#logout-window').fadeIn(function(){
                    $('#logout-message').show('pulsate', {times: 2}, function(){
                        window.location.reload(true);
                    })
                });
            }
        });
    });

    $('#login-submit').click(function(ev){
        ev.preventDefault();
        
        $.post('/index/login', $('#login-form').serialize(), function(data) {
            var color = data.split('|')[0];
            var message = data.split('|')[1];
            if(color == "#f00") {
                $('#login-error').text(message)
                    .css('color', color)
                    .show('pulsate', {times: 2});
            } else {
                $('#login-error').text(message)
                    .css('color', color)
                    .show('pulsate', {times: 2}, function(){
                        location.reload(true);
                    });
            }
        });
        
    });

    $('#register-submit').click(function(){
        var valid = true;
        $('#register-form input').each(function(){
            if(this.value == '') {
                valid = false;
            }
        });
        
        if(valid) {
            $.post('/index/register', $('#register-form').serialize(), function(data) {
                var color = data.split('|')[0];
                var message = data.split('|')[1];
                $('#register-error').text(message).show('pulsate', {times: 2}).css('color', color);
            });
        } else {
            $('#register-error').text('All fields must be filled').show('pulsate', {times: 2}).css('color', '#f00');
        }
        return false;
    });
    
    $('#register-button').click(function(){
        $('#login-window').height(300);
        $('#register-form').show();
        $('#login-form').hide();
        $('#register-form input:first').focus();
    });
    
    $('#login-link').click(function(){
        $('#login-window-blur').css('height', $(document).height() + 'px');
        $('#login-window-blur').css('width', $(document).width() + 'px');
        $('#login-window-blur').show();
        $('#login-window').fadeIn();
        $('#login-form input:first').focus();
    });
    
    $('#login-close').click(function(){
        $('#login-window').hide();
        $('#login-window-blur').hide();
        $('#login-form').show();
        $('#register-form').hide();
        $('#login-window').height('auto');
        return false;
    });
    
    $('#submenu a').click(function(ev){
        ev.preventDefault();
            
        $('#big_logo').effect('bounce', {times: 2, distance: 10, direction: "up"}, 280);

        $('#submenu a').each(function(){
            $(this).attr('class', '');
        });

        $(this).attr('class', 'active'); 

        $('.item-list').each(function(){
            $(this).hide();
        });
        
        $('.item-list#' + this.id).show();

        $('#category').text(controller + ' - ' + tabs[this.id]);


        return false;
        
    });
    
});
