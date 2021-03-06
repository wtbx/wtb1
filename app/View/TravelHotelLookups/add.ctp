<?php
$this->Html->addCrumb('Add Hotel', 'javascript:void(0);', array('class' => 'breadcrumblast'));
?>

<div class="col-sm-12" id="mycl-det">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">Add Information</h4>
        </div>
        <div class="panel-body">
            <fieldset>
                <legend><span>Add Hotel</span></legend>
            </fieldset>
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    echo $this->Form->create('TravelHotelLookup', array('enctype' => 'multipart/form-data', 'method' => 'post', 'id' => 'wizard_a', 'novalidate' => true,
                        'inputDefaults' => array(
                            'label' => false,
                            'div' => false,
                            'class' => 'form-control',
                        )
                    ));
                    echo $this->Form->hidden('continent_name');
                    echo $this->Form->hidden('country_name');
                    echo $this->Form->hidden('city_name');
                    echo $this->Form->hidden('suburb_name');
                    echo $this->Form->hidden('area_name');
                    echo $this->Form->hidden('chain_name');
                    echo $this->Form->hidden('brand_name');
                    ?>

                    <h4>Primary Information</h4>
                    <fieldset class="nopdng">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <h4>Hotel Basic Information</h4>
                                    <div class="form-group">
                                        <label for="input_name" class="req">Hotel Name</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('hotel_name', array('data-required' => 'true'));
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name" class="req">Continent</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('continent_id', array('options' => $TravelLookupContinents, 'empty' => '--Select--', 'data-required' => 'true'));
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name" class="req">City</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('city_id', array('options' => array(), 'empty' => '--Select--', 'data-required' => 'true'));
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name" class="req">Area</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('area_id', array('options' => array(), 'empty' => '--Select--', 'data-required' => 'true'));
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name" class="req">Brand</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('brand_id', array('options' => array(), 'empty' => '--Select--', 'data-required' => 'true'));
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name">Rate Type </label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('rate_type', array('options' => $TravelLookupRateTypes, 'empty' => '--Select--','disabled'));
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="input_name">Hotel Url</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('url_hotel', array());
                                            ?></div>
                                    </div> 
                                    <div class="form-group">
                                        <label for="input_name">Post Code</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('post_code', array());
                                            ?></div>
                                    </div> 
                                    <div class="form-group">
                                        <label for="input_name">Hotel Group</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('hotel_group', array());
                                            ?></div>
                                    </div>                                  


                                </div>




                                <div class="col-sm-6">
                                    <h4>&nbsp;</h4>
                                    <div class="form-group">
                                        <label for="input_name" class="req">Hotel Code</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('hotel_code');
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name" class="req">Country</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('country_id', array('options' => array(), 'empty' => '--Select--', 'data-required' => 'true'));
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name" class="req">Suburb</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('suburb_id', array('options' => array(), 'empty' => '--Select--', 'data-required' => 'true'));
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name" class="req">Chain</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('chain_id', array('options' => $TravelChains, 'empty' => '--Select--', 'data-required' => 'true'));
                                            ?></div>
                                    </div> 
                                    <div class="form-group">
                                        <label for="reg_input_name" class="req">Property Type</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('property_type', array('options' => $TravelLookupPropertyTypes, 'empty' => '--Select--', 'data-required' => 'true'));
                                            ?></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="reg_input_name">Star</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            $options = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7');
                                            $attributes = array('legend' => false, 'hiddenField' => false, 'label' => false, 'div' => false, 'class' => 'attrInputs');
                                            echo $this->Form->radio('star', $options, $attributes);
                                            //  echo $this->Form->input('star');
                                            ?></div>
                                    </div>
                                    <div class="form-group int-sm">
                                        <label>Location</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">  <?php
                                            echo $this->Form->input('gps_prm_1', array('class' => 'form-control decimal','placeholder' => 'GPS Parameter 1','style' => 'width:47%'));
                                            echo $this->Form->input('gps_prm_2', array('class' => 'form-control decimal','placeholder' => 'GPS Parameter 2','style' => 'width:47%'));
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="input_name">No. Of Rooms</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('no_room', array());
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="input_name">Top Hotel</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            $options = array('TRUE' => 'True', 'FALSE' => 'False');
                                            $attributes = array('legend' => false, 'hiddenField' => false, 'label' => false, 'div' => false, 'class' => 'attrInputs');
                                            echo $this->Form->radio('top_hotel', $options, $attributes);
                                            ?>
                                        </div>
                                    </div>                                                             

                                </div>
                                <br class="spacer" />
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Address</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10 editable txtbox">
                                            <?php
                                            echo $this->Form->input('address', array('type' => 'textarea'));
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Description</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10 editable txtbox">
                                            <?php
                                            echo $this->Form->input('hotel_comment', array('type' => 'textarea','style' => 'width:100%;height:100px'));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <h4>Hotel Details</h4>
                    <fieldset class="nopdng">
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>Facilities</h4>
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Business Center</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            $options = array('Y' => 'Yes', 'N' => 'No');
                                            $attributes = array('legend' => false, 'hiddenField' => false, 'label' => false, 'div' => false, 'class' => 'attrInputs', 'default' => 'N');
                                            echo $this->Form->radio('business_center', $options, $attributes);
                                            ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name">Dining</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php echo $this->Form->radio('dining_facilities', $options, $attributes); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name">Bar</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php echo $this->Form->radio('bar_lounge', $options, $attributes); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name">Fitness</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php echo $this->Form->radio('fitness_center', $options, $attributes); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name">Kids</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php echo $this->Form->radio('kids', $options, $attributes); ?></div>
                                    </div>


                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="reg_input_name">Meeting</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php echo $this->Form->radio('meeting_facilities', $options, $attributes); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name">Pool</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php echo $this->Form->radio('pool', $options, $attributes); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name">Golf</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php echo $this->Form->radio('golf', $options, $attributes); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name">Tennis</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php echo $this->Form->radio('tennis', $options, $attributes); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="reg_input_name">Handicap</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php echo $this->Form->radio('handicap', $options, $attributes); ?></div>
                                    </div>




                                </div>
                                <div style="clear: both"></div>
                                <h4>Ratings</h4>
                                <div class="col-sm-6">

                                    <div class="form-group ">
                                        <label>Standard Rating</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                           
                                            $options = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7');
                                            $attributes = array('legend' => false, 'hiddenField' => false, 'label' => false, 'div' => false, 'class' => 'attrInputs');
                                            echo $this->Form->radio('standard_rating', $options, $attributes);
                                            //  echo $this->Form->input('star');
                                           
                                            //echo $this->Form->input('standard_rating');
                                            ?></div>
                                    </div>
                                    <div class="form-group ">
                                        <label>Food Rating</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            $options = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7');
                                            $attributes = array('legend' => false, 'hiddenField' => false, 'label' => false, 'div' => false, 'class' => 'attrInputs');
                                            echo $this->Form->radio('food_rating', $options, $attributes);
                                           // echo $this->Form->input('food_rating');
                                            ?></div>
                                    </div>

                                    <div class="form-group">
                                        <label>Location Rating</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php 
                                         $options = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7');
                                            $attributes = array('legend' => false, 'hiddenField' => false, 'label' => false, 'div' => false, 'class' => 'attrInputs');
                                            echo $this->Form->radio('location_rating', $options, $attributes);
                                        //echo $this->Form->input('location_rating'); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label>Overall Rating</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php 
                                         $options = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7');
                                            $attributes = array('legend' => false, 'hiddenField' => false, 'label' => false, 'div' => false, 'class' => 'attrInputs');
                                            echo $this->Form->radio('overall_rating', $options, $attributes);
                                        //echo $this->Form->input('overall_rating'); ?></div>
                                    </div>

                                </div>
                                <div class="col-sm-6">

                                    <div class="form-group">
                                        <label>Hotel Rating</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php 
                                         $options = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7');
                                            $attributes = array('legend' => false, 'hiddenField' => false, 'label' => false, 'div' => false, 'class' => 'attrInputs');
                                            echo $this->Form->radio('hotel_rating', $options, $attributes);
                                        //echo $this->Form->input('hotel_rating'); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label>Service Rating</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php 
                                         $options = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7');
                                            $attributes = array('legend' => false, 'hiddenField' => false, 'label' => false, 'div' => false, 'class' => 'attrInputs');
                                            echo $this->Form->radio('service_rating', $options, $attributes);
                                        //echo $this->Form->input('service_rating'); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label>Value Rating</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10"> <?php 
                                         $options = array('1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7');
                                            $attributes = array('legend' => false, 'hiddenField' => false, 'label' => false, 'div' => false, 'class' => 'attrInputs');
                                            echo $this->Form->radio('value_rating', $options, $attributes);
                                        //echo $this->Form->input('value_rating'); ?></div>
                                    </div>

                                </div>



                                <div style="clear: both;"></div>
                                <h4>Hotel Contacts</h4>
                                <div class="col-sm-6">

                                    <div class="form-group ">
                                        <label>Reservation Name</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('reservation_contact');
                                            ?></div>
                                    </div>
                                    <div class="form-group ">
                                        <label for="reg_input_name">Reservation Email</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('reservation_email');
                                            ?>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <label>Emergency No.</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('emergency_contact_number');
                                            ?></div>
                                    </div>
                                </div>

                                <div class="col-sm-6">

                                    <div class="form-group ">
                                        <label>Reservation Desk No.</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('reservation_desk_number');
                                            ?></div>
                                    </div>
                                    <div class="form-group ">
                                        <label>Emergency Name</label>
                                        <span class="colon">:</span>
                                        <div class="col-sm-10">
                                            <?php
                                            echo $this->Form->input('emergency_contact_name');
                                            ?></div>
                                    </div>


                                </div>

                                <div style="clear: both;"></div>
                                <h4>Hotel Pictures</h4>       


                                <div class="row" id="fr-select">
                                    <div class="col-sm-6 uploadfile">
                                        <div class="form-group">
                                            <div class="col-sm-10 editable txtbox">
                                                <label>Picture1</label>
                                                <span class="colon">:</span>
                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="fileupload-new img-thumbnail" style="width: 178px; height: 120px;">
                                                        <?php
                                                       
                                                            $image1 = $this->webroot . "img/no_img_180.png";
                                                       
                                                        ?>
                                                        <img src="<?php echo $image1; ?>" height="200" width="170" />

                                                    </div>
                                                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="width: 178px; height: 120px"></div>
                                                    <div>
                                                        <span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span>
                                                            <input type="file" name="data[TravelHotelLookup][hotel_img1]" />

                                                        </span>
                                                        <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-10 editable txtbox">
                                                <label>Picture3</label>
                                                <span class="colon">:</span>
                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="fileupload-new img-thumbnail" style="width: 178px; height: 120px;">
                                                        <?php
                                                        
                                                            $image3 = $this->webroot . "img/no_img_180.png";
                                                       
                                                        ?>
                                                        <img src="<?php echo $image3; ?>" height="200" width="170" />

                                                    </div>
                                                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="width: 178px; height: 120px"></div>
                                                    <div>
                                                        <span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span>
                                                            <input type="file" name="data[TravelHotelLookup][hotel_img3]" />

                                                        </span>
                                                        <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-10 editable txtbox">
                                                <label>Picture5</label>
                                                <span class="colon">:</span>
                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="fileupload-new img-thumbnail" style="width: 178px; height: 120px;">
                                                        <?php
                                                       
                                                            $image5 = $this->webroot . "img/no_img_180.png";
                                                        
                                                        ?>
                                                        <img src="<?php echo $image5; ?>" height="200" width="170" />

                                                    </div>
                                                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="width: 178px; height: 120px"></div>
                                                    <div>
                                                        <span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span>
                                                            <input type="file" name="data[TravelHotelLookup][hotel_img5]" />

                                                        </span>
                                                        <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h4>Hotel Logo</h4>

                                        <div class="form-group">
                                            <div class="col-sm-10 editable txtbox">
                                                <label>Logo1</label>
                                                <span class="colon">:</span>
                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="fileupload-new img-thumbnail" style="width: 178px; height: 120px;">

