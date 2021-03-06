<?php
$this->Html->addCrumb('View / Edit Base', 'javascript:void(0);', array('class' => 'breadcrumblast'));
//pr($this->data);
?>

<div class="col-sm-12" id="mycl-det">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">View / Edit Information</h4>         
        </div>

        <div class="panel-body">
            <fieldset>
                <legend><span>View / Edit Base</span></legend>
            </fieldset>
            <div class="row">
                <div class="col-sm-12">
                    <?php
                    echo $this->Form->create('DigBase', array('method' => 'post', 'id' => 'parsley_reg', 'class' => 'form-horizontal user_form', 'novalidate' => true,
                        'inputDefaults' => array(
                            'label' => false,
                            'div' => false,
                            'class' => 'form-control',
                        )
                    ));
            
                    ?>
                    <div class="col-sm-6"> 
                           
                        <div class="form-group">
                            <label for="reg_input_name" class="req">Base Website</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBase']['base_website_url']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_website_url', array('data-required' => 'true', 'tabindex' => '1')); ?>
                                </div>
                            </div>
                        </div>
                         <div class="form-group">
                            <label for="reg_input_name" class="req">Target Geography</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBaseTargetGeography']['value']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_target_geography', array('options' => $DigBaseTargetGeographies, 'empty' => '--Select--', 'data-required' => 'true', 'tabindex' => '3')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reg_input_name" class="req">Used By</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php if($this->data['DigBase']['base_used_by'] > 1) echo $this->data['DigBaseUsedBy']['channel_name']; else echo $this->data['DigBase']['base_used_by']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_used_by', array('data-required' => 'true','options' =>$DigBaseUsedBies, 'empty' => '--Select--', 'tabindex' => '5')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reg_input_name" class="req">Base PR</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBase']['base_pr']; ?></p>
                                <div class="hidden_control">
