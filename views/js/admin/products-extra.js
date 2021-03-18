/**
* 2015 KaisarCode
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    KaisarCode <kaisar@kaisarcode.com>
*  @copyright 2015 KaisarCode.com
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/**
 * Placeholder definitions
 * */
if(!meli_data) meli_data = {};

if(!meli_data.pictures) {
    meli_data.pictures = [];
}
if(!meli_data.variations) {
    meli_data.variations = [];
}

/**
 * Catch if has attributes
 * */
meli_data.has_attributes = false;
if(meli_data.attributes){
    meli_data.has_attributes = true;
}

/**
 * Stringify data and append to post vars
 * */
function setMeliJSON(){
    var meli_json = JSON.stringify(meli_data).replace(/\\n/g,'');
    jQuery('#meli-data').val(meli_json);
    return meli_json;
}
setMeliJSON();

/**
 * Site Id
 * */
jQuery('#meli-item-site').on('change',function(){
    meli_data.site_id = jQuery(this).val();
    setMeliJSON();
});

/**
 * Category:
 * categories are loaded asynchronously
 * search for "Adding selected category to meli_data"
 * */

/**
 * Title
 * */
var ttl_maxlen = 60;
jQuery('#meli-item-title').on('change',function(){
    meli_data.title = jQuery(this).val().replace('"','&quot;');
    if (meli_data.title.length > 60) {
        meli_data.title = meli_data.title.substring(0, 60);
        jQuery('#meli-item-title').val(meli_data.title);
    }
    setMeliJSON();
});

/**
 * Description:
 * TinyMCE doesn't changes the textarea as default
 * that's why we attach the change event to the tinyMCE setup
 * go to that line searching for "Show tinyMCE"
 * */
if (!meli_data.dscription) {
    meli_data.description = ' ';
}

/**
 * Listing Type
 * listing types are loaded asynchronously
 * we're setting up its default meli data in function showListingTypes
 * */
jQuery('#meli-item-listing-type').on('change',function(){
    meli_data.listing_type_id = jQuery(this).val();
    if (meli_data.buying_mode == 'auction' || meli_data.listing_type_id == 'free') {
        jQuery('#meli-item-automatic-relist-container').hide();
    } else {
        jQuery('#meli-item-automatic-relist-container').show();
    }
    setMeliJSON();
});

/**
 * Buying Mode
 * buying modes are loaded asynchronously
 * find where the default meli data is set in "Add default BM value"
 * */
jQuery('#meli-item-buying-mode').on('change',function(){
    meli_data.buying_mode = jQuery(this).val();
    if (meli_data.buying_mode == 'auction' || meli_data.listing_type_id == 'free') {
        jQuery('#meli-item-automatic-relist-container').hide();
    } else {
        jQuery('#meli-item-automatic-relist-container').show();
    }
    setMeliJSON();
});

/**
 * Condition
 * condition states are loaded asynchronously
 * find where the default meli data is set in "Add default Condition value"
 * */
jQuery('#meli-item-condition').on('change',function(){
    meli_data.condition = jQuery(this).val();
    setMeliJSON();
});

/**
 * Price
 * */
jQuery('#meli-item-price').on('change',function(){
    meli_data.price = jQuery(this).val();
    if (meli_data.variations.length > 0) {
        jQuery(meli_data.variations).each(function(b, a){
            a.price = meli_data.price;
        });
    }
    setMeliJSON();
});

/**
 * Quantity
 * */
var ps_quantity = parseInt(jQuery('#meli-item-quantity').attr('data-ps-quantity'));
if (ps_quantity == 0) {
    ps_quantity = 1;
    meli_data.available_quantity = 1;
    jQuery('#meli-item-quantity').val(1);
    jQuery('#meli-item-quantity').attr('data-ps-quantity', 1);
    setMeliJSON();
}
jQuery('#meli-item-quantity').on('change',function(){
    meli_data.available_quantity = parseInt(jQuery(this).val());
    ps_quantity = meli_data.available_quantity;
    setMeliJSON();
});

/**
 * Warranty
 * */
jQuery('#meli-item-warranty').on('change',function(){
    meli_data.warranty = jQuery(this).val().replace('"','&quot;');
    setMeliJSON();
});

/**
 * Automatic Relist
 * */
jQuery('#meli-item-automatic-relist').on('change',function(){
    if (jQuery(this).is(':checked')) {
        meli_data.automatic_relist = true;
    } else {
        meli_data.automatic_relist = false;
    }
    setMeliJSON();
});

/**
 * Status
 * */
jQuery('#meli-item-status-active').on('change',function(){
    if(jQuery(this).is(':checked')) {
        meli_data.status = jQuery(this).val();
    }
    setMeliJSON();
});
jQuery('#meli-item-status-paused').on('change',function(){
    if(jQuery(this).is(':checked')) {
        meli_data.status = jQuery(this).val();
    }
    setMeliJSON();
});
jQuery('#meli-item-status-closed').on('change',function(){
    if(jQuery(this).is(':checked')) {
        meli_data.status = jQuery(this).val();
    }
    setMeliJSON();
});

/**
 * Seller Contact
 * */
if (!meli_data.id) {
    jQuery('#meli-seller-contact-area-code').on('change',function(){
        if (!meli_data.seller_contact) {
            meli_data.seller_contact = {};
        }
        meli_data.seller_contact.area_code = jQuery(this).val();
        setMeliJSON();
    });
    jQuery('#meli-seller-contact-area-phone').on('change',function(){
        if (!meli_data.seller_contact) {
            meli_data.seller_contact = {};
        }
        meli_data.seller_contact.phone = jQuery(this).val();
        setMeliJSON();
    });
    jQuery('#meli-seller-contact-email').on('change',function(){
        if (!meli_data.seller_contact) {
            meli_data.seller_contact = {};
        }
        meli_data.seller_contact.email = jQuery(this).val();
        setMeliJSON();
    });
}

/**
 * Location
 * Just on Classifieds. See: classifiedsAction()
 * */