<?php

    $logo = $this->webroot . "img/no_img_180.png";

?>
                                                        <img src="<?php echo $logo; ?>" height="200" width="170" /></div>
                                                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="width: 178px; height: 120px"></div>
                                                    <div>
                                                        <span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span>
                                                            <input type="file" name="data[TravelHotelLookup][logo]" />

                                                        </span>
                                                        <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                    </div>

                                    <div class="col-sm-6 uploadfile">
                                        <div class="form-group">
                                            <div class="col-sm-10 editable txtbox">
                                                <label>Picture2</label>
                                                <span class="colon">:</span>
                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="fileupload-new img-thumbnail" style="width: 178px; height: 120px;">

<?php

    $image2 = $this->webroot . "img/no_img_180.png";

?>
                                                        <img src="<?php echo $image2; ?>" height="200" width="170" /></div>
                                                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="width: 178px; height: 120px">


                                                    </div>
                                                    <div>
                                                        <span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span>
                                                            <input type="file" name="data[TravelHotelLookup][hotel_img2]" />

                                                        </span>
                                                        <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-10 editable txtbox">
                                                <label>Picture4</label>
                                                <span class="colon">:</span>
                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="fileupload-new img-thumbnail" style="width: 178px; height: 120px;">
                                                        <?php
                                                       
                                                            $image4 = $this->webroot . "img/no_img_180.png";
                                                        
                                                        ?>
                                                        <img src="<?php echo $image4; ?>" height="200" width="170" />

                                                    </div>
                                                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="width: 178px; height: 120px"></div>
                                                    <div>
                                                        <span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span>
                                                            <input type="file" name="data[TravelHotelLookup][hotel_img4]" />

                                                        </span>
                                                        <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-10 editable txtbox">
                                                <label>Picture6</label>
                                                <span class="colon">:</span>
                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="fileupload-new img-thumbnail" style="width: 178px; height: 120px;">
                                                        <?php
                                                        
                                                            $image6 = $this->webroot . "img/no_img_180.png";
                                                        
                                                        ?>
                                                        <img src="<?php echo $image6; ?>" height="200" width="170" />

                                                    </div>
                                                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="width: 178px; height: 120px"></div>
                                                    <div>
                                                        <span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span>
                                                            <input type="file" name="data[TravelHotelLookup][hotel_img6]" />

                                                        </span>
                                                        <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-10 editable txtbox">
                                                <label>Logo2</label>
                                                <span class="colon">:</span>
                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                    <div class="fileupload-new img-thumbnail" style="width: 178px; height: 120px;">