<?php  echo $this->Form->input('base_pr', array('type' => 'text', 'tabindex' => '7')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reg_input_name" class="req">Base DA</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBase']['base_da']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_da', array('type' => 'text', 'tabindex' => '9')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reg_input_name" class="req">Auto Approved?</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['BaseAutoApprove']['value']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_auto_approved', array('options' =>$DigBaseDaAaClLrLookups, 'empty' => '--Select--', 'tabindex' => '11')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reg_input_name" class="req">Login Required?</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['BaseLoginRequired']['value']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_login_required', array('options' =>$DigBaseDaAaClLrLookups, 'empty' => '--Select--', 'tabindex' => '13')); ?>
                                </div>
                            </div>
                        </div>
                         <div class="form-group">
                            <label for="reg_input_name" class="req">Base Usage Status</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBaseUsageStatus']['value']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_usage_status', array('options' => $DigBaseUsages,'empty' => '--Select--', 'tabindex' => '15')); ?>
                                </div>
                            </div>
                        </div>
                       
                        
                    </div>  
                    <div class="col-sm-6">
                       
                        <div class="form-group">
                            <label for="reg_input_name" class="req">Base Primary Type</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['BaseType']['value']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_type', array('options' => $DigBaseTypes, 'empty' => '--Select--','data-required' => 'true', 'tabindex' => '2')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reg_input_name" class="req">Usage Type</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBaseUsageType']['value']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_usage', array('options' => $DigBaseUsageTypes, 'empty' => '--Select--','data-required' => 'true', 'tabindex' => '4')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reg_input_name" class="req">Base Why</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBase']['base_why']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_why', array('options' => $DigBaseWhies,'empty' => '--Select--','data-required' => 'true', 'tabindex' => '6')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="reg_input_name" class="req">Base DF</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBaseDofollow']['value']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_dofollow', array('options' => $DigBaseDofollows, 'empty' => '--Select--', 'tabindex' => '8')); ?>
                                </div>
                            </div>
                        </div>
                         <div class="form-group">
                            <label for="reg_input_name" class="req">Base PA</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBase']['base_pa']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_pa', array('type' => 'text', 'tabindex' => '10')); ?>
                                </div>
                            </div>
                        </div> <div class="form-group">
                            <label for="reg_input_name" class="req">Profile Equal To Public?</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['BasePP']['value']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_pp',array('options' => $DigBaseDaAaClLrLookups, 'empty' => '--Select--', 'tabindex' => '12')); ?>
                                </div>
                            </div>
                        </div>
                         <div class="form-group">
                            <label for="reg_input_name" class="req">Link Within Text?</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['BaseWithinComment']['value']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_link_within_comment',array('options' => $DigBaseDaAaClLrLookups, 'empty' => '--Select--', 'tabindex' => '14')); ?>
                                </div>
                            </div>
                        </div>
                         <div class="form-group">
                            <label for="reg_input_name" class="req">Base Active</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBase']['active']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('active', array('options' => array('TRUE' => 'TRUE','FALSE' => 'FALSE'),'empty' => '--Select--', 'tabindex' => '16')); ?>
                                </div>
                            </div>
                        </div>
                       
                      
                
                   
                    </div>
                    
                    <div class="form-group">
                            <label for="reg_input_name" class="req">Link 2</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['BaseUsedAs1']['value'].' , '.$this->data['DigBaseLinkCategory1']['value'].' , '.$this->data['BaseLink1Dofollow']['value']; ?></p>
                                <div class="hidden_control">

                                          
                                                <?php echo $this->Form->input('base_used_as1', array('options' =>$DigBaseTypes, 'empty' => '--Used As--', 'tabindex' => '17')); ?>
                                                
                                         
                                          
                                                <?php echo $this->Form->input('base_link1_category', array('options' =>$DigBaseLinkCategories, 'empty' => '--Link Category--', 'tabindex' => '18')); ?>
                                                
                                           
                                           
                                                <?php echo $this->Form->input('base_link1_dofollow', array('options' =>$DigBaseDaAaClLrLookups, 'empty' => '--Link Dofollow--', 'tabindex' => '19')); ?>
                                            

                                        
                                </div>
                            </div>
                        </div>
                    <div class="form-group">
                            <label for="reg_input_name" class="req">Link 3</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['BaseUsedAs2']['value'].' , '.$this->data['DigBaseLinkCategory2']['value'].' , '.$this->data['BaseLink2Dofollow']['value']; ?></p>
                                <div class="hidden_control">

                                         
                                                <?php echo $this->Form->input('base_used_as2', array('options' =>$DigBaseTypes, 'empty' => '--Used As--', 'tabindex' => '20')); ?>
                                                
                                         
                                         
                                                <?php echo $this->Form->input('base_link2_category', array('options' =>$DigBaseLinkCategories, 'empty' => '--Link Category--', 'tabindex' => '21')); ?>
                                                
                                          
                                           
                                                <?php echo $this->Form->input('base_link2_dofollow', array('options' =>$DigBaseDaAaClLrLookups, 'empty' => '--Link Dofollow--', 'tabindex' => '22')); ?>
                                           

                                      
                                </div>
                            </div>
                        </div>
                    <div class="form-group">
                            <label for="reg_input_name" class="req">Link 4</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['BaseUsedAs3']['value'].' , '.$this->data['DigBaseLinkCategory3']['value'].' , '.$this->data['BaseLink3Dofollow']['value']; ?></p>
                                <div class="hidden_control">

                                           
                                                <?php echo $this->Form->input('base_used_as3', array('options' =>$DigBaseTypes, 'empty' => '--Used As--', 'tabindex' => '23')); ?>
                                                
                                         
                                         
                                                <?php echo $this->Form->input('base_link3_category', array('options' =>$DigBaseLinkCategories, 'empty' => '--Link Category--', 'tabindex' => '24')); ?>
                                                
                                        
                                      
                                                <?php echo $this->Form->input('base_link3_dofollow', array('options' =>$DigBaseDaAaClLrLookups, 'empty' => '--Link Dofollow--', 'tabindex' => '25')); ?>
                                     

                                        
                                </div>
                            </div>
                        </div>
                    <div class="form-group">
                            <label for="reg_input_name" class="req">Usage Instructions</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBase']['base_usage_instructions']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_usage_instructions', array('type' => 'textarea', 'style' => 'width:122%;height:100px', 'tabindex' => '26')); ?>
                                </div>
                            </div>
                        </div>
                  <div class="form-group">
                            <label for="reg_input_name" class="req">Base Comment</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBase']['base_comment']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_comment', array('type' => 'textarea', 'style' => 'width:122%;height:100px', 'tabindex' => '27')); ?>
                                </div>
                            </div>
                        </div>
                    <div class="form-group">
                            <label for="reg_input_name" class="req">Base Description</label>
                            <span class="colon">:</span>
                            <div class="col-sm-10 editable">
                                <p class="form-control-static"><?php echo $this->data['DigBase']['base_description']; ?></p>
                                <div class="hidden_control">
<?php echo $this->Form->input('base_description', array('type' => 'textarea', 'style' => 'width:122%;height:100px', 'tabindex' => '28')); ?>
                                </div>
                            </div>
                        </div>
                   
                  
                
                    <div style ="clear:both"></div>
                    <div class="form_submit clearfix" style="display:none">
                        <div class="row">
                            <div class="col-sm-1">
<?php
echo $this->Form->submit('Update', array('name' => 'add', 'class' => 'btn btn-success sticky_success', 'value' => 'add'));
?>
                            </div>
                            <div class="col-sm-1">
<?php echo $this->Form->button('Reset', array('type' => 'reset', 'class' => 'btn btn-danger sticky_important')); ?>
                            </div>


                        </div>

                    </div>
                    </div> 
<?php 
echo $this->Form->end(); 
?>
                
                   
                    
            </div>
        </div>
    </div>
</div>