jQuery('#meli-location-address').on('change',function(){
    if (!meli_data.location) {
        meli_data.location = {};
    }
    meli_data.location.address_line = jQuery(this).val();
    setMeliJSON();
});
jQuery('#meli-location-zip').on('change',function(){
    if (!meli_data.seller_contact) {
        meli_data.seller_contact = {};
    }
    meli_data.seller_contact.zip_code = jQuery(this).val();
    setMeliJSON();
});

country_not_ft = false;
state_not_ft = false;
city_not_ft = false;
function locationSelector(){
    jQuery.getJSON('https://api.mercadolibre.com/classified_locations/countries',function(countries){
        if (!meli_data.location) {
            meli_data.location = {};
        }
        jQuery('#meli-location-country-container').html('\
            <div><select id="meli-location-country"></select></div>\
            <br />\
            <div id="meli-location-state-container"></div>\
        ');
        jQuery(countries).each(function(i, country){
            var selected = '';
            if (meli_data.location.country) {
                if (meli_data.location.country.id == country.id) {
                    selected = ' selected="selected"';
                }
            }
            jQuery('#meli-location-country').append('\
                <option value="'+country.id+'"'+selected+'>'+country.name+'</option>\
            ');
        });
        jQuery('#meli-location-country').off('change');
        jQuery('#meli-location-country').on('change',function(){
            var country_id = jQuery(this).val();
            jQuery.getJSON('https://api.mercadolibre.com/classified_locations/countries/'+country_id, function(country){
                
                if (country.geo_information) {
                    var geo = country.geo_information.location;
                    meli_data.location.latitude = geo.latitude;
                    meli_data.location.longitude = geo.longitude;
                }
                meli_data.location.country = {
                    id:country_id
                }
                if (country_not_ft) {
                    if (meli_data.location.state) { 
                        delete meli_data.location.state;
                    }
                    if (meli_data.location.city) { 
                        delete meli_data.location.city;
                    }
                    if (meli_data.location.neighborhood) { 
                        delete meli_data.location.neighborhood;
                    }
                }
                country_not_ft = true;
                setMeliJSON();
                if (country.states.length) {
                    var states = country.states;
                    
                    jQuery('#meli-location-state-container').html('\
                        <div><select id="meli-location-state"></select></div>\
                        <br />\
                        <div id="meli-location-city-container"></div>\
                    ');
                    
                    jQuery('#meli-coverage-areas-select-container').empty();
                    jQuery(states).each(function(i, state){
                        var selected = '';
                        if (meli_data.location.state) {
                            if (meli_data.location.state.id == state.id) {
                                selected = ' selected="selected"';
                            }
                        }
                        
                        if (
                        state.name != 'Brasil' &&
                        state.name != 'USA' &&
                        state.name != 'Uruguay' &&
                        state.name != 'República Dominicana' &&
                        state.name != 'Paraguay'
                        ) {
                            
                            jQuery('#meli-location-state').append('\
                                <option value="'+state.id+'"'+selected+'>'+state.name+'</option>\
                            ');
                            
                            var checked = '';
                            if (meli_data.coverage_areas) {
                                jQuery(meli_data.coverage_areas).each(function(i1, ca){
                                    if (ca == state.id) {
                                        checked = ' checked="checked"';
                                        return;
                                    }
                                });
                            }
                            
                            jQuery('#meli-coverage-areas-select-container').append('\
                                <div>\
                                    <input\
                                    type="checkbox"\
                                    id="meli-coverage-area-'+state.id+'"\
                                    data-state-id="'+state.id+'"\
                                    class="meli-coverage-area"'+checked+' />\
                                    <label>'+state.name+'</label>\
                                </div>\
                            ');
                        }
                        
                        jQuery('#meli-coverage-area-'+state.id).off('change');
                        jQuery('#meli-coverage-area-'+state.id).on('change', function(){
                            meli_data.coverage_areas = [];
                            jQuery('.meli-coverage-area').each(function(i1, ca){
                                if (jQuery(ca).is(':checked')) {
                                    meli_data.coverage_areas.push(jQuery(ca).attr('data-state-id'));
                                }
                            });
                            setMeliJSON();
                        });
                    });
                    jQuery('#meli-location-state').off('change');
                    jQuery('#meli-location-state').on('change',function(){
                        var state_id = jQuery(this).val();
                        
                        jQuery.getJSON('https://api.mercadolibre.com/classified_locations/states/'+state_id, function(state){
                            
                            if (state.geo_information) {
                                var geo = state.geo_information.location;
                                meli_data.location.latitude = geo.latitude;
                                meli_data.location.longitude = geo.longitude;
                            }
                            meli_data.location.state = {
                                id:state_id
                            }
                            if (state_not_ft) {
                                if (meli_data.location.city) { 
                                    delete meli_data.location.city;
                                }
                                if (meli_data.location.neighborhood) { 
                                    delete meli_data.location.neighborhood;
                                }
                            }
                            state_not_ft = true;
                            setMeliJSON();
                            if (state.cities.length) {
                                var cities = state.cities;
                                jQuery('#meli-location-city-container').html('\
                                    <div><select id="meli-location-city"></select></div>\
                                    <br />\
                                    <div id="meli-location-neighborhood-container"></div>\
                                ');
                                jQuery(cities).each(function(i, city){
                                    var selected = '';
                                    if (meli_data.location.city) {
                                        if (meli_data.location.city.id == city.id) {
                                            selected = ' selected="selected"';
                                        }
                                    }
                                    jQuery('#meli-location-city').append('\
                                        <option value="'+city.id+'"'+selected+'>'+city.name+'</option>\
                                    ');
                                });
                                jQuery('#meli-location-city').off('change');
                                jQuery('#meli-location-city').on('change',function(){
                                    var city_id = jQuery(this).val();
                                    jQuery.getJSON('https://api.mercadolibre.com/classified_locations/cities/'+city_id, function(city){
                                        
                                        if (city.geo_information) {
                                            var geo = city.geo_information.location;
                                            meli_data.location.latitude = geo.latitude;
                                            meli_data.location.longitude = geo.longitude;
                                        }
                                        meli_data.location.city = {
                                            id:city_id
                                        }
                                        if (city_not_ft) {
                                            if (meli_data.location.neighborhood) { 
                                                delete meli_data.location.neighborhood;
                                            }
                                        }
                                        city_not_ft = true;
                                        setMeliJSON();
                                        if (city.neighborhoods.length) {
                                            var neighborhoods = city.neighborhoods;
                                            jQuery('#meli-location-neighborhood-container').html('\
                                                <div><select id="meli-location-neighborhood"></select></div>\
                                            ');
                                            jQuery(neighborhoods).each(function(i, neighborhood){
                                                var selected = '';
                                                
                                                if (meli_data.location.neighborhood) {
                                                    if (meli_data.location.neighborhood.id == neighborhood.id) {
                                                        selected = ' selected="selected"';
                                                    }
                                                }
                                                jQuery('#meli-location-neighborhood').append('\
                                                    <option value="'+neighborhood.id+'"'+selected+'>'+neighborhood.name+'</option>\
                                                ');
                                            });
                                            jQuery('#meli-location-neighborhood').off('change');
                                            jQuery('#meli-location-neighborhood').on('change',function(){
                                                var neighborhood_id = jQuery(this).val();
                                                jQuery.getJSON('https://api.mercadolibre.com/classified_locations/neighborhoods/'+neighborhood_id, function(neighborhood){
                                                    
                                                    if (neighborhood.geo_information) {
                                                        var geo = neighborhood.geo_information.location;
                                                        meli_data.location.latitude = geo.latitude;
                                                        meli_data.location.longitude = geo.longitude;
                                                    }
                                                    meli_data.location.neighborhood = {
                                                        id:neighborhood_id
                                                    }
                                                    setMeliJSON();
                                                });
                                            });
                                            jQuery('#meli-location-neighborhood').change();
                                        }
                                    });
                                });
                                jQuery('#meli-location-city').change();
                            }
                        });
                    });
                    jQuery('#meli-location-state').change();
                }
                
            });
        });
        jQuery('#meli-location-country').change();
    });
}