<?php

    $logo1 = $this->webroot . "img/no_img_180.png";

?>
                                                        <img src="<?php echo $logo1; ?>" height="200" width="170" />
                                                    </div>
                                                    <div class="fileupload-preview fileupload-exists img-thumbnail" style="width: 178px; height: 120px"></div>
                                                    <div>
                                                        <span class="btn btn-default btn-file"><span class="fileupload-new">Select image</span><span class="fileupload-exists">Change</span>
                                                            <input type="file" name="data[TravelHotelLookup][logo1]" />

                                                        </span>
                                                        <a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>


                            </div>
                        </div>
                    </fieldset>
<?php echo $this->Form->end(); ?>
                </div>
            </div>

        </div>
    </div>
</div>


<?php
$data = $this->Js->get('#wizard_a')->serializeForm(array('isForm' => true, 'inline' => true));

$this->Js->get('#TravelHotelLookupContinentId')->event('change', $this->Js->request(array(
            'controller' => 'all_functions',
            'action' => 'get_travel_country_by_continent_id/TravelHotelLookup/continent_id'
                ), array(
            'update' => '#TravelHotelLookupCountryId',
            'async' => true,
            'before' => 'loading("TravelHotelLookupCountryId")',
            'complete' => 'loaded("TravelHotelLookupCountryId")',
            'method' => 'post',
            'dataExpression' => true,
            'data' => $data
        ))
);

