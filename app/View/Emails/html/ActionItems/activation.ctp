<span><strong>Primary Information</strong></span>
<p>CLIENT NAME: <?php echo $lead_name;?></p>
<p>PHONE NO.: <?php echo $lead_primaryphonenumber;?></p>
<p>EMAIL ID: <?php echo $lead_emailid;?></p>
<p>TYPE: <?php echo $Type;?></p>
<p>BUDGET: <?php echo $Budget;?></p>
<p>CLOSURPROBABILITY: <?php echo $ClosurProbability;?></p>
<p>&nbsp;</p>
<span><strong>Client Preferences</strong></span>
<p>SUBURB: <?php echo $SuburbPref1; if($SuburbPref2<>'') echo ' | '.$SuburbPref2;if($SuburbPref3<>'') echo ' | '.$SuburbPref3;?></p>
<p>AREA: <?php echo $AreaPref1;if($AreaPref2<>'') echo ' | '.$AreaPref2;if($AreaPref3<>'') echo ' | '.$AreaPref3;?></p>
<p>BUILDER: <?php echo $BuilderPref1;if($BuilderPref2<>'') echo ' | '.$BuilderPref2;if($BuilderPref3<>'') echo ' | '.$BuilderPref3;?></p>
<p>PROJECT: <?php echo $ProjectPref1;if($ProjectPref2<>'') echo ' | '.$ProjectPref2;if($ProjectPref3<>'') echo ' | '.$ProjectPref3;?></p>
<p>UNIT: <?php echo $UnitPref1;if($UnitPref2<>'') echo ' | '.$UnitPref2;if($UnitPref3<>'') echo ' | '.$UnitPref3;?></p>
<p>PROJECT TYPE: <?php echo $ProjectTypepPref1;if($ProjectTypepPref2<>'') echo ' | '.$ProjectTypepPref2;if($ProjectTypepPref3<>'') echo ' | '.$ProjectTypepPref3;?></p>
<p>&nbsp;</p>
<span><strong><?php echo $client_call?></strong></span>
<p>START DATE: <?php echo $StartDate;?></p>
<p>END DATE.: <?php echo $EndDate;?></p>
<p>QUALITY: <?php echo $Quality;?></p>
<?php if($client_info == 'OLD'){?>
<p>DETAILS: <?php echo $Details;?></p>
<?php }else{?>
<p>Witnin 15 Minutes? <?php if($CallDuration == '1') echo 'YES'; else echo 'NO';?></p>
<?php }?>
<p>REMARK: <?php echo $EventDescription;?></p>