jQuery('#meli-coverage-areas-checkall').on('click',function(){
    jQuery('.meli-coverage-area').prop('checked', 'checked');
    meli_data.coverage_areas = [];
    jQuery('.meli-coverage-area').each(function(b,a){
        meli_data.coverage_areas.push(jQuery(a).attr('data-state-id'));
    });
});

/**
 * Youtube Video
 * */
jQuery('#meli-item-youtube').on('change',function(){
    var input = jQuery(this);
    var val = input.val();
    if (val) {
        var ytid = youtubeIdParser(val);
        if (ytid) {
            jQuery('#meli-item-youtube-show').html('<br /><iframe width="280" height="280" src="https://www.youtube.com/embed/'+ytid+'" frameborder="0" allowfullscreen></iframe>');
            
            meli_data.video_id = ytid;
            
        } else {
            jQuery('#meli-item-youtube-show').html('\
            <div class="alert alert-danger">\
                '+m4p.messages.youtubeURLError+'\
            </div>');
            
            meli_data.video_id = '';
        }
    }else{
        jQuery('#meli-item-youtube-show').empty();
        
        meli_data.video_id = '';
    }
    
    setMeliJSON();
});

/**
 * Pictures
 * */
function imageAdded(dz){
    var elem = dz.element;
    jQuery(elem).removeClass('empty');
}

function imageRemoved(dz){
    var elem = dz.element;
    var imglength = jQuery(elem).find('.dz-image').size();
    if(imglength < 1) {
        jQuery(elem).addClass('empty');
    }
}


var default_pictures = [];
jQuery('#meli-item-images-dropzone').dropzone({
    url: m4p.paths.base+'/services/external/images/upload.php?token='+m4p.tokens.images,
    acceptedFiles: 'image/*',
    addRemoveLinks:true,
    dictDefaultMessage: '<b>'+m4p.messages.fileUploadDefault+'</b><br /><span>'+m4p.messages.fileUploadDefaultSub+'</span>',
    dictRemoveFile: m4p.messages.fileUploadRemove,
    maxFiles: 12,
    init: function() {
        var thisDropzone = this;
        
        /**
         * Load existent pictures
         * */
        for(var i in meli_data.pictures){
            var a = meli_data.pictures[i];
             
            var mockFile = { name: a.fname, size: a.weight };
            
            thisDropzone.emit("addedfile", mockFile);
            thisDropzone.emit("thumbnail", mockFile, a.protocol_based_url);
            
            default_pictures.push({
                id: a.id,
                url: a.url,
                protocol_based_url: a.protocol_based_url,
                source: a.protocol_based_url
            });
            
            /**
             * Add picture index reference to "remove" link
             * */
            var dzelem = jQuery(thisDropzone.element);
            var rmlink = dzelem.find('.dz-remove').eq(i);
            jQuery(rmlink).data('meli_picture_data', {
                index: parseInt(i)
            });
            
            /**
             * Add pictures to meli_data
             * */
            meli_data.pictures[i] = {
                id: a.id,
                source: a.protocol_based_url,
                fname: a.fname,
                weight: a.weight
            };
            
            setMeliJSON();
            
            /**
             * Image Added Callback
             * */
            imageAdded(thisDropzone);
        };
        this.on('success', function(a,b){
            /**
             * Add picture to meli_data
             * */
            var res = jQuery.parseJSON(b);
            jQuery(res).each(function(d,c){
                meli_data.pictures.push({
                    id: c.id,
                    source: m4p.paths.services_abs+'/external/images/'+c.path,
                    fname: c.name,
                    weight: c.weight
                });
            });
            setMeliJSON();
            
            /**
             * Add picture index reference to "remove" link
             * */
            var img_index = jQuery(a.previewElement).parent().find('.dz-image img').size()-1;
            var rmlink = jQuery(a._removeLink);
            jQuery(rmlink).data('meli_picture_data', {
                index: img_index
            });
            
            /**
             * Add picture index reference to "remove" link
             * */
            jQuery(a._removeLink).data('meli_picture_data', {
                index: meli_data.pictures.length-1
            });
            
        });
        
        this.on("removedfile", function(a) {
            
            var rm_index = jQuery(a._removeLink).data('meli_picture_data').index;
            
            /**
             * Remove picture from /tmp
             * */
            if (meli_data.pictures[rm_index].fname) {
                var fname = meli_data.pictures[rm_index].fname;
                var rmurl = m4p.paths.base+'/services/external/images/remove.php?token='+m4p.tokens.images;
                jQuery.post(rmurl, { file: fname} );
            }
            
            /**
             * Remove picture from meli_data
             * */
            meli_data.pictures.splice(rm_index, 1);
            setMeliJSON();
            
            /**
             * Add picture index reference to "remove" link
             * */
            var dzelem = jQuery(thisDropzone.previewsContainer);
            var rmlinks = dzelem.find('.dz-remove');
            jQuery(rmlinks).each(function(i){
                var rmlink = jQuery(this);
                rmlink.data('meli_picture_data', {
                    index: i
                });
            });
            
            /**
             * Image Removed Callback
             * */
            imageRemoved(thisDropzone);
        });
        
        this.on("addedfile", function(a) {
            imageAdded(thisDropzone, a);
        });
    }
});