$this->Js->get('#TravelHotelLookupCountryId')->event('change', $this->Js->request(array(
            'controller' => 'all_functions',
            'action' => 'get_travel_city1_by_country_id/TravelHotelLookup/continent_id/country_id'
                ), array(
            'update' => '#TravelHotelLookupCityId',
            'async' => true,
            'before' => 'loading("TravelHotelLookupCityId")',
            'complete' => 'loaded("TravelHotelLookupCityId")',
            'method' => 'post',
            'dataExpression' => true,
            'data' => $data
        ))
);

$this->Js->get('#TravelHotelLookupCityId')->event('change', $this->Js->request(array(
            'controller' => 'all_functions',
            'action' => 'get_travel_suburb_by_country_id_and_city_id/TravelHotelLookup/country_id/city_id'
                ), array(
            'update' => '#TravelHotelLookupSuburbId',
            'async' => true,
            'before' => 'loading("TravelHotelLookupSuburbId")',
            'complete' => 'loaded("TravelHotelLookupSuburbId")',
            'method' => 'post',
            'dataExpression' => true,
            'data' => $data
        ))
);

$this->Js->get('#TravelHotelLookupSuburbId')->event('change', $this->Js->request(array(
            'controller' => 'all_functions',
            'action' => 'get_travel_area_by_suburb_id/TravelHotelLookup/suburb_id'
                ), array(
            'update' => '#TravelHotelLookupAreaId',
            'async' => true,
            'before' => 'loading("TravelHotelLookupAreaId")',
            'complete' => 'loaded("TravelHotelLookupAreaId")',
            'method' => 'post',
            'dataExpression' => true,
            'data' => $data
        ))
);