/**
 * Compile Variations
 * */
function compileVariations(){
    
    meli_data.variations = [];
    meli_data.pictures = [];
    jQuery('.meli-item-attribute-group').each(function(){
        var group_elem = jQuery(this);
        var variation_elems = group_elem.find('.meli-variation');
        var variation_images = group_elem.find('.dz-image img');
        var variation_id = group_elem.find('.meli-variation-id').val();
        var variation_price = meli_data.price;
        var variation_quantity = group_elem.find('.meli-variation-quantity').val();
        var variation_combination_link = group_elem.find('.meli-combination-link').val();
        var combinations = [];
        
        jQuery(variation_elems).each(function(){
            var variation_elem = jQuery(this);
            if (variation_elem.attr('data-is-variation-value') == '0') {
                combinations.push({
                    id: variation_elem.attr('data-variation-id'),
                    value_id: variation_elem.val()
                });
            } else {
                combinations.push({
                    id: variation_elem.attr('data-variation-id'),
                    value_name: variation_elem.val()
                });
            }
        });
        
        var picture_ids = [];
        jQuery(variation_images).each(function(b,a){
            var src = jQuery(a).data('src');
            picture_ids.push(src);
            meli_data.pictures.push({
                source: src
            });
        });  
        
        meli_data.variations.push({
            attribute_combinations:combinations,
            id: parseInt(variation_id),
            price: variation_price,
            available_quantity: variation_quantity,
            combination_link: variation_combination_link,
            picture_ids: picture_ids
        });
    });
    setMeliJSON();
}

/**
 * Add Variation
 * */
function addVariation(data, variation_group_num, dont_disable){
    
    var attr_group_str =  '';
    
    if (typeof(dont_disable) == 'undefined') {
        dont_disable = false;
    }
    
    if(!meli_data.variations[variation_group_num]){
        meli_data.variations[variation_group_num] = {};
    }
    
    var variation_id;
    var variation_price = 0;
    var variation_quantity = 0;
    
    if (meli_data.variations[variation_group_num]) {
        var saved_attr_obj = {};
        jQuery(meli_data.variations[variation_group_num].attribute_combinations).each(function(b, a){
            saved_attr_obj[a.id] = a;
        });
    }
    
    jQuery(data).each(function(b,a){
        var id = a.id;
        var name = a.name;
        var value_type = a.value_type;
        var values = a.values;
        var tags = a.tags;
        
        var attr_str = '';
        
        var required = '';
        if(tags.required) required=' required="required"';
        
        if(!tags.fixed){
            
            var disabled = '';
            if (meli_data.sold_quantity > 0 && meli_data.status != 'closed') {
                if (!dont_disable) {
                    disabled = ' disabled="disabled"';
                }
            }
            
            if(value_type=='list' || value_type=='boolean'){
                
                attr_str+='<select \
                class="meli-variation form-control" \
                data-variation-group-index="'+variation_group_num+'" \
                data-is-variation-value="0" \
                data-variation-id="'+id+'"'+required+disabled+'\
                >';
                if(!required) attr_str+='<option value="">--'+m4p.messages.notSet+'--</option>';
                jQuery(values).each(function(d,c){
                    
                    /**
                     * Populating Variation Values
                     * */
                    var selected = '';
                    if(meli_data.variations[variation_group_num]){
                        jQuery(meli_data.variations[variation_group_num].attribute_combinations).each(function(f,g){
                            if (c.id == g.value_id) {
                                selected = ' selected="selected"';
                            }
                        });
                    }
                    attr_str+='<option value="'+c.id+'"'+selected+'>'+c.name+'</option>';
                    
                });
                attr_str+='</select>';
                
            }
            if(value_type=='string'){
                
                var variation_value = ' ';
                if(meli_data.variations[variation_group_num]){
                    if (typeof(saved_attr_obj[id]) != 'undefined') {
                        variation_value = saved_attr_obj[id].value_name;
                    }
                }
                
                var value_max_length = '';
                if (a.value_max_length) {
                    value_max_length = ' maxlength="'+a.value_max_length+'"';
                }
                
                attr_str+='<input class="meli-variation form-control" type="text" data-variation-group-index="'+variation_group_num+'" data-is-variation-value="1" data-variation-id="'+id+'" value="'+variation_value+'"'+required+disabled+''+value_max_length+' />';
            }
            if(value_type=='number'){
                
                var variation_value = ' ';
                if(meli_data.variations[variation_group_num]){
                    if (typeof(saved_attr_obj[id]) != 'undefined') {
                        variation_value = saved_attr_obj[id].value_name;
                    }
                }
                
                var value_max_length = '';
                if (a.value_max_length) {
                    value_max_length = ' maxlength="'+a.value_max_length+'"';
                }
                
                attr_str+='<input class="meli-variation form-control" type="number" data-variation-group-index="'+variation_group_num+'" data-is-variation-value="1" data-variation-id="'+id+'" value="'+variation_value+'"'+required+disabled+''+value_max_length+' />';
            }
            
            /**
             * Variation ID
             * */
            
            if(meli_data.variations[variation_group_num].id){
                variation_id = meli_data.variations[variation_group_num].id;
            }
            
            /**
             * Variation Price
             * */
            variation_price = meli_data.price;
            
            /**
             * Variation Quantity
             * */
            if(meli_data.variations[variation_group_num].available_quantity){
                variation_quantity = meli_data.variations[variation_group_num].available_quantity;
            }
            
            var attr_display_name = name;
            if(tags.required) attr_display_name += ' <b style="color:red;">*</b>';
            attr_group_str += '\
            <div class="meli-attribute-element-container">\
                <div class="form-group row">\
                    <label class="control-label col-lg-3">\
                        <span>'+attr_display_name+'</span>\
                    </label>\
                    <div class="col-lg-9">\
                        '+attr_str+'\
                    </div>\
                </div>\
            </div>\
            ';
             
        }
        
    });
    
    /**
     * Set variations HTML
     * */
    
    var variation_not_modifiable_msg = '';
    
    var dynamic_variation_fields_html= '\
    <div class="meli-variation-images-container form-group row">\
        <label class="control-label col-lg-3">\
            <span>'+m4p.messages.images+'</span>\
        </label>\
        <div class="col-lg-9">\
            <div id="meli-variation-dropzone-'+variation_group_num+'" class="dropzone empty meli-variation-images"></div>\
        </div>\
    </div>\
    '+attr_group_str;
    
    var price_variation_field = '\
    <div class="meli-variation-price-container form-group row">\
        <label class="control-label col-lg-3">\
            <span>'+m4p.messages.price+' ('+meli_data.currency_id+')</span>\
        </label>\
        <div class="col-lg-9">\
            <input class="meli-variation-price form-control" type="number" value="'+variation_price+'" required="required" placeholder="'+m4p.messages.placeholderPrice+'" />\
        </div>\
    </div>';
    
    if (meli_data.id == '' && !variation_quantity) {
        
        if (typeof(ps_prod_comb_stock[variation_group_num+1]) != 'undefined') {
            variation_quantity = ps_prod_comb_stock[variation_group_num+1].quantity;
        } else {
            variation_quantity = 1;
        }
          
    }
    
    var quantity_variation_field = '\
    <div class="meli-variation-quantity-container form-group row">\
        <label class="control-label col-lg-3">\
            <span>'+m4p.messages.availQuantity+'</span>\
        </label>\
        <div class="col-lg-9">\
            <input class="meli-variation-quantity form-control" type="number" value="'+variation_quantity+'" required="required" placeholder="'+m4p.messages.placeholderQuantity+'" />\
        </div>\
    </div>';
    
    var link_variation_field_opts = '<option value="0">'+m4p.messages.notSet+'</option>';
    jQuery(ps_prod_comb_stock).each(function(i, at){
        
        if (meli_data.id == '') {
            var vgnext = variation_group_num + 1;
            if (vgnext == i) {
                selected = ' selected="selected"';
                link_variation_field_opts += '<option value="'+i+'"'+selected+'>'+m4p.messages.combinationNum+i+'</option>';
            }
        } else {
            if (i>0){
                var selected = '';
                if (meli_data.variations[variation_group_num].combination_link == i) {
                    selected = ' selected="selected"';
                }
                link_variation_field_opts += '<option value="'+i+'"'+selected+'>'+m4p.messages.combinationNum+i+'</option>';
            }
        }
        
        
    });
    var link_variation_field = '\
    <div class="meli-variation-link-container form-group row">\
        <label class="control-label col-lg-3">\
            <span>'+m4p.messages.linkWith+'</span>\
        </label>\
        <div class="col-lg-9">\
            <select class="meli-combination-link form-control">'+link_variation_field_opts+'</select>\
        </div>\
    </div>';
    
    /**
     * If status is closed
     * remove dynamic fields
     * */
    if (meli_data.status == 'closed') {
        dynamic_variation_fields_html= '';
    }
    
    jQuery('#meli-attributes-groups-container').append('\
        <div class="meli-item-attribute-group panel">\
            <div class="meli-item-attribute-group-content">\
                <input type="hidden" class="meli-variation-id" value="'+variation_id+'" />\
                '+dynamic_variation_fields_html+'\
                '+price_variation_field+'\
                '+quantity_variation_field+'\
                '+link_variation_field+'\
                <div class="clearfix meli-btn-remove-variation-container">\
                    <button class="btn btn-default meli-btn-remove-variation pull-right" type="button">\
                        <span class="icon-trash"></span>\
                    </button>\
                </div>\
            </div>\
            <div class="meli-item-attribute-group-msg"></div>\
        </div>\
    ');
    
    jQuery('.meli-combination-link').off('change');
    jQuery('.meli-combination-link').on('change',function(){
        compileVariations();
    });
    
    if (!meli_data.variations[variation_group_num].price) { 
        compileVariations();
    }
    
    if (meli_data.status == 'closed') {
        jQuery('.meli-variation-images-container').hide();
        jQuery('#btn-meli-add-variation').hide();
        jQuery('.meli-btn-remove-variation').hide();
    }
    
    if (cat_attr_type == 'attributes') {
        jQuery('#btn-meli-add-variation').hide();
        jQuery('.meli-btn-remove-variation').hide();
        jQuery('.meli-variation-link-container').hide();
        
        jQuery('.meli-variation-quantity-container').hide();
        jQuery('#meli-item-quantity-container').show();
    }
    
    /**
     * Variations Image Uploader
     * */
    
    jQuery('#meli-variation-dropzone-'+variation_group_num).dropzone({
        url: m4p.paths.base+'/services/external/images/upload.php?token='+m4p.tokens.images,
        acceptedFiles: 'image/*',
        addRemoveLinks:true,
        dictDefaultMessage: '<b>'+m4p.messages.fileUploadDefault+'</b><br /><span>'+m4p.messages.fileUploadDefaultSub+'</span>',
        dictRemoveFile: m4p.messages.fileUploadRemove,
        maxFiles: 12,
        init: function() {
            this.variation_group_num = variation_group_num;
            var thisDropzone = this;
            
            var pic_by_ids = {};
            
            for(var i = 0; i < meli_data.pictures.length; i++){
                pic_by_ids[meli_data.pictures[i].id] = meli_data.pictures[i];
            }
            
            if (!meli_data.id) {
                for(var i = 0; i < default_pictures.length; i++){
                    pic_by_ids[default_pictures[i].id] = default_pictures[i];
                    meli_data.variations[variation_group_num].picture_ids.push(default_pictures[i].id);
                }   
            }
            
            var i = 0;
            
            jQuery(meli_data.variations[variation_group_num].picture_ids).each(function(b,a){
                if (pic_by_ids[a]) {
                    
                    var c = pic_by_ids[a];
                    var src = c.source;
                    var mockFile = { name: c.fname, size: c.weight };
                    thisDropzone.emit("addedfile", mockFile);
                    thisDropzone.emit("thumbnail", mockFile, src);
                    var dzelem = jQuery(thisDropzone.element);
                    
                    /**
                     * Add pictures to meli_data
                     * */
                    jQuery(dzelem).find('.dz-image').eq(i).find('img').data('src', src);
                    jQuery(dzelem).find('.dz-image').eq(i).find('img').attr('src', src);
                    if (!meli_data.id) {
                        meli_data.variations[variation_group_num].picture_ids[i] = src;
                    }
                    
                    /**
                     * Image Added Callback
                     * */
                    imageAdded(thisDropzone);
                }
                
                /**
                 * Add picture index reference to "remove" link
                 * */
                if (typeof(dzelem) != 'undefined') {
                    var rmlink = dzelem.find('.dz-remove').eq(i);
                    jQuery(rmlink).data('meli_picture_data', {
                        index: parseInt(i)
                    });
                }
                
                i++;
            });
            
            this.on('success', function(a, b){
                
                var res = jQuery.parseJSON(b)[0];
                var src = m4p.paths.services_abs+'/external/images/'+res.path;
                
                previewElem = jQuery(a.previewElement);
                
                previewElem.find('.dz-image img').data('src', src);
                previewElem.find('.dz-image img').attr('src', src);
                
                /**
                 * Add picture index reference to "remove" link
                 * */
                var img = jQuery(a.previewElement).parent().find('.dz-image img');
                var img_index = img.size()-1;
                var rmlink = jQuery(a._removeLink);
                jQuery(rmlink).data('meli_picture_data', {
                    index: img_index
                });
                
                compileVariations();
                
            });
            
            this.on("removedfile", function(a) {
                
                var rm_index = jQuery(a._removeLink).data('meli_picture_data').index;
                
                /**
                 * Remove picture from /tmp
                 * */
                if (meli_data.variations[this.variation_group_num].picture_ids[rm_index].fname) {
                    var fname = meli_data.variations[this.variation_group_num].picture_ids[rm_index].fname;
                    var rmurl = m4p.paths.base+'/services/external/images/remove.php?token='+m4p.tokens.images;
                    jQuery.post(rmurl, { file: fname} );
                }
                
                /**
                 * Add picture index reference to "remove" link
                 * */
                var dzelem = jQuery(thisDropzone.previewsContainer);
                var rmlinks = dzelem.find('.dz-remove');
                jQuery(rmlinks).each(function(i){
                    var rmlink = jQuery(this);
                    rmlink.data('meli_picture_data', {
                        index: i
                    });
                });
                
                /**
                 * Image Removed Callback
                 * */
                compileVariations();
                imageRemoved(thisDropzone);
                
            });
            
        }
    });
    
    /**
     * Adding Variations to meli_data 
     **/
    jQuery('.meli-variation').off('change');
    jQuery('.meli-variation').on('change',function(){
        compileVariations();
    });
    
    jQuery('.meli-variation-quantity').off('change');
    jQuery('.meli-variation-quantity').on('change',function(){
        compileVariations();
    });
    
    jQuery('.meli-btn-remove-variation').off('click');
    jQuery('.meli-btn-remove-variation').data('delete_index',variation_group_num);
    jQuery('.meli-btn-remove-variation').on('click',function(){
        if(jQuery('.meli-item-attribute-group').size()>1){
            jQuery(this).parent().parent().parent().remove();
            compileVariations();
        }else{
            alert(m4p.messages.cantRemoveVariation);
        }
    });
}