$this->Js->get('#TravelHotelLookupChainId')->event('change', $this->Js->request(array(
            'controller' => 'all_functions',
            'action' => 'get_travel_brand_by_chain_id/TravelHotelLookup/chain_id'
                ), array(
            'update' => '#TravelHotelLookupBrandId',
            'async' => true,
            'before' => 'loading("TravelHotelLookupBrandId")',
            'complete' => 'loaded("TravelHotelLookupBrandId")',
            'method' => 'post',
            'dataExpression' => true,
            'data' => $data
        ))
);
?>
<script>
    $(document).ready(function() {
        $('#TravelHotelLookupContinentId').change(function() {
            $('#TravelHotelLookupContinentName').val($('#TravelHotelLookupContinentId option:selected').text());
        });
        
        $('#TravelHotelLookupCountryId').change(function() {
            $('#TravelHotelLookupCountryName').val($('#TravelHotelLookupCountryId option:selected').text());
        });
        
        $('#TravelHotelLookupCityId').change(function() {
            $('#TravelHotelLookupCityName').val($('#TravelHotelLookupCityId option:selected').text());
        });
        
        $('#TravelHotelLookupSuburbId').change(function() {
            $('#TravelHotelLookupSuburbName').val($('#TravelHotelLookupSuburbId option:selected').text());
        });
        
        $('#TravelHotelLookupAreaId').change(function() {
            $('#TravelHotelLookupAreaName').val($('#TravelHotelLookupAreaId option:selected').text());
        });
        
        $('#TravelHotelLookupChainId').change(function() {
            $('#TravelHotelLookupChainName').val($('#TravelHotelLookupChainId option:selected').text());
        });
        
        $('#TravelHotelLookupBrandId').change(function() {
            $('#TravelHotelLookupBrandName').val($('#TravelHotelLookupBrandId option:selected').text());
        });
        
        $('.decimal').change(function(event) {        
        this.value = parseFloat(this.value).toFixed(7);
       
    })
    
  
    });

</script>    