/**
 * Get Attributes
 * */
function attributesShown() {
    
    jQuery('#meli-item-images-container').hide();
    jQuery('#meli-item-quantity-container').hide();
    
    if (meli_data.buying_mode == 'classified') {
        jQuery('#meli-item-quantity').attr('disabled','disabled');
    } else {
        jQuery('#meli-item-quantity').removeAttr('disabled');
    }
    if (cat_attr_type == 'attributes') {
        jQuery('.meli-variation-quantity-container').hide();
        jQuery('.meli-variation-images-container').hide();
        jQuery('#meli-item-quantity-container').show();
        jQuery('#meli-item-images-container').show();
    } else {
        jQuery('#meli-item-quantity-container').hide();
        jQuery('#meli-item-images-container').hide();
        jQuery('.meli-variation-quantity-container').show();
        jQuery('.meli-variation-images-container').show();
    }
    
    jQuery('#meli-attributes-content').show();
}
function attributesHidden() {
    jQuery('#meli-item-quantity-container').show();
    jQuery('#meli-item-images-container').show();
}
function getAttributes(cat){
    var url = 'https://api.mercadolibre.com/categories/'+cat+'/attributes';
    
    jQuery.getJSON(url,function(data){
        if(data.length){
            
            jQuery('#meli-images-panel').stop().hide();
            
            var attrs_editable = false;
            jQuery(data).each(function(b,a){
                var tags = a.tags;
                if(!tags.fixed) attrs_editable = true;
            });
            
            jQuery('#meli-item-attributes-container').show();
            jQuery('#meli-attributes-content').show(0, '' ,function(){
                jQuery('#meli-attributes-content').empty();
                /* ************************************************** */
                
                jQuery('#meli-attributes-content').html('\
                <div id="meli-attributes-groups-container"></div>\
                <div class="panel-footer" style="height: auto">\
                    <button id="btn-meli-add-variation" class="btn btn-default" type="button">\
                        <span class="icon-plus"></span> '+m4p.messages.addVariation+'\
                    </button>\
                </div>');
                var variation_group_num = 0;
                jQuery('#btn-meli-add-variation').on('click',function(){
                    addVariation(data, variation_group_num, true);
                    variation_group_num++; 
                });
                
                if(meli_data.variations.length){
                    jQuery(meli_data.variations).each(function(){
                        addVariation(data, variation_group_num);
                        variation_group_num++; 
                    });
                    compileVariations();
                }else{
                    if(!meli_data.id){
                        addVariation(data, variation_group_num);
                        variation_group_num++;
                        compileVariations();
                    }
                }
                
                /* ************************************************** */
                attributesShown();
            });
            
            if(!attrs_editable){
                jQuery('#meli-item-attributes-container').hide();
                attributesHidden();
            }
            
        }else{
            jQuery('#meli-item-attributes-container').stop().hide(0, '' ,function(){
                jQuery('#meli-attributes-content').empty();
            });
            attributesHidden();
        }
    });
}

/**
 * Listing Types
 * */
function showListingTypes(data){
    
    /**
     * Creating LT options
     * */
    jQuery('#meli-item-listing-type').empty();
    jQuery(data).each(function(b,a){
        var selected = '';
        if(meli_data.listing_type_id == a.id){
            selected = ' selected="selected"';
        }
        jQuery('#meli-item-listing-type').append('<option value="'+a.id+'"'+selected+'>'+a.name+'</option>');
    });
    
    /**
     * Add default LT value
     * to meli_data
     * */
    meli_data.listing_type_id = jQuery('#meli-item-listing-type').val();
    setMeliJSON();
    
    /**
     * Show LT container
     * */
    jQuery('#meli-item-listing-type-container').show();
}

/**
 * Classifieds Actions
 * Also, add variation button is hidden when classified, search for:
 * jQuery('#btn-meli-add-variation').hide();
 * */
function classifiedsAction(){
    if(meli_data.buying_mode == 'classified'){
      jQuery('#meli-item-location-container').show();
      jQuery('#meli-item-seller-contact-container').show();
      jQuery('#meli-item-coverage-areas-container').show();
      locationSelector();
    } else {
      jQuery('#meli-item-location-container').hide();
      jQuery('#meli-item-seller-contact-container').hide();
      jQuery('#meli-item-coverage-areas-container').hide();
    }
}

/**
 * Categories
 * */
var cat_folders_disabled = false;
var cat_attr_type;

if (typeof(meli_data.category_id) != 'undefined') {
    jQuery.getJSON('https://api.mercadolibre.com/categories/'+meli_data.category_id,function(data){
        cat_attr_type = data.attribute_types;
    });
}

var is_attributed = false;
var coverage_allowed = true;
function setCategory(data, tree, is_folder, path){
    if(!path) var path=false;
    
    jQuery(data).each(function(b,a){
        var cat=a;
        var name = cat.name;
        
        if(!is_folder){
            name = '\
            <input type="radio" id="meli-item-category-'+cat.id+'" name="meli-item-category" value="'+cat.id+'" />\
            <label class="meli-cat-radio-label" for="meli-item-category-'+cat.id+'">'+name+'</label>';
        }
        var branch = tree.addBranch({
            folder:is_folder,
            title:name,
            path: path
        });
        
        if(!is_folder){
            
            jQuery('#meli-item-category-'+cat.id).off('change');
            jQuery('#meli-item-category-'+cat.id).on('change',function(){
                
                /**
                 * Getting Attributes
                 * */
                
                if (cat.settings.coverage_areas == 'not_allowed') {
                    coverage_allowed = false;
                    delete meli_data.coverage_areas;
                    jQuery('#meli-item-coverage-areas-container').hide();
                    setMeliJSON();
                } else {
                    coverage_allowed = true;
                    jQuery('#meli-item-coverage-areas-container').show();
                }
                
                if (cat.attribute_types == 'attributes') {
                    is_attributed = true;
                } else {
                    is_attributed = false;
                }
                getAttributes(cat.id);
                
                /**
                 * Getting Listing types
                 * */
                jQuery.getJSON('https://api.mercadolibre.com/categories/'+cat.id+'/listing_types',function(data){
                    showListingTypes(data);
                });
                
                /**
                 * Adding selected category to meli_data
                 * */
                meli_data.category_id = jQuery(this).val();
                setMeliJSON();
                
                /**
                 * Showing "After Category" Meli Data Container
                 * */
                if(!jQuery('#meli-data-after-category').data('shown')){
                    jQuery('#meli-data-after-category').data('shown',true);
                    jQuery('#meli-data-after-category').show();
                }
                
                if (meli_data.condition == 'not_allowed') {
                    jQuery('#meli-item-condition-container').hide();
                } else {
                    jQuery('#meli-item-condition-container').show();
                }
                
            });
            
            jQuery('#meli-item-category-'+cat.id).attr('checked', 'checked');
            jQuery('#meli-item-category-'+cat.id).change();
            
            cat_attr_type = cat.attribute_types;
        }
        
        jQuery(branch.elem[0]).data('disabled', cat.id);
        jQuery(branch.elem[0]).data('cat', cat.id);
        jQuery(branch.elem[0]).data('path', branch.path);
        
        jQuery(branch.elem[0]).find('.kc-tree-title').on('click',function(){
            
            var br = jQuery(branch.elem[0]);
            var path = br.data('path');
            
            var url = 'https://api.mercadolibre.com/categories/'+br.data('cat');
            jQuery.getJSON(url,function(dt){
                if(dt.children_categories.length){
                    
                    if (!jQuery(br).data('set')) {
                        setCategory(dt.children_categories, tree, true, br.data('path'));
                        br.data('is_leaf',false);
                        jQuery(br).data('set', 'true');
                    }
                    
                }else{
                    
                    /**
                     * Populating Buying Modes
                     * */
                    jQuery('#meli-item-buying-mode').empty();
                    jQuery(dt.settings.buying_modes).each(function(b,a){
                        var selected = '';
                        if(a == meli_data.buying_mode){
                            selected = ' selected="selected"';
                        }
                        jQuery('#meli-item-buying-mode').append('<option value="'+a+'"'+selected+'>'+m4p.messages.buying_modes[a]+'</option>');
                    });
                    jQuery('#meli-item-buying-mode-container').show();
                    
                    /**
                     * Add default BM value
                     * to meli_data
                     * */
                    meli_data.buying_mode = jQuery('#meli-item-buying-mode').val();
                    setMeliJSON();
                    
                    /**
                     * Populating Conditions
                     * */
                    jQuery('#meli-item-condition').empty();
                    jQuery(dt.settings.item_conditions).each(function(b,a){
                        var selected = '';
                        if(a == meli_data.condition){
                            selected = ' selected="selected"';
                        }
                        jQuery('#meli-item-condition').append('<option value="'+a+'"'+selected+'>'+m4p.messages.conditions[a]+'</option>');
                    });
                    
                    /**
                     * Add default Condition value
                     * to meli_data
                     * */
                    meli_data.condition = jQuery('#meli-item-condition').val();
                    setMeliJSON();
                    
                    /**
                     * Classifieds Actions
                     * */
                    classifiedsAction();
                    
                    /**
                     * Opening Category
                     * */
                    if(!br.data('loaded')){
                        setCategory(dt, tree, false, br.data('path'));
                        br.data('is_leaf',true);
                        br.data('loaded',true);
                    }
                    
                }
            });
        });
    });
}

function showCategories(data){
    jQuery("#meli-category-container").empty();
    var tree = new orangeTree("#meli-category-container");
    setCategory(data, tree, true);
}

function showCurrency(data){
    meli_data.currency_id = data.default_currency_id;
    jQuery('#m4p-currency-iso').text(meli_data.currency_id)
    setMeliJSON();
}

/**
 * Call Categories
 * Override Currencies
 * */
jQuery('#meli-item-site').on('change',function(){
    var meli_site = jQuery(this);
    
    /**
     * Categories
     **/
    var url = 'https://api.mercadolibre.com/sites/'+meli_site.val()+'/categories';
    jQuery.getJSON(url,function(data){
        showCategories(data);
    });
    
    /**
     * Currencies
     **/
    var url = 'https://api.mercadolibre.com/sites/'+meli_site.val();
    jQuery.getJSON(url,function(data){
        showCurrency(data);
    });
});

/***********************************************************************
 * DEFAULT EXECUTION
 * ********************************************************************/

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/**
 * Trigger Site Change
 * to load default value
 * */
jQuery('#meli-item-site').change();

function meliSetupOnLoad() {
    /**
     * Show tinyMCE
     * */
    tinySetup({
        editor_selector :"meli_rte",
        setup: function(ed){
            ed.on('change',function(){
                
                /**
                 * Adding description to meli data
                 * */
                ed.save();
                meli_data.description = parseDefaultMeliDataValue(jQuery('#meli-item-description').val());
                setMeliJSON();
                
            });
        }
    });
    if (jQuery('#meli-item-description').val()) {
        meli_data.description = parseDefaultMeliDataValue(jQuery('#meli-item-description').val());
    }
    if (jQuery('#meli-item-title').val()) {
        meli_data.title = jQuery('#meli-item-title').val().replace('"','&quot;');
    }
    if (jQuery('#meli-item-warranty').val()) {
        meli_data.warranty = jQuery('#meli-item-warranty').val().replace('"','&quot;');
    }
    setMeliJSON();
    
    /**
     * IF meli item exists
     * */
    
    if (meli_data.id) {
        
        /**
         * Show "after category" container
         * */
        jQuery('#meli-data-after-category').show();
        
        if (meli_data.buying_mode == 'classified') {
            classifiedsAction();
        }
        
        if (meli_data.status == 'closed') {
            /**
             * Getting Listing types
             * */
            jQuery.getJSON('https://api.mercadolibre.com/categories/'+meli_data.category_id+'/listing_types',function(data){
                showListingTypes(data);
            });
        }
        
        /**
         * Set Attributes/Variations if applies
         * */
        if (meli_data.category_id) {
            jQuery.getJSON('https://api.mercadolibre.com/categories/'+meli_data.category_id, function(data){
                if (data.attribute_types == 'attributes') {
                    delete meli_data.automatic_relist;
                    setMeliJSON();
                } else {
                    getAttributes(meli_data.category_id);
                }
            });
        }
        
        /**
         * Currencies
         **/
        var url = 'https://api.mercadolibre.com/sites/'+meli_data.site_id;
            jQuery.getJSON(url,function(data){
                showCurrency(data);
            });
        }
}

jQuery(document).ready(function(){
    if (typeof(tinyMCE) == 'undefined') {
        var checkinterval = setInterval(function(){
            if (typeof(tinyMCE) != 'undefined') {
                meliSetupOnLoad();
                clearInterval(checkinterval);
            }
        },2000);
    } else {
        jQuery('#meli-data-after-category').livequery(function(){
            meliSetupOnLoad();
        });
    }
    /**
     * Hide automatic relist option
     * */
    if (meli_data.buying_mode == 'auction' || meli_data.listing_type_id == 'free') {
        jQuery('#meli-item-automatic-relist-container').hide();
    } else {
        jQuery('#meli-item-automatic-relist-container').show();
    }
});
/***********************************************************************
 * ********************************************************************/
